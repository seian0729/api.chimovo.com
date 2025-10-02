<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Data;
use App\Models\AccountSlot;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }
    
    
    public function getAllUsers(Request $request)
    {
        $token = $request->header('Authorization');
        $perPageRequest = $request->input('perPage');
        $pageRequest = $request->input('page');
        
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
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
            
            $data = new Data();
            $countData = $data->where('UID', $id)->count();
            
            $accountSlot = new AccountSlot();

            if ($account && $account->Role == "Admin" ) {
                $Users = $Account->paginate($perPageRequest,['*'],'page', $pageRequest);
                $newUserOBJ = [];
                foreach ($Users as $user_) {
                    $totalCountData = $data->where('UID', $user_['UID'])->count();
                    $totalData = $data->select('gameId', DB::raw('count(*) as total'))
                                ->where('UID', $user_['UID'])
                                ->groupBy('gameId')
                                ->pluck('total','gameId');
                                
                    $user_['totalData'] +=  $totalCountData or 0;
                    $user_['dataPerGameId'] = $totalData or [];
                    
                    //echo $user;
                    array_push($newUserOBJ,$user_);
                }
            }
            return response()->json(['success' => ['data' =>  $newUserOBJ]], 200);
            
        }
        else{
            return response()->json(['errors' => ['message' => ['Token not found or invalid']]], 401);
        }

    }
    
}