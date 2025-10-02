<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Data;
use App\Models\AccountSlot;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use DB;

class DataController extends Controller
{
    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }

    public function getData(Request $request)
    {
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
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $account = $Account->where('UID', $id)->first();

            if ($account) {
                $data = new Data();
                $data = $data->where('UID', $id)->where('gameId', $gameId) ->get();

                #return response()->json(['status' => 'success', 'data' => $data]);
                return json_encode(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }

    }
    
    public function getDataChunk(Request $request){
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
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $account = $Account->where('UID', $id)->first();

            if ($account) {
                $data = new Data();
                $data->where('UID', $id)->where('gameId', $gameId)->chunkById(100, function (Collection $users) {
                    foreach ($datas as $dataFor) {
                        return json_encode(['status' => 'success', 'data' => $dataFor]);
                    }
                });
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    public function getDataLimit(Request $request){
        $token = $request->header('Authorization');
        $gameId = $request->input('gameId');
        
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $limit = $limit >= 100 ? 100 : $limit;
        

        //
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

            if ($account) {
                $data = new Data();
                $totalData = $data->where('UID', $id)->where('gameId', $gameId)->count();
                $data = $data->where('UID', $id)->where('gameId', $gameId)->limit($limit)->offset(($page - 1) * $limit)->orderBy('updatedAt','desc')->get();
                $totalPage = round($totalData/$limit);
                return response()->json(['status' => 'success', 'data' => $data, 'totalPage' => $totalPage, 'totalData' => $totalData]);
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    public function getOnlineAccountEachNote(Request $request)
    {
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
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $account = $Account->where('UID', $id)->first();
            if ($account) {
                $data = new Data();
                // username and uid
                $data = $data->where('UID', $id)->where('gameId', $gameId)->whereRaw('updatedAt >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)')
                ->groupBy('Note')->select('Note', DB::raw('count(*) as total'))->get();

                if ($data) {
                    return response()->json(['status' => 'success', 'data' => $data, 'time' => Carbon::now() ]);
                } else {
                    return response()->json(['errors' => ['message' => ['Account not found']]], 401);
                }

            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    public function getTotalAccount(Request $request){
        $token = $request->header('Authorization');
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

            if ($account) {
                $data = new Data();
                $data = $data->where('UID', $id)->count();

                return response()->json(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    // Get Data By Username
    public function getDataByUsername(Request $request)
    {
        $username = $request->input('Username');
        $gameId = 994732206;

        $data = new Data();
        $data = $data->where('UsernameRoblocc', $username)->where('gameId', $gameId)->first();

        if ($data) {
            return response()->json(['status' => 'success', 'data' => $data]);
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    
    // Get Data By Username & gameId
    public function getDataByUsernameAndGameId(Request $request)
    {
        $username = $request->input('Username');
        $gameId = $request->input('GameId');;

        $data = new Data();
        $data = $data->where('UsernameRoblocc', $username)->where('gameId', $gameId)->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }

    // updateData
    public function updateData(Request $request)
    {
        // UID
        try {
            $uid = $request->input('UID');
            $note = $request->input('Note');
            $UsernameRoblocc = $request->input('Username');
            $packet = json_encode($request->input('Packet'));
            $gameId = $request->input('gameId');

            $account = new Account();
            $accountSlot = new AccountSlot();
            
            /*
            if ($gameId == "3317771874"){
                return response()->json(['errors' => ['message' => ['NOT SUPPORT']]], 401);
            }
            */
            
            if (gettype($uid) == 'string' and strlen($uid) >= 16){
                $account = $account->where('siginKey', $uid)->first();
            }
            else{
                $account = $account->where('UID', $uid)->first();
            }

            if (!$account) {
                return response()->json(['errors' => ['message' => ['Account not found']]], 401);
            }
            $data = new Data();
            $data = $data->where('UsernameRoblocc', $UsernameRoblocc)->where('gameId', $gameId)->first();
            if ($data) {
                if (gettype($uid) == 'string' and strlen($uid) > 24){
                    $data->update([
                        'Note' => $note,
                        'Description' => $packet,
                        'UID' => (int)$account -> UID
                    ]);
                }
                else{
                    $data->update([
                        'Note' => $note,
                        'Description' => $packet,
                        'UID' => $uid
                    ]);
                }
                // if
                if ($data) {
                    return response()->json(['status' => 'success', 'message' => ['Updated Data']]);
                } else {
                    return response()->json(['errors' => ['message' => ['Fail to update']]], 401);
                }
            } else {
                // // create new data
                // $dataCount = new Data();
                // if (gettype($uid) == 'string' and strlen($uid) >= 16){
                //     $dataCount = $dataCount->where('UID', (int)$account -> UID)->count();
                // }
                // else
                // {
                //     $dataCount = $dataCount->where('UID', $uid)->count();
                // }
                
                // $activeSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->first();
                // if ($activeSlot){
                //     $totalSlot = $accountSlot->where('UID', $account->UID)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
                // }
                // else{
                //     $totalSlot = 0;
                // }
                
                // if ($dataCount >= $totalSlot){
                //      return response()->json(['message' => 'No more slots - Contact Hanei if u want buy more'.$totalSlot], 401);
                // }
                // else {
                //     $data = new Data();
                //     $data->create([
                //         'UID' => (int)$account -> UID,
                //         'UsernameRoblocc' => $UsernameRoblocc,
                //         'Note' => $note,
                //         'Description' => $packet,
                //         'gameId' => $gameId
                //     ]);
                //     return response()->json(['status' => 'success', 'message' => ['Created new Data']]);
                // }
                $data = new Data();
                $data->create([
                    'UID' => (int)$account -> UID,
                    'UsernameRoblocc' => $UsernameRoblocc,
                    'Note' => $note,
                    'Description' => $packet,
                    'gameId' => $gameId
                ]);
                return response()->json(['status' => 'success', 'message' => ['Created new Data']]);
            }

        } catch (\Throwable $th) {
            // log
            Log::error($th);
            return response()->json(['errors' => ['message' => ['Error while update data - Pls Contact Hanei'], 'err' => $th]], 401);
        }
    }

    // deleteData
    public function deleteData(Request $request)
    {
        // UID
        $token = $request->header('Authorization');
        $username = $request->input('Username');

        //
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
            if ($account) {
                $data = new Data();
                // username and uid
                $data = $data->where('UsernameRoblocc', $username)->where('UID', $id)->first();

                if ($data) {
                    $data->delete();
                    return response()->json(['status' => 'success', 'data' => $data],200);
                } else {
                    return response()->json(['errors' => ['message' => ['Account not found']]], 401);
                }
            }
        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
    }
    // bulkDeleteData
    public function bulkDeleteData(Request $request)
    {
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
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $account = $Account->where('UID', $id)->first();
            if ($account) {
                $data = new Data();
                // username and uid
                $data = $data->whereIn('UsernameRoblocc', $usernames)->where('UID', $id)->delete();

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
    // bulk update Password and Cookie
    public function bulkUpdatePasswordAndCookie(Request $request)
    {
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
                response()->json(['errors' => ['message' => ['Invalid Format']]], 401);
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
            $user = $decoded_array['user'];
            $user = (array)$user;
            $id = $user['id'];
            $Account = new Account();
            $accountSlot = new AccountSlot();
            $account = $Account->where('UID', $id)->first();
            $tempAdd = 0;
            if ($account) {
                $data = new Data();
                // username and uid
                foreach ($Data as $key => $value) {
                    $data = $data->where('UsernameRoblocc', $value['Username'])->where('UID', $id)->first();
                    if ($data) {
                        $data->update([
                            'Password' => $value['Password'],
                            'Cookie' => $value['Cookie']
                        ]);
                    }
                    else{
                        // $dataCount = new Data();
                        // $dataCount = $dataCount->where('UID', $id)->count();
                        
                        // $activeSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->first();
                        // if ($activeSlot){
                        //     $totalSlot = $accountSlot->where('UID', $id)->whereDate('dateExpired','>=',Carbon::today())->sum('slots');
                        // }
                        // else{
                        //     $totalSlot = 0;
                        // }
                        
                        // $limit = $totalSlot;
                        
                        // if ($dataCount >= $limit){
                        //     if ($tempAdd == 0){
                        //         return response()->json(['message' => 'No more slots - Contact Hanei if u want buy more'], 401);
                        //     }
                        //     else{
                        //         return response()->json(['message' => 'Added '.$tempAdd.' accounts - No more slots - Contact Hanei if u want buy more'], 401);
                        //     }
                        // }
                        // else{
                        //     $data = new Data();
                        //     $data->insert([
                        //         'UsernameRoblocc' => $value['Username'],
                        //         'Password' => $value['Password'],
                        //         'Cookie' => $value['Cookie'],
                        //         'UID' => $id,
                        //         'Note' => 'None',
                        //         'gameId' => '994732206',
                        //     ]);
                        //     $tempAdd++;
                        // }
                        $data = new Data();
                            $data->insert([
                                'UsernameRoblocc' => $value['Username'],
                                'Password' => $value['Password'],
                                'Cookie' => $value['Cookie'],
                                'UID' => $id,
                                'Note' => 'None',
                                'gameId' => '994732206',
                            ]);
                    }
                }
                return response()->json(['status' => 'success', 'data' => $data]);
            }

        } else {
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }

    }

}
