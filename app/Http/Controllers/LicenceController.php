<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Licence;
    use Illuminate\Http\Request;
    use App\Models\Proforma;
    use App\Models\Bordereau;
    use App\Models\Client;
use App\Models\Equipement;
use App\Models\Tolicence;

    class LicenceController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_licence";
        private $table="licences";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Licence(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                // $data["data"]=$this->getClient($data["data"]);
                $data["data"]=$this->countMonthsCostumed(json_decode(json_encode($data["data"]),true));
                $data["total"]=Licence::count();
                $data["total_products"]=Licence::count()+Equipement::count();
                $data["total_licenses"]=Licence::count();
                $data["total_equipements"]=Equipement::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Licence();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["fournisseur"],$data["libelle"],$data["duree"])){
                $element->{"reference_fournisseur"}=$data["fournisseur"];
                $element->{"duree".$this->prefix}=$data["duree"];
                $element->{"libelle".$this->prefix}=$data["libelle"];
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Licence enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Licence non enregistré"
                ];
            }
        }
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            // echo json_encode($data);
            // exit;
            if(isset($id)){
                $success=false;
                $count=Licence::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["duree"])){
                        Licence::where("reference".$this->prefix,$id)->update(["duree".$this->prefix=>$data["duree"]]);
                        $success=true;
                    }
                    if(isset($data["libelle"])){
                        Licence::where("reference".$this->prefix,$id)->update(["libelle".$this->prefix=>$data["libelle"]]);
                        $success=true;
                    }
                    if(isset($data["garantie"])){
                        Licence::where("reference".$this->prefix,$id)->update(["garantie".$this->prefix=>$data["garantie"]]);
                        $success=true;
                    }
                    if(isset($data["fournisseur"])){
                        Licence::where("reference".$this->prefix,$id)->update(["reference_fournisseur"=>$data["fournisseur"]]);
                        $success=true;
                    }
                    if($success){
                        Licence::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Licence modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
        }
        public function del($id){
            $count=Licence::where("reference".$this->prefix,$id)->count();
            $deleted=false;
            if($count>0){
                // Calcule::where("reference".$this->prefix,$id)->delete();
                if(Licence::where("reference".$this->prefix,$id)->delete()) $deleted=true;
            }
            if($deleted){
                $this->insertActivity("DELETE",$id);
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Licence supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function getClient($data){
            $items=json_decode(json_encode($data),true);
            $i=0;
            foreach($items as $item){
                $bordereau=json_decode(Bordereau::where("reference_bordereau",$item["bordereau"]["reference"] ?? $item["reference_bordereau"])
                    ->get()->toJSON(),true);
                
                if(isset($bordereau[0]["reference_proforma"])){
                    $proforma=json_decode(Proforma::where("reference_proforma",$bordereau[0]["proforma"]["reference"] ?? $bordereau[0]["reference_proforma"])
                        ->get()->toJSON(),true);
                        
                        if(isset($proforma[0]["reference_client"])){
                        $client=json_decode(Client::where("reference_client",$proforma[0]["reference_client"] ?? $proforma[0]["client"]["reference"])
                        ->get()->toJSON(),true);
                        $client=$this->clean($client,"_client",false);
                        
                        $items[$i]["client"]=isset($client[0])?$client[0]:[];
                    }
                }
                $i++;
            }
            return $items;
        }
        public function countMonthsCostumed($licences){
            $i=0;
            foreach ($licences as $item) {
                // $date=explode(" ",$item["created_at"])[0];
                // $date=[...explode(":",explode(" ",$item["created_at"])[1]),...explode("-",$date)];
                // $diffs=(mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"))
                //  - mktime($date[0],$date[1],$date[2],$date[3],$date[4],$date[5]))/30;
                $licences[$i]["mois_average"]=0;
                $i++;
            }
            return $licences;
        }
        public function to_equipement($equipement){
            $list=Tolicence::select("reference_equipement as id")->where("reference_equipement",$equipement)->get();
            $list=json_decode(json_encode($list),true);
            $keys=[];
            foreach ($list as $value) {
                $keys[]=$value["id"];
            }
            $licences=Licence::whereNotIn("reference_licence",$keys)->get();
            $licences=json_decode(json_encode($licences),true);
            foreach ($licences as $key => $item) {
                $licences[$key]=["reference"=>$item["reference_licence"],"content"=>$item["reference_licence"]." - ".$item["libelle_licence"]];
            }
            return response([
                "status"=>200,
                "data"=>$licences
            ],200);
        }
    }
?>