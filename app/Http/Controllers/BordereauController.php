<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Bordereau;
    use Illuminate\Http\Request;
    use App\Models\Client;
    use App\Models\Livrer;
    use App\Models\Proforma;
    use App\Models\Estado;
use App\Models\Licence;

    class BordereauController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["proforma","utilisateur","estado"];
        private $prefix="_bordereau";
        private $table="bordereaus";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Bordereau(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Bordereau(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->getClient(json_decode(json_encode($data["data"]),true));
            }
            return $data;
        }
        public function post(Request $request){
            $data = json_decode($request->getContent(),true);
            $element=new Bordereau();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["proforma"],$data["noms_livreur"],$data["contact_livreur"],$data["items"])){
                $element->{"reference_proforma"}=$data["proforma"];
                $element->{"nom_livreur".$this->prefix}=$data["noms_livreur"];
                $element->{"contact_livreur".$this->prefix}=$data["contact_livreur"];
                $element->{"nom_recepteur".$this->prefix}=$data["noms_recepteur"] ?? "";
                $element->{"contact_recepteur".$this->prefix}=$data["contact_recepteur"] ?? "";
                $element->{"reference_estado"}="EST0001";
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                if($element){
                    foreach($data["items"] as $item){
                        $livrer=new Livrer();
                        $prefix="_livrer";
                        $inserteds=[];
                        if(isset($item["qty"],$item["equipement"])){
                            $livrer->{"reference_equipement"}=$item["equipement"];
                            $livrer->{"reference_bordereau"}=$element->{"reference".$this->prefix};
                            $livrer->{"unites".$prefix}=$item["qty"];
                            $livrer->{"reference_utilisateur"}="UTI0001";
                            $livrer->{"created_at".$prefix}=date("Y-m-d H:i:s");
                            $livrer->{"updated_at".$prefix}=date("Y-m-d H:i:s");
                            $livrer->save();
                            if($livrer) $inserteds[]=true;
                        }
                    }
                    if(count($inserteds)==count($data["items"])){
                        return[
                            "status"=>200,
                            "id"=>$element->{"reference".$this->prefix},
                            "success"=>"Bordereau enregistré avec succés"
                        ];
                    }elseif(count($inserteds)>0 && count($inserteds)<count($data["items"])){
                        return[
                            "status"=>200,
                            "id"=>$element->{"reference".$this->prefix},
                            "success"=>"Toute les equipements n'ont pas été inserés"
                        ];
                    }elseif(count($inserteds)==0 && count($data["items"])>0){
                        return[
                            "status"=>200,
                            "id"=>$element->{"reference".$this->prefix},
                            "success"=>"Aucun equipement n'a pas été inseré"
                        ];
                    }
                }else{
                    return[
                        "status"=>400,
                        "error"=>"proforma non enregistré"
                    ];
                }
            }else{
                return[
                    "status"=>400,
                    "error"=>"proforma non enregistré"
                ];
            }
        }
        public function del($id){
            $count=Bordereau::where("reference_bordereau",$id)->count();
            $bordereau=false;
            $livrer=false;
            if($count>0){
                $livrers=Livrer::where("reference_bordereau",$id)->delete();
                $licences=Licence::where("reference_bordereau",$id)->delete();
                $bordereau=Bordereau::where("reference_bordereau",$id)->delete();
            }
            if($bordereau){
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Bordereau supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function changeStatus($id,$status){
            $stade=Estado::where("reference_estado",$status)->count();
            $update=false;
            if($stade>0){
                $update=Proforma::where("reference_bordereau",$id)->update(["reference_estado" => $status]);
            }
            if($update){
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Status modifié avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function downloadPDF(){
            
        }
        public function getPDF(){

        }
        public function getClient($users){
            $i=0;
            foreach ($users as $item) {
                $users[$i]["client"]=[];
                $proformas=json_decode(Proforma::where("reference_proforma",$item["proforma"]["reference"] ?? $item["reference_proforma"])
                ->get()->toJSON(),true);
                if(count($proformas)>0){
                    $proforma=$proformas[0];
                    $clients=json_decode(Client::where("reference_client",$proforma["reference_client"])
                    ->get()->toJSON(),true);
                    if(isset($clients[0])){
                        $users[$i]["client"]=$this->clean($clients,"_client")[0];
                    }
                }
                $i++;
            }
            return $users;
        }
    }
?>