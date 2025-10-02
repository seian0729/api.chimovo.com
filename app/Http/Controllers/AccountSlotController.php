<?php

namespace App\Http\Controllers;

use App\Models\AccountSlot;
use App\Models\Account;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountSlotController extends Controller
{

    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }
    
    public function getTotalSlot(Request $request){
        $token = $request->header('Authorization');

        //
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        $decoded_array = (array)$decoded;
        $user = $decoded_array['user'];
        $user = (array)$user;
        $id = $user['id'];
        $Account = new Account();
        $account = $Account->where('UID', $id)->first();
        if ($account) {
            $accountSlot = new AccountSlot();
            $activeSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->first();
            if ($activeSlot){
                $totalSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
                return response()->json(['success' => ['data' => ['slots' => $totalSlot]]], 200);
            }
            else{
                return response()->json(['success' => ['data' => ['slots' => 0]]], 200);
            }

        }
        else{
            return response()->json(['error' => ['messages' => 'Account not found']], 401);
        }
    }
    
    public function getSlots(Request $request){
        $token = $request->header('Authorization');

        //
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        $decoded_array = (array)$decoded;
        $user = $decoded_array['user'];
        $user = (array)$user;
        $id = $user['id'];
        $Account = new Account();
        $account = $Account->where('UID', $id)->first();
        if ($account) {
            $accountSlot = new AccountSlot();
            $accountSlot-where('UID',$id)->get();
            return response()->json(['success' => ['data' => $accountSlot]], 200);
        }
        else{
            return response()->json(['error' => ['messages' => 'Account not found']], 401);
        }
    }


}