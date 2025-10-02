<?php

namespace App\Http\Controllers;

use App\Models\Hanei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HaneiController extends Controller
{
    public function getSignature(Request $request)
    {
        //Rquest
        $token = $request -> header('Authorization');
        
        if (!$token){
            return response()->json(['error' => ['messages' => 'Token not found']], 401);
        }
        
        if (strlen($token) != 32){
            return response()->json(['error' => ['messages' => 'Invalid Token Format']], 401);
        }
        
        $hanei = new Hanei();
        
        $Hanei = $hanei->where('wl_key',$token)->first();
        
        if(!$Hanei){
            return response()->json(['error' => ['messages' => 'What da dog doin']], 401);
        }
        else{
            return response()->json(['status' => 'success', 'data' => $Hanei->wl_key], 200);
        }

    }
    
    public function getScript(){
        return File::get(storage_path('app/blocc-trai-cay.lua'));
    }
    
}
