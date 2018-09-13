<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use QuickBooksOnline\API\DataService\DataService;
use Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if(!$user->access_token){
            return response()->json([
                'statusText' => 'Access token not found.',
            ], 404);
        }

        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'accessTokenKey' =>  $user->access_token,
            'refreshTokenKey' => $user->refresh_token,
            'QBORealmID' => $user->realm_id,
            'baseUrl' => "Development" // For production: Production
          ));

          $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
          $accessToken = $OAuth2LoginHelper->refreshToken();

          $error = $OAuth2LoginHelper->getLastError();

          if ($error) {
              return response()->json([
                'statusText' => $error->getResponseBody(),
            ], $error->getHttpStatusCode());
          }

          $user->access_token = $accessToken->getAccessToken();
          $user->refresh_token = $accessToken->getRefreshToken();
          $user->save();

          $dataService->updateOAuth2Token($accessToken);

          $data = collect();
          // Iterate through all Accounts, even if it takes multiple pages
          $i = 1;
          while (1) {
            $invoices = $dataService->FindAll('Invoice', $i, 1000);

            $error = $dataService->getLastError();

            if ($error) {
                return response()->json([
                    'statusText' => $error->getResponseBody(),
                ], $error->getHttpStatusCode());
            }

            if (!$invoices || (0==count($invoices))) {
                break;
            }

            foreach ($invoices as $invoice) {
                $i++;

                // $lineItems = collect();

                // foreach($invoice->Line as $l){
                //     $lineItems->push([
                //         'description' => $l->Description,
                //         'amount' => $l->Amount,
                //     ]);
                // }

                $data->push([
                    'deposit' => $invoice->Deposit,
                    'customer-ref' => $invoice->CustomerRef,
                    'customer-memo' => $invoice->CustomerMemo,
                    'billing-address' => collect([
                        'line1' => $invoice->BillAddr->Line1,
                        'line2' => $invoice->BillAddr->Line2,
                        'line3' => $invoice->BillAddr->Line3,
                        'line4' => $invoice->BillAddr->Line4,
                        'line5' => $invoice->BillAddr->Line5,
                        'city' => $invoice->BillAddr->City,
                        'country' => $invoice->BillAddr->Country,
                        'postal-code' => $invoice->BillAddr->PostalCode
                    ]),
                    'shipping-address' => $invoice->ShipAddr ? collect([
                        'line1' => $invoice->ShipAddr->Line1,
                        'line2' => $invoice->ShipAddr->Line2,
                        'line3' => $invoice->ShipAddr->Line3,
                        'line4' => $invoice->ShipAddr->Line4,
                        'line5' => $invoice->ShipAddr->Line5,
                        'city' => $invoice->ShipAddr->City,
                        'country' => $invoice->ShipAddr->Country,
                        'postal-code' => $invoice->ShipAddr->PostalCode
                    ]) : null,
                    'due-date' => $invoice->DueDate,
                    'total-amount' => $invoice->TotalAmt,
                    'balance' => $invoice->Balance,
                    'doc-number' => $invoice->DocNumber,
                    'transaction-date' => $invoice->TxnDate,
                    // 'line-items' => $lineItems,
                    'tax' => $invoice->TxnTaxDetail->TotalTax,
                ]);
            }
        }

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();

        if(!$user->access_token){
            return response()->json([
                'statusText' => 'Access token not found.',
            ], 404);
        }

        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'accessTokenKey' =>  $user->access_token,
            'refreshTokenKey' => $user->refresh_token,
            'QBORealmID' => $user->realm_id,
            'baseUrl' => "Development" // For production: Production
        ));

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessToken = $OAuth2LoginHelper->refreshToken();

        $error = $OAuth2LoginHelper->getLastError();

        if ($error) {
            return response()->json([
            'statusText' => $error->getResponseBody(),
        ], $error->getHttpStatusCode());
        }

        $user->access_token = $accessToken->getAccessToken();
        $user->refresh_token = $accessToken->getRefreshToken();
        $user->save();

        $dataService->updateOAuth2Token($accessToken);

        $data = collect();

        $invoice = $dataService->FindById('invoice', $id);

        $error = $dataService->getLastError();

        if ($error) {
            return response()->json([
                'statusText' => $error->getResponseBody(),
            ], $error->getHttpStatusCode());
        }

        if (!$invoice) {
            return response()->json([
                'statusText' => 'Invoice not found.',
            ], 404);
        }

        $data->push([
            'deposit' => $invoice->Deposit,
            'customer-ref' => $invoice->CustomerRef,
            'customer-memo' => $invoice->CustomerMemo,
            'billing-address' => collect([
                'line1' => $invoice->BillAddr->Line1,
                'line2' => $invoice->BillAddr->Line2,
                'line3' => $invoice->BillAddr->Line3,
                'line4' => $invoice->BillAddr->Line4,
                'line5' => $invoice->BillAddr->Line5,
                'city' => $invoice->BillAddr->City,
                'country' => $invoice->BillAddr->Country,
                'postal-code' => $invoice->BillAddr->PostalCode
            ]),
            'shipping-address' => $invoice->ShipAddr ? collect([
                'line1' => $invoice->ShipAddr->Line1,
                'line2' => $invoice->ShipAddr->Line2,
                'line3' => $invoice->ShipAddr->Line3,
                'line4' => $invoice->ShipAddr->Line4,
                'line5' => $invoice->ShipAddr->Line5,
                'city' => $invoice->ShipAddr->City,
                'country' => $invoice->ShipAddr->Country,
                'postal-code' => $invoice->ShipAddr->PostalCode
            ]) : null,
            'due-date' => $invoice->DueDate,
            'total-amount' => $invoice->TotalAmt,
            'balance' => $invoice->Balance,
            'doc-number' => $invoice->DocNumber,
            'transaction-date' => $invoice->TxnDate,
            // 'line-items' => $lineItems,
            'tax' => $invoice->TxnTaxDetail->TotalTax,
        ]);

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
