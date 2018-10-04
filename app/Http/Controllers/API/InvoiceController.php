<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use QuickBooksOnline\API\DataService\DataService;
use Auth;
use App\Upload;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
			if($request->filled('customer')){
				$invoices = $dataService->Query("Select * from Invoice WHERE CustomerRef = '$request->customer'", $i, 1000);
			}else{
				$invoices = $dataService->FindAll('Invoice', $i, 1000);
			}

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
                $invoice->FileCount = Upload::where('invoice_key',$invoice->Id)->where('customer_key', $invoice->CustomerRef)->count();
                $data->push($invoice);
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

        $data->push($invoice);

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
