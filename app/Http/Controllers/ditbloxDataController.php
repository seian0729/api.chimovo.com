<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Data;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class ditbloxDataController extends Controller
{
    
    public function __construct()
    {
        $this->key = "0A076B265B98524601E2D5AC4AFA732B";
    }
    //ditblox 
    public function updateDataDitblox(Request $request)
    {
        return response()->json(['errors' => ['message' => ['Current maintenance']]], 401);
        // UID
        try {
            $key = $request->input('Key');
            $note = $request->input('Note');
            $UsernameRoblocc = $request->input('Username');
            $packet = json_encode($request->input('Packet'));
            $gameId = $request->input('gameId');
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://ditbloxfruit.cc/api/v1/user/findid?key='.$key,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            if (gettype(json_decode($response,true)) != 'integer'){
                return response()->json(['errors' => ['message' => json_decode($response,true)['message']]], 401);
            }
            else
            {
                $uid = $response;
            }
            
            $curlSlot = curl_init();
            curl_setopt_array($curlSlot, array(
              CURLOPT_URL => 'https://ditbloxfruit.cc/api/v1/user/checkactive?discord_id='.$uid,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $responseSlot = curl_exec($curlSlot);
            curl_close($curlSlot);
            
            $slot = 0;
            $jsonSlot = json_decode($responseSlot,true);
            if($jsonSlot['Expired']){
              return response()->json(['errors' => ['message' => ['Expired']]], 401);
            }
            $slot = $jsonSlot['Slot'];
            
            $dataCount = new Data();
            $dataCount = $dataCount->where('UID', $uid)->count();

            $data = new Data();
            $data = $data->where('UsernameRoblocc', $UsernameRoblocc)->where('gameId', $gameId)->first();
            if ($data) {
                $data->update([
                    'Note' => $note,
                    'Description' => $packet,
                    'UID' => $uid,
                ]);
                // if
                if ($data) {
                    return response()->json(['status' => 'success', 'message' => ['Updated Data']]);
                } else {
                    return response()->json(['errors' => ['message' => ['Fail to update']]], 401);
                }
            } else {
                if ($dataCount >= $slot){
                    return response()->json(['message' => 'No more slots - Contact Hanei if u want buy more'], 401);
                }
                else{
                    $data = new Data();
                    $data->create([
                        'UID' => $uid,
                        'UsernameRoblocc' => $UsernameRoblocc,
                        'Note' => $note,
                        'Description' => $packet,
                        'gameId' => $gameId
                    ]);
                    return response()->json(['status' => 'success', 'message' => ['Created new Data']]);
                }
                
            }

        } catch (\Throwable $th) {
            // log
            Log::error($th);
            return response()->json(['errors' => ['message' => ['Error while update data - Pls Contact Hanei'], 'err' => $th,'uid' => $uid]], 401);
        }
    } 
    
    // getdata
    
    public function getData(Request $request)
    {
        return response()->json(['errors' => ['message' => ['Current maintenance']]], 401);
        $token = $request->header('Authorization');
        $gameId = $request->input('gameId');
    
        //
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

        if ($decoded) {
            $decoded_array = (array)$decoded;
            $uid = $decoded_array['userId'];
            if ($uid) {
                $data = new Data();
                $data = $data->where('UID', $uid)->where('gameId', $gameId) ->get();

                #return response()->json(['status' => 'success', 'data' => $data]);
                return json_encode(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }

    }
    
    public function getTotalAccount(Request $request){
        return response()->json(['errors' => ['message' => ['Current maintenance']]], 401);
        $token = $request->header('Authorization');
        $gameId = $request->input('gameId');
        
        //
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

        if ($decoded) {
            $decoded_array = (array)$decoded;
            $uid = $decoded_array['userId'];
            if ($uid) {
                $data = new Data();
                $data = $data->where('UID', $uid)->count();

                #return response()->json(['status' => 'success', 'data' => $data]);
                return json_encode(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }

    }
    
    // bulkDeleteData
    public function bulkDeleteData(Request $request)
    {
        return response()->json(['errors' => ['message' => ['Current maintenance']]], 401);
        // UID
        $token = $request->header('Authorization');
        $usernames = $request->input('Usernames');

        //
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

        if ($decoded) {
            $decoded_array = (array)$decoded;
            $uid = $decoded_array['userId'];
            if ($uid) {
                $data = new Data();
                // username and uid
                $data = $data->whereIn('UsernameRoblocc', $usernames)->where('UID', $uid)->delete();

                if ($data) {
                    return response()->json(['status' => 'success', 'data' => $data]);
                } else {
                    return response()->json(['errors' => ['message' => ['Account not found']]], 401);
                }
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    public function bulkUpdatePasswordAndCookie(Request $request)
    {
        return response()->json(['errors' => ['message' => ['Current maintenance']]], 401);
        // UID
        $token = $request->header('Authorization');
        // input file txt
        $file = $request->file('file');
        // read file
        $file = file($file);
        // each line has format: username/password/cookie
        $Data = [];
        foreach ($file as $line) {
            $line = trim($line);
            if (str_contains($line, '/')) {
                $line = explode('/', $line);
                // insert to array
                $Data[] = [
                    'Username' => $line[0],
                    'Password' => $line[1],
                    'Cookie' => $line[2]
                ];
            }
            else{
                return response()->json(['message' => ['Invalid Format']], 401);
            }
        }
        // Data[0] = {
        //     Username: 'string',
        //     Password: 'string',
        //     Cookie: 'string'
        // }

        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }

        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        
        if ($decoded) {
            $decoded_array = (array)$decoded;
            $uid = $decoded_array['userId'];
            $tempAdd = 0;
            if ($uid) {
                
                $curlSlot = curl_init();
                curl_setopt_array($curlSlot, array(
                  CURLOPT_URL => 'https://ditbloxfruit.cc/api/v1/user/checkactive?discord_id='.$uid,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                ));
                
                $responseSlot = curl_exec($curlSlot);
                curl_close($curlSlot);
                
                $slot = 0;
                $jsonSlot = json_decode($responseSlot,true);
                if($jsonSlot['Expired']){
                  return response()->json(['errors' => ['message' => ['Expired']]], 401);
                }
                $slot = $jsonSlot['Slot'];
                
                $data = new Data();
                // username and uid
                foreach ($Data as $key => $value) {
                    $data = $data->where('UsernameRoblocc', $value['Username'])->where('UID', $uid)->first();
                    if ($data) {
                        $data->update([
                            'Password' => $value['Password'],
                            'Cookie' => $value['Cookie']
                        ]);
                    }
                    else{
                        
                        $dataCount = new Data();
                        $dataCount = $dataCount->where('UID', $uid)->count();
                        
                        if ($dataCount >= $slot){
                            if ($tempAdd == 0){
                                return response()->json(['message' => 'No more slots - Contact Hanei if u want buy more'], 401);
                            }
                            else{
                                return response()->json(['message' => 'Added '.$tempAdd.' accounts - No more slots - Contact Hanei if u want buy more'], 401);
                            }
                        }
                        else{
                            $data = new Data();
                            $data->insert([
                                'UsernameRoblocc' => $value['Username'],
                                'Password' => $value['Password'],
                                'Cookie' => $value['Cookie'],
                                'UID' => $uid,
                                'Note' => 'None',
                                'gameId' => '994732206',
                            ]);
                            $tempAdd++;
                        }
                        
                    }
                }
                return response()->json(['status' => 'success', 'data' => $data]);
            }
        }
    }
    
    
}