<?php
    namespace App\Http\Controllers;
    use App\Http\Controllers\Traits\Setting;
    use App\Models\Connexion;
    class ConnexionController extends Controller{
        use Setting;
        private $prefix="_connexion";
        private $table="connexions";
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Connexion(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["total"]=Connexion::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
    }
?>