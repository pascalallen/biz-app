<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Upload;
use Illuminate\Support\Facades\Storage;
use Zipper;
use Auth;

class FileController extends Controller
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
        $user = Auth::user();
        $company = $user->companies->first();
        // Loop through files in POST request
        foreach($request->files as $key => $value){
            // Check if exists
            $existsInBucket = Storage::disk('s3')
                ->exists("uploads/$company->name/$request->customer_name/$request->invoice_key/$request->file($key)->getClientOriginalName()");
            $existsInDb = Upload::where('invoice_key', $request->invoice_key)
                ->where('customer_key',$request->customer_key)
                ->where('filename',$request->filename)
                ->exists();

            if(!$existsInBucket && !$existsInDb){
                $upload = new Upload();
                $upload->name = 'test name'; // $request->name;
                $upload->description = 'test description'; // $request->description;
                $upload->filename = $request->file($key)->getClientOriginalName();
                $upload->invoice_key = $request->invoice_key;
                $upload->customer_key = $request->customer_key;
                $upload->user_id = $user->id;
                $upload->company_id = $company->id;
                $upload->save();

                $request->file($key)->storeAs("uploads/$company->name/$request->customer_name/$request->invoice_key", $request->file($key)->getClientOriginalName(), 's3');
            }
        }

        return response()->json([
            'statusText' => 'Success',
        ], 200);
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
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function downloadFiles(Request $request)
    {
        // TODO: loop through records in db and grab each file from s3,
        // then zip files in local storage,
        // and delete from local storage after downloaded

        $uploadRecords = Upload::where('invoice_key', $request->invoice_key)
                         ->where('customer_key',$request->customer_key)
                         ->get();

        $files = collect();
        $uploadRecords->map(function($uploadRecord,$i) use ($files) {
            $files->push(Storage::disk('s3')->get($uploadRecord));
        });

    }
}
