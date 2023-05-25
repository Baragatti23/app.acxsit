<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Profil;
use App\Models\Statu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use App\Models\Utilisateur;
use \stdClass;

class AuthController extends Controller
{
    use Setting;
    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required|string|max:255',
            'lastname'=>'required|string|max:255',
            'email'=>'required|string|max:255|unique:users',
            'password'=>'required|string|min:8',
        ]);
        if($validator->fails()){
            return response()->json([
                "status"=>401,
                "error"=>"Utilisateur non enregistré"
            ],401);
        }
        $prefix="_utilisateur";
        $user=User::create([
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        $utilisateur=Utilisateur::create([
            'reference'.$prefix=>"UTS00009",
            'name_utilisateur'=>$request->name,
            'email_utilisateur'=>$request->email,
            'lastname_utilisateur'=>$request->email,
            'created_by'.$prefix=>"UTI0001",
            'reference_statu'=>"STA0001",
            'reference_profil'=>"PRO0001",
            'created_at'.$prefix=>date("Y-m-d H:i:s"),
            'updated_at'.$prefix=>date("Y-m-d H:i:s")
        ]);
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status'=>200,
            'id'=>$user,
            'access_token'=>$token,
            'token_type'=>'Bearer'
        ],200);
    }
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required',],
        ]);
        if(!Auth::attempt($credentials)){
            return response()->json([
                "status"=>401,
                "error"=>"incorrect email or password"
            ],401);
        }
        $user=User::where('email',$request['email'])->firstOrFail();
        $token=$user->createToken('auth_token')->plainTextToken;
        $utilisateur=$this->getUtilisateur($request["email"]);
        return response()->json([
            'status'=>200,
            'data'=>$utilisateur,
            'access_tokent'=>$token,
            'token_type'=>'Bearer'
        ]);
    }
    public function logout(){
        Auth::logout();
        return response()->json([
            'status'=>200,
            'message'=>'Utilisateur deconnecté avec succès'
        ]);
    }
    public function loged(Request $request){
        return response()->json([
            'status'=>200,
            'data'=>$request->user()?$this->getUtilisateur($request->user()->email):null,
        ],200);
    }
    public function getUtilisateur($email){
        $utilisateur=Utilisateur::where('email_utilisateur',$email)->firstOrFail();
        $utilisateur=$this->clean([json_decode(json_encode($utilisateur),true)],"_utilisateur",false)[0];
        $profil=Profil::where('reference_profil',$utilisateur['reference_profil'])->first();
        $utilisateur["profil"]=$this->clean([json_decode(json_encode($profil),true)],"_profil")[0];
        $statu=Statu::where('reference_statu',$utilisateur['reference_statu'])->first();
        $utilisateur["statu"]=$this->clean([json_decode(json_encode($statu),true)],"_statu")[0];
        unset($utilisateur["reference_profil"],$utilisateur["reference_statu"]);
        return $utilisateur;
    }
}