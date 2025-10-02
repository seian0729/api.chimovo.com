<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PetX;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PetxsendController extends Controller
{
    
    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }
    
    // Get Data
    
    public function getData(Request $request)
    {
        //Rquest
        $uid = $request -> input('UID');
        $Account = new Account();
        
        
        $account = $Account->where('UID', $uid)->first();
    
        if ($account) {
            $data = new PetX();
            $data = $data->where('Uid', $uid)->where('Received',0)->get();
    
            return response()->json(['status' => 'success', 'data' => $data]);
        } else {
            return response()->json(['errors' => ['message' => ['Tài khoản không tồn tại']]], 401);
        }
    }
    
    //Update Data
    
    public function updateData(Request $request)
    {
        //Request
        $id = $request -> input('Id');
        $uid = $request -> input('UID');
        $received =  $request -> input('Received');
        $status = $request -> input('Status');
        
        //Check account
        $Account = new Account();
        $account = $Account->where('UID', $uid)->first();
        if (!$account) {
            return response()->json(['errors' => ['message' => ['Tài khoản không tồn tại']]], 401);
        }
        $data = new PetX();
        $data = $data->where('Id', $id)->where('Received',0)->first();
        if($data){
            $data -> update([
                'Received' => $received,
                'Status' => $status,
            ]);
            if ($data) {
                return response()->json(['status' => 'success', 'message' => ['Updated Data']]);
            } else {
                return response()->json(['errors' => ['message' => ['Failed Update']]], 401);
            }
        }
        else{
             return response()->json(['errors' => ['message' => ['Data Not Found', "Id:" => $id]]], 401);
        }
    }
    
    public function createData(Request $request)
    {
        $uid = $request -> input('Uid');
        $UserReceive = $request -> input('UserReceive');
        $Diamonds = $request -> input('Diamonds');
        $Received = 0;
        $Status = json_encode(['Status' => '0', 'messages' => 'Orderd']);
        
        $data = new PetX();
        $data->create([
            'Uid' => $uid,
            'UserReceive' => $UserReceive,
            'Diamonds' => floatval($Diamonds),
            'Received' => $Received,
            'Status' => $Status,
        ]);
        return response()->json(['status' => 'success', 'message' => 'Order Created']);
        
    }
    
    public function getOrder(Request $request){
        
        $token = $request->header('Authorization');
        
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Không có token']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

        if ($decoded) {
            $decoded_array = (array)$decoded;
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $account = $Account->where('UID', $id)->first();
            if ($account) {
                $data = new PetX();
                $data = $data->where('Uid', $id)->get();
        
                return response()->json(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['errors' => ['message' => ['Tài khoản không tồn tại']]], 401);
            }
        }
        else {
            return response()->json(['errors' => ['message' => ['Tài khoản không tồn tại']]], 401);
        }
        
        
    }
    
    public function getRate(){
        $rate = 16;
        return response()->json(['status' => 'success', 'rate' => $rate]);
    }
    
}


?>