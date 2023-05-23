<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Client;
use App\Models\Proforma;
use Illuminate\Http\Request;

    class ClientController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_client";
        private $table="clients";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Client(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Client(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->countAchats(json_decode(json_encode($data["data"]),true));
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Client();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["name"])){
                $element->{"name".$this->prefix}=$data["name"];
                $element->{"telephone".$this->prefix}=$data["telephone"] ?? "";
                $element->{"email".$this->prefix}=$data["email"] ?? "";
                $element->{"address".$this->prefix}=$data["address"] ?? "";
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Client enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Client non enregistré"
                ];
            }
        }
        public function countAchats($clients){
            $i=0;
            foreach ($clients as $item) {
                $clients[$i]["total_achats"]=Proforma::where("reference_client",$item["reference"] ?? $item["reference_client"])
                ->count();
                $i++;
            }
            return $clients;
        }
    }
?>