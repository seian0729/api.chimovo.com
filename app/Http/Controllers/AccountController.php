<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Data;
use App\Models\AccountSlot;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountController extends Controller
{

    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }

    // getUser


    public function login(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $username = $request->input('username');
        $password = $request->input('password');
        $Account = new Account();
        $accountSlot = new AccountSlot();
        // password in database not hash
        $account = $Account->where('Username', $username)->where('Password', $password)->first();
        if ($account) {
            if ($account->Password == $password) {
                $dateExpired = Carbon::createFromFormat('Y-m-d H:i:s', $account-> dateExpired);
                $dateNow = Carbon::createFromFormat('Y-m-d H:i:s',  Carbon::now('Asia/Ho_Chi_Minh'));
                if ($dateExpired->lt($dateNow)) {
                    return response()->json(['errors' => ['message' => ['Your access is no longer available (expired), contact Hanei if you want to renew']]], 401);
                    } else {
                        $activeSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->first();
                        if ($activeSlot){
                            $totalSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
                        }
                        else{
                            $totalSlot = 0;
                        }
                    $payload = array(
                        "iat" => time(),
                        "exp" => time() + (60 * 60 * 24 * 7),
                        "user" => array(
                            "id" => $account->UID,
                            "username" => $account->Username,
                            "role" => $account->Role,
                            "limitacc" => $totalSlot,
                            "siginKey" => $account->siginKey,
                            "dateExpired" => strtotime($account->dateExpired)
                        )
                    );
                    $jwt = JWT::encode($payload, $this->key, 'HS256');
                    return response()->json(['status' => 'success', 'user' => ['token' => $jwt, 'username' => $account->Username, 'id' => $account->UID, 'role' => $account->Role, 'limitacc' => $totalSlot, 'dateExpired' => strtotime($account->dateExpired), 'siginKey' => $account->siginKey, 'ditbloxData' => $account->ditbloxData]], 200);
                }
            } else {
                return response()->json(['errors' => ['message' => ['Your username or password is incorrect']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Your username or password is incorrect']]], 401);
        }
    }

    public function loginKey(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $siginKey = $request->input('key');
        $accountSlot = new AccountSlot();
        $Account = new Account();
        $account = $Account->where('siginKey', $siginKey)->first();
        
        if ($account) {
            $dateExpired = Carbon::createFromFormat('Y-m-d H:i:s', $account-> dateExpired);
            $dateNow = Carbon::createFromFormat('Y-m-d H:i:s',  Carbon::now('Asia/Ho_Chi_Minh'));
            if ($dateExpired->lt($dateNow)) {
                return response()->json(['errors' => ['message' => ['Your access is no longer available (expired), contact Hanei if you want to renew']]], 401);
            } else {
                $activeSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->first();
                if ($activeSlot){
                    $totalSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
                }
                else{
                    $totalSlot = 0;
                }
                
                $payload = array(
                "iat" => time(),
                "exp" => time() + (60 * 60 * 24 * 7),
                "user" => array(
                    "id" => $account->UID,
                    "username" => $account->Username,
                    "role" => $account->Role,
                    "limitacc" => $totalSlot,
                    "siginKey" => $account->siginKey,
                    "dateExpired" => strtotime($account->dateExpired)
                )
            );
                
                $jwt = JWT::encode($payload, $this->key, 'HS256');
                    return response()->json(['status' => 'success', 'user' => ['token' => $jwt, 'username' => $account->Username, 'id' => $account->UID, 'role' => $account->Role, 'limitacc' => $totalSlot, 'dateExpired' => strtotime($account->dateExpired), 'siginKey' => $account->siginKey, 'ditbloxData' => $account->ditbloxData]], 200);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Your key is incorrect']]], 401);
        }
    }

    public function getUser(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
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
        $accountSlot = new AccountSlot();
        $account = $Account->where('UID', $id)->first();
        if ($account) {
            $activeSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->first();
            if ($activeSlot){
                $totalSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
            }
            else{
                $totalSlot = 0;
            }
            $payload = array(
                "iat" => time(),
                "exp" => time() + (60 * 60 * 24 * 7),
                "user" => array(
                    "id" => $account->UID,
                    "username" => $account->Username,
                    "role" => $account->Role,
                    "limitacc" => $totalSlot,
                    "siginKey" => $account->siginKey,
                    "dateExpired" => strtotime($account->dateExpired)
                )
            );
            $jwt = JWT::encode($payload, $this->key, 'HS256');

            return response()->json(['status' => 'success', 'user' => ['token' => $jwt, 'username' => $account->Username, 'id' => $account->UID, 'role' => $account->Role, 'limitacc' => $totalSlot, 'dateExpired' => strtotime($account->dateExpired), 'siginKey' => $account->siginKey, 'ditbloxData' => $account->ditbloxData]], 200);
        } else {
            return response()->json(['errors' => ['message' => ['Tài khoản không tồn tại']]], 401);
        }

    }
}
