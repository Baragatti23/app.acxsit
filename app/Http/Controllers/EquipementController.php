<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
use App\Models\Calcule;
use App\Models\Equipement;
use App\Models\Licence;
use App\Models\Tolicence;
use App\Models\Vendreequipement;
use Illuminate\Http\Request;

    class EquipementController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_equipement";
        private $table="equipements";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Equipement(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->countLicences(json_decode(json_encode($data["data"]),true));
                $data["total"]=Equipement::count();
                $data["total_products"]=Equipement::count()+Licence::count();
                $data["total_equipements"]=Equipement::count();
                $data["total_licenses"]=Licence::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Equipement();
            $element->{"reference".$this->prefix}="EQU".$this->generateID();
            if(isset($data["libelle"])){
                $element->{"designation".$this->prefix}=$data["libelle"];
                $element->{"categorie".$this->prefix}=$data["category"];
                $element->{"gpl".$this->prefix}=$data["gpl"] || 0;
                $element->{"prix_achat".$this->prefix}=$data["amount"] || 0;
                if(isset($data["price_sell"])) $element->{"prix_vente".$this->prefix}=$data["price_sell"] || 0;
                if(isset($data["stock"])) $element->{"stock".$this->prefix}=$data["stock"] || 0;
                $element->{"licence".$this->prefix}=$data["license"] ?? "N";
                if(isset($data["supplier"])) $element->{"reference_fournisseur"}=$data["supplier"];
                else $element->{"reference_fournisseur"}="FOU0001";
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Equipement enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Client non enregistré"
                ];
            }
        }
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            // echo json_encode($data);
            // exit;
            if(isset($id)){
                $success=false;
                $count=Equipement::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["designation"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["designation".$this->prefix=>$data["designation"]]);
                        $success=true;
                    }
                    if(isset($data["categorie"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["reference_categorie"=>$data["categorie"]]);
                        $success=true;
                    }
                    if(isset($data["fournisseur"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["reference_fournisseur"=>$data["fournisseur"]]);
                        $success=true;
                    }
                    if(isset($data["prix_vente"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["prix_vente".$this->prefix=>$data["prix_vente"]]);
                        $success=true;
                    }
                    if(isset($data["prix_achat"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["prix_achat".$this->prefix=>$data["prix_achat"]]);
                        $success=true;
                    }
                    if(isset($data["stock"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["stock".$this->prefix=>$data["stock"]]);
                        $success=true;
                    }
                    if(isset($data["gpl"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["gpl".$this->prefix=>$data["gpl"]]);
                        $success=true;
                    }
                    if(isset($data["caracteristiques"])){
                        Equipement::where("reference".$this->prefix,$id)->update(["caracteristiques".$this->prefix=>$data["caracteristiques"]]);
                        $success=true;
                    }
                    if($success){
                        Equipement::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Equipement modifié avec succés"
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
            $count=Equipement::where("reference".$this->prefix,$id)->count();
            $deleted=false;
            if($count>0){
                // Calcule::where("reference".$this->prefix,$id)->delete();
                if(Equipement::where("reference".$this->prefix,$id)->delete()) $deleted=true;
            }
            if($deleted){
                $this->insertActivity("DELETE",$id);
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Equipement supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function to_create_calcules($id){
            $list=Equipement::select("reference_equipement as reference","designation_equipement as content")
            ->get();
            $data=json_decode(json_encode($list),true);
            $return=[];
            foreach ($data as $key=>$item) {
                if(Vendreequipement::where("reference_proforma",$id)->where("reference_equipement",$item["reference"])->count()==0){
                    $data[$key]["content"]=$item["reference"]." - ".$item["content"];
                    $return[]=$data[$key];
                }
            }
            return response()->json([
                "status"=>200,
                "data"=>$return
            ],200);
        }
        public function countLicences($data){
            // echo json_encode($data);
            foreach ($data as $key => $item) {
                $data[$key]["total_licences"]=Tolicence::where("reference_equipement",$item["reference"])->count();
            }
            return $data;
        }
    }
?>