<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Equipement;

    class EquipementController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_equipement";
        private $table="equipements";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Equipement(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            return $this->executeQuery(new Equipement(),$params,$id);
        }
    }
?>