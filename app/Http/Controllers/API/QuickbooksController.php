<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use QuickBooksOnline\API\DataService\DataService;
use Auth;

class QuickbooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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

    /**
     * Initialize Quickbooks for user
     *
     * @return void
     */
    public function init()
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

    /**
     * Get all accounts for this Quickbooks user
     *
     * @param Request $request
     * @return Response
     */
    public function getAccounts(Request $request) // STILL WORKING ON
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
          // Iterate through all Accounts, even if it takes multiple pages
          $i = 1;
          while (1) {
            $allAccounts = $dataService->FindAll('Account', $i, 500);
            $error = $dataService->getLastError();
            if ($error) {
                return response()->json([
                    'statusText' => $error->getResponseBody(),
                ], $error->getHttpStatusCode());
            }

            if (!$allAccounts || (0==count($allAccounts))) {
                break;
            }

            foreach ($allAccounts as $oneAccount) {
                echo "Account[".($i++)."]: {$oneAccount->Name}\n";
                echo "\t * Id: [{$oneAccount->Id}]\n";
                echo "\t * AccountType: [{$oneAccount->AccountType}]\n";
                echo "\t * AccountSubType: [{$oneAccount->AccountSubType}]\n";
                echo "\t * Active: [{$oneAccount->Active}]\n";
                echo "\n";
            }
        }
    }
}
