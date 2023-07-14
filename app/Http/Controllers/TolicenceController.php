<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Equipement;
use App\Models\Tolicence;
use App\Models\Licence;
use Illuminate\Http\Request;

class TolicenceController extends Controller{
    use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_tolicence";
        private $table="tolicences";
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Tolicence(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference_equipement","reference_licence"]);
            }
            return $data;
        }
        public function get_licences($equipement){
            $list=Tolicence::where("reference_equipement",$equipement)->get();
            $list=json_decode(json_encode($list),true);
            $data=[];
            if(count($list)!=0){
                foreach ($list as $value) {
                    $elem=Licence::select("reference_licence as reference","libelle_licence as libelle")->where("reference_licence",$value["reference_licence"])->first();
                    if($elem){
                        $data[]=$elem;
                    }
                }
            }
            return response([
                "status"=>200,
                "data"=>json_decode(json_encode($data),true)
            ],200);
        }
        public function delete_licences(Request $request){
            $data = json_decode($request->getContent(),true);
            Tolicence::where("reference_equipement",$data["equipement"])->where("reference_licence",$data["licence"])->delete();
            return response([
                "status"=>200,
                // "data"=>json_decode(json_encode($data),true),
                "success"=>"Licence supprimé avec succès"
            ],200);
        }
        public function post(Request $request){
            $data = json_decode($request->getContent(),true);
            $element=new Tolicence();
            if(isset($data["equipement"],$data["licence"])){
                $element->reference_equipement=$data["equipement"];
                $element->reference_licence=$data["licence"];
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Equipement enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Client non enregistré"
                ];
            }
            return response([
                "status"=>200,
                // "data"=>json_decode(json_encode($data),true),
                "success"=>"Licence supprimé avec succès"
            ],200);
        }
}
