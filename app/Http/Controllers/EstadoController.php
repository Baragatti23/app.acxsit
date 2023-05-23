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
            return $this->executeQuery(new Estado(),$params,$id);
        }
    }
?>