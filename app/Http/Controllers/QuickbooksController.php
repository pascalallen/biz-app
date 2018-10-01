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
}
