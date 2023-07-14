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
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Bordereau(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->setCountLivrers(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
                if($params["betweenStartValue"]){
                    $data=[...$data,...[
                        "total"=>Bordereau::whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                        ->count(),
                        "pending"=>Bordereau::where("reference_estado","EST0001")
                        ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                        ->count(),
                        "delivered"=>Bordereau::where("reference_estado","EST0002")
                        ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                        ->count(),
                        "rejected"=>Bordereau::where("reference_estado","EST0003")
                        ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                        ->count()
                    ]];
                }else{
                    $data=[...$data,...[
                        "total"=>Bordereau::count(),
                        "pending"=>Bordereau::where("reference_estado","EST0001")->count(),
                        "delivered"=>Bordereau::where("reference_estado","EST0002")->count(),
                        "rejected"=>Bordereau::where("reference_estado","EST0003")->count()
                    ]];
                }
            }
            return $data;
        }
        public function stats(){
            return response()->json([
                "status"=>200,
                "data"=>[
                    "total"=>Bordereau::count(),
                    "pending"=>Bordereau::where("reference_estado","EST0001")->count(),
                    "delivered"=>Bordereau::where("reference_estado","EST0002")->count(),
                    "rejected"=>Bordereau::where("reference_estado","EST0003")->count()
                ]
            ],200);
        }
        public function get_status($id){
            $bordereau=Bordereau::where("reference_bordereau",$id)->first();
            $count=0;
            if($bordereau) $count=Livrer::where("reference_bordereau",$bordereau["reference_bordereau"])->count();
            if($count==0){
                $data=Estado::where("reference_estado","!=","EST0002")
                ->where("reference_estado","!=",$bordereau["reference_estado"])->get();
            }else{
                $data=Estado::where("reference_estado","!=",$bordereau["reference_estado"])->get();
            }
            $data=$this->cleanPrefix(json_decode(json_encode($data),true),"_estado");
            return response()->json([
                "status"=>200,
                "data"=>$data
            ],200);
        }
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            // echo json_encode($data);
            // exit;
            if(isset($id)){
                $success=false;
                $count=Bordereau::where("reference_bordereau",$id)->count();
                if($count>0){
                    if(isset($data["nom_livreur"])){
                        Bordereau::where("reference_bordereau",$id)->update(["nom_livreur".$this->prefix=>$data["nom_livreur"]]);
                        $success=true;
                    }
                    if(isset($data["contact_livreur"])){
                        Bordereau::where("reference_bordereau",$id)->update(["contact_livreur".$this->prefix=>$data["contact_livreur"]]);
                        $success=true;
                    }
                    if(isset($data["nom_recepteur"])){
                        Bordereau::where("reference_bordereau",$id)->update(["nom_recepteur".$this->prefix=>$data["nom_recepteur"]]);
                        $success=true;
                    }
                    if(isset($data["contact_recepteur"])){
                        Bordereau::where("reference_bordereau",$id)->update(["contact_recepteur".$this->prefix=>$data["contact_recepteur"]]);
                        $success=true;
                    }
                    if(isset($data["status"]) || isset($data["estado"])){
                        $count=Estado::where("reference_estado",$data["status"] ?? $data["estado"])->count();
                        if($count>0){
                            Bordereau::where("reference_bordereau",$id)->update(["reference_estado"=>$data["status"] ?? $data["estado"]]);
                            $success=true;
                        }
                    }
                    if($success){
                        Bordereau::where("reference_bordereau",$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Bordereau modifié avec succés"
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
            $data = json_decode($request->getContent(),true);
            $element=new Bordereau();
            $element->{"reference".$this->prefix}="BL-".$this->generateCode("bordereaus");
            if(isset($data["reference_proforma"])){
                $element->{"reference_proforma"}=$data["reference_proforma"];
                if(isset($data["nom_livreur"])) $element->{"nom_livreur".$this->prefix}=$data["nom_livreur"];
                if(isset($data["contact_livreur"])) $element->{"contact_livreur".$this->prefix}=$data["contact_livreur"];
                if(isset($data["nom_recepteur"])) $element->{"nom_recepteur".$this->prefix}=$data["nom_recepteur"] ?? "";
                if(isset($data["contact_recepteur"])) $element->{"contact_recepteur".$this->prefix}=$data["contact_recepteur"] ?? "";
                $element->{"reference_estado"}="EST0001";
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
                $element->save();
                if($element){
                    return[
                        "status"=>200,
                        "success"=>"proforma enregistré avec succés"
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
                // $licences=Licence::where("reference_bordereau",$id)->delete();
                $bordereau=Bordereau::where("reference_bordereau",$id)->delete();
            }
            if($bordereau){
                $this->insertActivity("DELETE",$id);
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
        public function setCountLivrers(array $bordereaux){
            $i=0;
            foreach ($bordereaux as $item) {
                $bordereaux[$i]["total_items"]=Livrer::where("reference_bordereau",$item["reference"])
                ->count();
                $i++;
            }
            return $bordereaux;
        }
        
    }
?>