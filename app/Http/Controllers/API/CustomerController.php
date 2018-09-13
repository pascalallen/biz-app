<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use QuickBooksOnline\API\DataService\DataService;
use Auth;

class CustomerController extends Controller
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
          // Iterate through all Customers, even if it takes multiple pages
          $i = 1;
          while (1) {
            $customers = $dataService->FindAll('Customer', $i, 1000);

            $error = $dataService->getLastError();

            if ($error) {
                return response()->json([
                    'statusText' => $error->getResponseBody(),
                ], $error->getHttpStatusCode());
            }

            if (!$customers || (0==count($customers))) {
                break;
            }

            foreach ($customers as $customer) {
                $i++;

                $data->push($customer);
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


        $customer = $dataService->FindById('customer', $id);

        $error = $dataService->getLastError();

        if ($error) {
            return response()->json([
                'statusText' => $error->getResponseBody(),
            ], $error->getHttpStatusCode());
        }

        if (!$customer) {
            return response()->json([
                'statusText' => 'Customer not found.',
            ], 404);
        }

        $data = collect();

        $data->push($customer);

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
