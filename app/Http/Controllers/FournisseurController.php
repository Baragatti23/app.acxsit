<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Equipement;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    use Setting;
    // PROPERTIES ==============================
    private $foreign_columns=["utilisateur"];
    private $prefix="_fournisseur";
    private $table="fournisseurs";
    
    // METHODS =================================
    public function get($id=null){
        $id=$id?["reference".$this->prefix,$id]:[];
        $params=$this->validateParams(new Fournisseur(),$id);
        if(isset($params["status"]) && $params["status"]=400){
            return $params;
        }
        $data=$this->executeQuery(new Fournisseur(),$params,$id);
        if($data["status"]==200 && isset($data["data"])){
            $data["total"]=Fournisseur::count();
        }
        return $data;
    }
    public function to_create_products(){
        $list=Fournisseur::select("reference_fournisseur as reference","nom_fournisseur as content")
        ->get();
        $data=json_decode(json_encode($list),true);
        foreach ($data as $key=>$item) {
            $data[$key]["content"]=$item["reference"]." - ".$item["content"];
        }
        return response()->json([
            "status"=>200,
            "data"=>$data
        ],200);
    }
    public function del($id){
        $count=Fournisseur::where("reference".$this->prefix,$id)->count();
        $deleted=false;
        if($count>0){
            // Equipement::where("reference".$this->prefix,$id)->delete();
            if(Fournisseur::where("reference".$this->prefix,$id)->delete()) $deleted=true;
        }
        if($deleted){
            return[
                "status"=>200,
                "id"=>$id,
                "success"=>"Equipement supprimée avec succés"
            ];
        }else{
            return[
                "status"=>500,
                "id"=>$id,
                "error"=>"Operation non réussi!"
            ];
        }
    }
}
