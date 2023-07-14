<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Traits\Setting;
    use App\Models\Activite;
    class ActiviteController extends Controller{
        use Setting;
        private $prefix="_activite";
        private $table="activites";
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Activite(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["total"]=Activite::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
    }
?>