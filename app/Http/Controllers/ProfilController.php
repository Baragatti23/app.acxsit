<?php

    namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Profil;
class ProfilController extends Controller{

    use Setting;

    // PROPERTIES ==============================
    private $prefix="_profil";
    private $table="profil";
    
    // METHODS =================================
    public function get($id=null){
        $id=$id?["reference".$this->prefix,$id]:[];
        $params=$this->validateParams(new Profil(),$id);
        if(isset($params["status"]) && $params["status"]=400){
            return $params;
        }
        if(isset($params["status"]) && $params["status"]=200){
            $data=$this->executeQuery(new Profil(),$params,$id);
            $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
        }
        return $data;
    }
    public function to_create_user(){
        $data=Profil::select("reference_profil as reference","libelle_profil as content")->get();
        $data=json_decode(json_encode($data),true);
        foreach ($data as $key=>$item) {
            $data[$key]["content"]=$item["reference"]." - ".$item["content"];
        }
        return response()->json([
            "status"=>200,
            "data"=>$data
        ],200);
    }
}
?>