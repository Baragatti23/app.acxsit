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
        $params=$this->validateParams();
        if(isset($params["status"]) && $params["status"]=400){
            return $params;
        }
        $data=$this->executeQuery(new Fournisseur(),$params,$id);
        if($data["status"]==200 && isset($data["data"])){
            $data["total"]=Fournisseur::count();
            $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
        }
        return $data;
    }
    public function post(Request $request){
        // $columns=$this->getTableColumns($this->table);
        $data = json_decode($request->getContent(),true);
        $element=new Fournisseur();
        $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
        if(isset($data["nom"],$data["email"])){
            $element->{"nom".$this->prefix}=$data["nom"];
            $element->{"email".$this->prefix}=$data["email"];
            $element->{"telephone".$this->prefix}=$data["telephone"];
            $element->{"adresse".$this->prefix}=$data["adresse"];
            $element->{"reference_utilisateur"}="UTS109083DOM";
            $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
            $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
            $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
            $element->save();
            return[
                "status"=>200,
                "id"=>$element->{"reference".$this->prefix},
                "success"=>"Fournisseur enregistré avec succés"
            ];
        }else{
            return[
                "status"=>400,
                "error"=>"Fournisseur non enregistré"
            ];
        }
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
    public function put($id,Request $request){
        $data = json_decode($request->getContent(),true);
        // echo json_encode($data);
        // exit;
        if(isset($id)){
            $success=false;
            $count=Fournisseur::where("reference".$this->prefix,$id)->count();
            if($count>0){
                if(isset($data["nom"])){
                    Fournisseur::where("reference".$this->prefix,$id)->update(["nom".$this->prefix=>$data["nom"]]);
                    $success=true;
                }
                if(isset($data["telephone"])){
                    Fournisseur::where("reference".$this->prefix,$id)->update(["telephone".$this->prefix=>$data["telephone"]]);
                    $success=true;
                }
                if(isset($data["email"])){
                    Fournisseur::where("reference".$this->prefix,$id)->update(["email".$this->prefix=>$data["email"]]);
                    $success=true;
                }
                if(isset($data["adresse"])){
                    Fournisseur::where("reference".$this->prefix,$id)->update(["adresse".$this->prefix=>$data["adresse"]]);
                    $success=true;
                }
                if($success){
                    Fournisseur::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                    $this->insertActivity("UPDATE",$id);
                    return[
                        "status"=>200,
                        "id"=>$id,
                        "success"=>"Fournisseur modifié avec succés"
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
        $count=Fournisseur::where("reference".$this->prefix,$id)->count();
        $deleted=false;
        if($count>0){
            if(Fournisseur::where("reference".$this->prefix,$id)->delete()) $deleted=true;
        }
        if($deleted){
            $this->insertActivity("DELETE",$id);
            return[
                "status"=>200,
                "id"=>$id,
                "success"=>"Fournisseur supprimée avec succés"
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
