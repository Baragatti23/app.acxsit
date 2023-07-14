<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
use App\Models\Bordereau;
use Illuminate\Http\Request;
    use App\Models\Vendreequipement;
use App\Models\Livrer;
use App\Models\Proforma;

    class VendreequipementController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["proforma","utilisateur"];
        private $prefix="_vendreequipement";
        private $table="vendreequipements";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Vendreequipement(),$id);
            if(isset($params["status"]) && $params["status"]=200){
                return $params;
            }
            $data=$this->executeQuery(new Vendreequipement(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference_proforma","reference_equipement"]);
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Vendreequipement();
            if(
                isset(
                    $data["reference_proforma"]
                    ,$data["reference_equipement"]
                    ,$data["gpl_equipement"]
                    ,$data["discount"]
                    ,$data["unites"]
                    ,$data["transport"]
                    ,$data["marge"]
                )
            ){
                if($data["currency"]=="USD"){
                    $divise=550;
                }elseif($data["currency"]=="EUR"){
                    $divise=650;
                }else{
                    $divise=1;
                }
                $tva=18;
                $douane=40;
                $element->{"reference_equipement"}=$data["reference_equipement"];
                $element->{"reference_proforma"}=$data["reference_proforma"];
                $element->{"currency_value".$this->prefix}=$divise;
                $element->{"discount".$this->prefix}=$data["discount"];
                $element->{"gpl_equipement".$this->prefix}=intval(round($data["gpl_equipement"]*$divise));
                $element->{"transport".$this->prefix}=intval(round($data["transport"]*$divise));
                $element->{"unites".$this->prefix}=intval($data["unites"]);
                $element->{"marge".$this->prefix}=$data["marge"];
                $element->{"currency".$this->prefix}=$data["currency"];
                $element->{"douane_percent".$this->prefix}=$douane;
                $element->{"tva_percent".$this->prefix}=$tva;
                $element->{"reference_type"}=$data["reference_type"];
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",[$element->{"reference_proforma"},$element->{"reference_equipement"},]);
                $element->save();
                return[
                    "status"=>200,
                    "id"=>[
                        "proforma"=>$element->{"reference_proforma"},
                        "equipement"=>$element->{"reference_equipement"}
                    ],
                    "success"=>"Equipement ajouté"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Equipement non enregistré"
                ];
            }
        }
        public function calcules($id_proforma,$id_element){
            // CALCULES
            $count=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->count();
            if($count!==0){
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["gpl".$this->prefix=>$last->{"gpl_equipement".$this->prefix}*(1-$last->{"discount".$this->prefix}/100)]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["douane".$this->prefix=>($last->{"gpl".$this->prefix}+$last->{"transport".$this->prefix})*(1+$last->{"douane_percent".$this->prefix}/100)]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["prix_achat".$this->prefix=>$last->{"gpl".$this->prefix}+$last->{"transport".$this->prefix}+$last->{"douane".$this->prefix}]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["prix_vente".$this->prefix=>$last->{"gpl_equipement".$this->prefix}*(1-$last->{"marge".$this->prefix}/100)]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["montant_total_ht".$this->prefix=>$last->{"prix_vente".$this->prefix}*$last->{"unites".$this->prefix}]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["montant_unitaire_ht".$this->prefix=>$last->{"prix_vente".$this->prefix}]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["tva".$this->prefix=>(1-$last->{"tva_percent".$this->prefix}/100)*$last->{"montant_total_ht".$this->prefix}]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["montant_total_ttc".$this->prefix=>$last->{"montant_total_ht".$this->prefix}+$last->{"tva".$this->prefix}]);
                $last=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->first();
                Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)
                ->update(["montant_unitaire_ttc".$this->prefix=>$last->{"montant_total_ttc".$this->prefix}/$last->{"unites".$this->prefix}]);
                return true;
            }else{
                return false;
            }
        }
        public function inverse_calcules($elements){
            $i=0;
            foreach ($elements as $item) {
                $elements[$i]["gpl_equipement"]=$item["gpl_equipement"]/$item["currency_value"];
                $elements[$i]["gpl"]=$item["gpl"]/$item["currency_value"];
                $elements[$i]["transport"]=$item["transport"]/$item["currency_value"];
                $elements[$i]["douane"]=$item["douane_percent"];
                $elements[$i]["tva"]=$item["tva_percent"];
                $i++;
            }
            return $elements;
        }
        public function put($id_proforma,$id_equipement,Request $request){
            $data = json_decode($request->getContent(),true);
            if(isset($id_proforma,$id_equipement)){
                $success=false;
                $count=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)->count();
                if($count>0){
                    $element=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)->first();
                    $currency=$element->currency;
                    $divise=1;
                    if(isset($data["currency"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["currency".$this->prefix=>$data["currency"]]);
                        $currency=$data["currency"];
                        $success=true;
                    }
                    if($currency=="USD") $divise=550;
                    elseif($currency=="USD") $divise=650;
                    if(isset($data["discount"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["discount".$this->prefix=>$data["discount"]]);
                        $success=true;
                    }
                    if(isset($data["gpl_equipement"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["gpl_equipement".$this->prefix=>$data["gpl_equipement"]*$divise]);
                        $success=true;
                    }
                    if(isset($data["transport"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["transport".$this->prefix=>$data["transport"]*$divise]);
                        $success=true;
                    }
                    if(isset($data["marge"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["marge".$this->prefix=>$data["marge"]]);
                        $success=true;
                    }
                    if(isset($data["unites"])){
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["unites".$this->prefix=>$data["unites"]]);
                        $success=true;
                    }
                    if($success){
                        $this->calcules($id_proforma,$id_equipement);
                        Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)
                        ->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",[$id_proforma,$id_equipement]);
                        return[
                            "status"=>200,
                            "id"=>[
                                "proforma"=>$id_proforma,
                                "equipement"=>$id_equipement
                            ],
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
        public function del($id_proforma,$id_element){
            $count=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->count();
            $calcule=false;
            $delete=true;
            if($count>0){
                $bordereaux=Bordereau::where("reference_proforma",$id_proforma)->where("reference_estado","STA0002")->get();
                foreach ($bordereaux as $border) {
                    if(Livrer::where("reference_bordereau",$border->reference_bordereau)->where("reference_equipement",$id_element)->count()!==0){
                        $delete=false;
                        break;
                    }
                }
                if($delete){
                    $bordereaux=Bordereau::where("reference_proforma",$id_proforma)->get();
                    foreach ($bordereaux as $border) {
                        Livrer::where("reference_bordereau",$border->reference_bordereau)->where("reference_equipement",$id_element)->delete();
                        Bordereau::where("reference_bordereau",$border->reference_bordereau)->update(["updated_at_bordereau"=>date("Y-m-d h:i:s")]);
                        if(Livrer::where("reference_bordereau",$border->reference_bordereau)->count()===0){
                            Bordereau::where("reference_bordereau",$border->reference_bordereau)->update(["reference_estado"=>"STA0001"]);
                        }
                    }
                    Proforma::where("reference_proforma",$id_proforma)->update(["updated_at_proforma"=>date("Y-m-d h:i:s")]);
                    if(Vendreequipement::where("reference_proforma",$id_proforma)->count()-1===0){
                        Proforma::where("reference_proforma",$id_proforma)->update(["reference_stade"=>"STA0001"]);
                    };
                    $calcule=Vendreequipement::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_element)->delete();
                }
            }
            if($calcule){
                $this->insertActivity("DELETE",[$id_proforma,$id_element]);
                return[
                    "status"=>200,
                    "id"=>[
                        "reference_proforma"=>$id_proforma,
                        "reference_equipement"=>$id_element
                    ],
                    "success"=>"Equipement supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>[
                        "reference_proforma"=>$id_proforma,
                        "reference_equipement"=>$id_element
                    ],
                    "error"=>"Operation non réussi!"
                ];
            }
        }
    }
?>