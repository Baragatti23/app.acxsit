<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use Illuminate\Http\Request;
    use App\Models\Formation;

    class FormationController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        // private $foreign_columns=["Formation","utilisateur"];
        private $prefix="_formation";
        private $table="formations";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Formation(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            return $this->executeQuery(new Formation(),$params,$id);
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Formation();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["libelle"],$data["domaine"],$data["duration"],$data["amount"])){
                $element->{"libelle".$this->prefix}=$data["libelle"];
                $element->{"domaine".$this->prefix}=$data["domaine"];
                $element->{"duree".$this->prefix}=$data["duration"];
                $element->{"cout".$this->prefix}=$data["amount"];
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Formation enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Licence non enregistré"
                ];
            }
        }
    }
?>