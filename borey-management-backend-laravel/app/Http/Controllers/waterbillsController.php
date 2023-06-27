<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Http\Resources\WaterbillsResource;
use App\Models\waterbills;

class waterbillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            
        $userId = auth()->user()->user_id;

        $data = waterbills::where('user_id', $userId)->latest()->get();

        return response()->json([WaterbillsResource::collection($data), 'Programs fetched.']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'category' => 'required',
            'date_payment' => 'required',
            'price' => 'required',
            'payment_status' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());       
        }
        
        $user = auth()->user();
        $username = $user->username;
        $fullname = $user->fullname;
        $userInfo = $user->userInfo; 

        $waterbills = waterbills::create([
            'user_id' => $user->user_id, // Associate the user ID
            'username' => $username,
            'fullname' => $fullname,
            'phonenumber' => $userInfo->phonenumber, // Retrieve the value from the user info
            'house_type' => $userInfo->house_type, // Retrieve the value from the user info
            'house_number' => $userInfo->house_number, // Retrieve the value from the user info
            'street_number' => $userInfo->street_number,
            'category' => $request->category,
            'date_payment' => $request->date_payment,
            'price' => $request->price,
            'payment_status' => $request->payment_status,
        ]);
        
        return response()->json(['Bill created successfully.', new WaterbillsResource($waterbills)]);

        return response()->json(['error' => 'Image not found.'], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $waterbills = waterbills::find($id);
        if (is_null($waterbills)) {
            return response()->json('Bill not found', 404); 
        }

        // Check if the authenticated user is the owner of the form
        $user = auth()->user();
        if ($user->user_id !== $waterbills->user_id) {
            return response()->json('You are not authorized to view this bill', 403);
        }

        return response()->json([new WaterbillsResource($waterbills)]);
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
        // Validating the request data
        $validator = Validator::make($request->all(), [
            'category' => 'required',
            'date_payment' => 'required',
            'price' => 'required',
            'payment_status' => 'required',
        ]);

        // Handling validation errors
        if ($validator->fails()) {
            return response()->json($validator->errors());       
        }

        $user = auth()->user();
        // Retrieve the existing Security bill record
        $waterbills = waterbills::find($id);

        if (!$waterbills) {
            return response()->json('Bill not found', 404);
        }

        // Check if the authenticated user is the owner of the user info
        if ($user->user_id !== $waterbills->user_id) {
            return response()->json('You are not authorized to update this bill', 403);
        }

        // Updating the electric bill form with the request data
        $waterbills->category = $request->category;
        $waterbills->date_payment = $request->date_payment;
        $waterbills->price = $request->price;
        $waterbills->payment_status = $request->payment_status;

        // Saving the updated electric bill form
        $waterbills->save();

        // Returning the response
        return response()->json(['Bill updated successfully.', new WaterbillsResource($waterbills)]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $user = auth()->user();

        $waterbills = waterbills::find($id);

        if ($user->user_id !== $waterbills->user_id) {
        // User is not authorized to delete this form
        return response()->json('You are not authorized to delete this bill', 403);
        }
        $waterbills->delete();

        return response()->json('Bill deleted successfully');
    }

    /**
     * Search user info records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = waterbills::query();

        // Add your search criteria based on your needs
        $query->where('user_id', auth()->user()->user_id)
        ->where(function ($innerQuery) use ($keyword) {
            $innerQuery->where('username', 'like', "%$keyword%")
                ->orWhere('fullname', 'like', "%$keyword%")
                ->orWhere('phonenumber', 'like', "%$keyword%")
                ->orWhere('house_type', 'like', "%$keyword%")
                ->orWhere('house_number', 'like', "%$keyword%")
                ->orWhere('street_number', 'like', "%$keyword%")
                ->orWhere('category', 'like', "%$keyword%")
                ->orWhere('date_payment', 'like', "%$keyword%")
                ->orWhere('price', 'like', "%$keyword%")
                ->orWhere('payment_status', 'like', "%$keyword%");
        });
        $results = $query->get();

        if ($results->isEmpty()) {
            return response()->json('No data found.', 404);
        }

        return response()->json($results);
    }
}