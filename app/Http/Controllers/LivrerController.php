<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Livrer;
    use Illuminate\Http\Request;

    class LivrerController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        // private $foreign_columns=["utilisateur"];
        private $prefix="_livrer";
        private $table="livrers";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Livrer(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Livrer(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Livrer();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["bordereau"],$data["equipement"],$data["unities"])){
                $element->{"reference_bordereau"}=$data["bordereau"];
                $element->{"reference_equipement"}=$data["equipement"];
                $element->{"unities".$this->prefix}=$data["unities"];
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Equipement ajouté au bordereau de livraison avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Equipement non enregistré"
                ];
            }
        }
    }
?>