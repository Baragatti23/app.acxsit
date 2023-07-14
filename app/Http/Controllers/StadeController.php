<?php

    namespace App\Http\Controllers;
    use App\Http\Controllers\Traits\Setting;
    use App\Models\Stade;

    class StadeController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["proforma","utilisateur"];
        private $prefix="_stade";
        private $table="stades";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Stade(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            if(isset($params["status"]) && $params["status"]=200){
                $data=$this->executeQuery(new Stade(),$params,$id);
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
    }
?>