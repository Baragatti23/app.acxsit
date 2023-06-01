<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    use Setting;
    // PROPERTIES ==============================
    private $foreign_columns=["utilisateur"];
    private $prefix="_categorie";
    private $table="categories";
    
    // METHODS =================================
    public function get($id=null){
        $id=$id?["reference".$this->prefix,$id]:[];
        $params=$this->validateParams(new Categorie(),$id);
        if(isset($params["status"]) && $params["status"]=400){
            return $params;
        }
        $data=$this->executeQuery(new Categorie(),$params,$id);
        if($data["status"]==200 && isset($data["data"])){
            $data["total"]=Categorie::count();
        }
        return $data;
    }
    public function to_create_products(){
        $list=Categorie::select("reference_categorie as reference","libelle_categorie as content")
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
}
