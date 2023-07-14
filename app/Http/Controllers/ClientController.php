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
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Client(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->countAchats(json_decode(json_encode($data["data"]),true));
                $data["total"]=Client::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function del($id){
            $count=Client::where("reference".$this->prefix,$id)->count();
            $deleted=false;
            if($count>0){
                Proforma::where("reference".$this->prefix,$id)->delete();
                if(Client::where("reference".$this->prefix,$id)->delete()) $deleted=true;
            }
            if($deleted){
                $this->insertActivity("DELETE",$id);
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Client supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            if(isset($id)){
                $success=false;
                $count=Client::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["name"])){
                        Client::where("reference".$this->prefix,$id)->update(["name".$this->prefix=>$data["name"]]);
                        $success=true;
                    }
                    if(isset($data["email"])){
                        Client::where("reference".$this->prefix,$id)->update(["email".$this->prefix=>$data["email"]]);
                        $success=true;
                    }
                    if(isset($data["telephone"])){
                        Client::where("reference".$this->prefix,$id)->update(["telephone".$this->prefix=>$data["telephone"]]);
                        $success=true;
                    }
                    if(isset($data["address"])){
                        Client::where("reference".$this->prefix,$id)->update(["address".$this->prefix=>$data["address"]]);
                        $success=true;
                    }
                    if($success){
                        Client::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Client modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Client();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["name"],$data["email"])){
                $element->{"name".$this->prefix}=$data["name"];
                $element->{"telephone".$this->prefix}=$data["telephone"] ?? "";
                $element->{"email".$this->prefix}=$data["email"];
                $element->{"address".$this->prefix}=$data["address"] ?? "";
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
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
        public function to_create_proformas(){
            $list=Client::select("reference_client as reference","name_client as content")
            ->get();
            $data=json_decode(json_encode($list),true);
            foreach ($data as $key=>$item) {
                $data[$key]["content"]=$item["reference"]." - ".$item["content"];
            }
            return response()->json([
                "status"=>200,
                "data"=>$data
            ],200);
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