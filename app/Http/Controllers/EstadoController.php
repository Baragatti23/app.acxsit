<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Estado;

    class EstadoController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["proforma","utilisateur"];
        private $prefix="_estado";
        private $table="estados";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Estado(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            if(isset($params["status"]) && $params["status"]=200){
                $data=$this->executeQuery(new Estado(),$params,$id);
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
    }
?>