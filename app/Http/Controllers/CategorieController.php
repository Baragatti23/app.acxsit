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
        $params=$this->validateParams();
        if(isset($params["status"]) && $params["status"]=400){
            return $params;
        }
        $data=$this->executeQuery(new Categorie(),$params,$id);
        if($data["status"]==200 && isset($data["data"])){
            $data["total"]=Categorie::count();
            $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
        }
        return $data;
    }
    public function put($id,Request $request){
        $data = json_decode($request->getContent(),true);
        // echo json_encode($data);
        // exit;
        if(isset($id)){
            $success=false;
            $count=Categorie::where("reference".$this->prefix,$id)->count();
            if($count>0){
                if(isset($data["libelle"])){
                    Categorie::where("reference".$this->prefix,$id)->update(["libelle".$this->prefix=>$data["libelle"]]);
                    $success=true;
                }
                if($success){
                    Categorie::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                    $this->insertActivity("UPDATE",$id);
                    return[
                        "status"=>200,
                        "id"=>$id,
                        "success"=>"Categorie modifié avec succés"
                    ];
                }
            }
        }
        return[
            "status"=>400,
            "error"=>"Modification non réussi"
        ];
    }
    public function del($id){
        $count=Categorie::where("reference".$this->prefix,$id)->count();
        $deleted=false;
        if($count>0){
            if(Categorie::where("reference".$this->prefix,$id)->delete()) $deleted=true;
        }
        if($deleted){
            $this->insertActivity("DELETE",$id);
            return[
                "status"=>200,
                "id"=>$id,
                "success"=>"Categorie supprimée avec succés"
            ];
        }else{
            return[
                "status"=>500,
                "id"=>$id,
                "error"=>"Operation non réussi!"
            ];
        }
    }
    public function post(Request $request){
        // $columns=$this->getTableColumns($this->table);
        $data = json_decode($request->getContent(),true);
        $element=new Categorie();
        $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
        if(isset($data["libelle"])){
            $element->{"libelle".$this->prefix}=$data["nom"];
            $element->{"reference_utilisateur"}="UTS109083DOM";
            $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
            $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
            $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
            $element->save();
            return[
                "status"=>200,
                "id"=>$element->{"reference".$this->prefix},
                "success"=>"Categorie enregistré avec succés"
            ];
        }else{
            return[
                "status"=>400,
                "error"=>"Categorie non enregistré"
            ];
        }
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
