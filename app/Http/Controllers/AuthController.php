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
            'name'=>'required|string|min:3',
            'lastname'=>'required|string|min:3',
            'email'=>'required|string|min:3',
            'profil'=>'string|min:3',
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
        // json_encode(Auth::user());
        // exit;
        // $foreign=Utilisateur::select("reference_utilisateur")->where("email_utilisateur",Auth::user()->email)->first();
        $utilisateur=Utilisateur::create([
            'reference'.$prefix=>"UTS".$this->generateID(),
            'name_utilisateur'=>$request->name,
            'email_utilisateur'=>$request->email,
            'lastname_utilisateur'=>$request->lastname,
            'reference_profil'=>$request->profil ?? "PRO0001",
            'created_by'.$prefix=>"UTS00009",
            'reference_statu'=>"STA0001",
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
        Auth::user()->tokens->each(function($token, $key) {
            $token->delete();
        });
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
