<?php
namespace App\Http\Controllers;

use App\Models\DitbloxConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class DitbloxConfigController extends Controller
{

    
    public function __construct()
    {
        $this->key = "0A076B265B98524601E2D5AC4AFA732B";
    }
    //ditblox 

    public function getConfig(Request $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $token = $request->header('Authorization');
        $config = json_encode($request->input('Config'));
        
        if (!$token) {
            return response()->json(['errors' => ['message' => ['Token not found']]], 401);
        }
        
        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

        if ($decoded) {
            $decoded_array = (array)$decoded;
            $uid = $decoded_array['userId'];
            $DitbloxConfig = new DitbloxConfig();
            $DitbloxConfig = $DitbloxConfig->where('Uid', $uid)->first();
            if ($DitbloxConfig){
                $DitbloxConfig = $DitbloxConfig->where('Uid', $uid)->get();
                return response()->json(['status' => 'success', 'data' => $DitbloxConfig]);
            }
            else{
                return response()->json(['errors' => ['message' => ['Config not found']]], 401);
            }
        }
        else{
            return response()->json(['errors' => ['message' => ['Account not found']]], 401);
        }
        
    }
    
}