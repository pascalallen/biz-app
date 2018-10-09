<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use App\Company;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if($request->filled(['state', 'code', 'realmId'])){ // if Quickbooks has redirected, store data
            $user->state = $request->state;
            $user->code = $request->code;
            $user->realm_id = $request->realmId;

            $dataService = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => env('QUICKBOOKS_CLIENT_ID'),
                'ClientSecret' => env('QUICKBOOKS_CLIENT_SECRET'),
                'RedirectURI' => env('QUICKBOOKS_REDIRECT_URL'),
                'scope' => "com.intuit.quickbooks.accounting",
                'baseUrl' => "Development" // For production: Production
            ));
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            //It will return something like:https://b200efd8.ngrok.io/OAuth2_c/OAuth_2/OAuth2PHPExample.php?state=RandomState&code=Q0115106996168Bqap6xVrWS65f2iXDpsePOvB99moLCdcUwHq&realmId=193514538214074
            //get the Code and realmID, use for the exchangeAuthorizationCodeForToken
            $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($request->code, $request->realmId);
            $user->access_token = $accessToken->getAccessToken();
            $user->refresh_token = $accessToken->getRefreshToken();

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

            $companyInfo = $dataService->FindAll('CompanyInfo');

            foreach($companyInfo as $info){
                $error = $dataService->getLastError();

                if ($error) {
                    return response()->json([
                        'statusText' => $error->getResponseBody(),
                    ], $error->getHttpStatusCode());
                }

                // Add company
                $company = new Company();
                $company->quickbooks_id = $info->Id;
                $company->name = $info->CompanyName;
                $company->email = $info->CompanyEmailAddr;
                $company->save();
                 // Set relationship to company in company_user
                $user->companies()->attach($company->id);
            }

            $user->save();

            $dataService->updateOAuth2Token($accessToken);
        }
        return view('home');
    }
}
