<?php

namespace App\Http\Controllers;

use App\Models\ClientOder;
use Illuminate\Http\Request;

class ClientServicesController extends Controller
{
    function addoder(Request $request) {
        $client_id = auth()->guard('client')->id();
        $data = $request->all();
        if(ClientOder::where('client_id',$client_id)->where('post_id',$request->post_id)->exists()){
            return response()->json([
                'message' => 'The service was requested by an action'
            ], 200);

        }
        $data['client_id'] = $client_id;
        $order = ClientOder::create($data);
        return response()->json([
            'message' => 'successfully'
        ], 200);
    }



    function userOder () {
        $order = ClientOder::with('post','client')->whereStatus('pending')->whereHas('post', function ($quary){
            $quary->where('user_id',auth()->guard('user')->id());
        })->get();

        return response()->json(
            [
                'oredr' => $order
            ], 200);
    }

    function updateOrder(Request $request) {
        $order = ClientOder::findOrFail($request->post_id);
             $order->setAttribute('status',$request->status)->save();
            //  $order->update('status',$request->status);

             return response()->json(
                [
                    'message' => 'Update Status order'
                ], 200);
    }
}
