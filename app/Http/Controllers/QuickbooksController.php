<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\ServiceContext;
use Auth;

class QuickbooksController extends Controller
{
    public function connect()
    {
        // Prep Data Services
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => env('QUICKBOOKS_CLIENT_ID'),
            'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
            'RedirectURI' => env('QUICKBOOKS_REDIRECT_URL'),
            'scope' => "com.intuit.quickbooks.accounting",
            'baseUrl' => "Development" // For production: Production
        ));
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authorizationUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
        header("Location: ".$authorizationUrl); // Quickbooks docs say to use `header()` to redirect
    }

    public function disconnect()
    {
        $user = Auth::user();


    }

    public function getCompanyInfo()
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

        $companyInfo = $dataService->FindAll('CompanyInfo');

        foreach($companyInfo as $info){
            $error = $dataService->getLastError();

            if ($error) {
                return response()->json([
                    'statusText' => $error->getResponseBody(),
                ], $error->getHttpStatusCode());
            }
            $data->push($info);
        }

        return response()->json([
            'data' => $data
        ], 200);
    }
}
