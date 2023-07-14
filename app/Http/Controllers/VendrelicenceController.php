<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
use App\Models\Bordereau;
use Illuminate\Http\Request;
use App\Models\Livrer;
use App\Models\Proforma;
use App\Models\Vendrelicence;

class VendrelicenceController extends Controller{
    use Setting;
    // PROPERTIES ==============================
    private $foreign_columns=["proforma","utilisateur"];
    private $prefix="_vendrelicence";
    private $table="vendrelicences";
    
    // METHODS =================================
    public function get($id=null){
        $id=$id?["reference".$this->prefix,$id]:[];
        $params=$this->validateParams();
        if(isset($params["status"]) && $params["status"]=200){
            return $params;
        }
        $data=$this->executeQuery(new Vendrelicence(),$params,$id);
        if($data["status"]==200 && isset($data["data"])){
            $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference_proforma","reference_licence"]);
            $data["data"]=$this->inverse_calcules($data["data"]);
        }
        return $data;
    }
    public function post(Request $request){
        // $columns=$this->getTableColumns($this->table);
        $data = json_decode($request->getContent(),true);
        $element=new Vendrelicence();
        if(
            isset(
                $data["reference_proforma"]
                ,$data["reference_licence"]
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
            $element->{"reference_licence"}=$data["reference_licence"];
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
            $this->insertActivity("CREATE",[$element->{"reference_proforma"},$element->{"reference_licence"},]);
            $element->save();
            $this->calcules($data["reference_proforma"],$data["reference_licence"]);
            return[
                "status"=>200,
                "id"=>[
                    "proforma"=>$element->{"reference_proforma"},
                    "equipement"=>$element->{"reference_licence"}
                ],
                "success"=>"Licence ajouté"
            ];
        }else{
            return[
                "status"=>400,
                "error"=>"Licence non enregistré"
            ];
        }
    }
    public function put($id_proforma,$id_element,Request $request){
        $data = json_decode($request->getContent(),true);
        if(isset($id_proforma,$id_element)){
            $success=false;
            $count=Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)->count();
            if($count>0){
                $element=Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)->first();
                $currency=$element->currency;
                $divise=1;
                if(isset($data["currency"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["currency".$this->prefix=>$data["currency"]]);
                    $currency=$data["currency"];
                    $success=true;
                }
                if($currency=="USD") $divise=550;
                elseif($currency=="USD") $divise=650;
                if(isset($data["discount"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["discount".$this->prefix=>$data["discount"]]);
                    $success=true;
                }
                if(isset($data["gpl_equipement"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["gpl_equipement".$this->prefix=>$data["gpl_equipement"]*$divise]);
                    $success=true;
                }
                if(isset($data["transport"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["transport".$this->prefix=>$data["transport"]*$divise]);
                    $success=true;
                }
                if(isset($data["marge"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["marge".$this->prefix=>$data["marge"]]);
                    $success=true;
                }
                if(isset($data["unites"])){
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["unites".$this->prefix=>$data["unites"]]);
                    $success=true;
                }
                if($success){
                    $this->calcules($id_proforma,$id_element);
                    Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)
                    ->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                    $this->insertActivity("UPDATE",[$id_proforma,$id_element]);
                    return[
                        "status"=>200,
                        "id"=>[
                            "proforma"=>$id_proforma,
                            "equipement"=>$id_element
                        ],
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
    public function del($id_proforma,$id_element){
        $count=Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)->count();
        $calcule=false;
        $delete=true;
        if($count>0){
            $bordereaux=Bordereau::where("reference_proforma",$id_proforma)->where("reference_estado","STA0002")->get();
            foreach ($bordereaux as $border) {
                if(Livrer::where("reference_bordereau",$border->reference_bordereau)->where("reference_licence",$id_element)->count()!==0){
                    $delete=false;
                    break;
                }
            }
            if($delete){
                $bordereaux=Bordereau::where("reference_proforma",$id_proforma)->get();
                foreach ($bordereaux as $border) {
                    Livrer::where("reference_bordereau",$border->reference_bordereau)->where("reference_licence",$id_element)->delete();
                    Bordereau::where("reference_bordereau",$border->reference_bordereau)->update(["updated_at_bordereau"=>date("Y-m-d h:i:s")]);
                    if(Livrer::where("reference_bordereau",$border->reference_bordereau)->count()===0){
                        Bordereau::where("reference_bordereau",$border->reference_bordereau)->update(["reference_estado"=>"STA0001"]);
                    }
                }
                Proforma::where("reference_proforma",$id_proforma)->update(["updated_at_proforma"=>date("Y-m-d h:i:s")]);
                if(Vendrelicence::where("reference_proforma",$id_proforma)->count()-1===0){
                    Proforma::where("reference_proforma",$id_proforma)->update(["reference_stade"=>"STA0001"]);
                };
                $calcule=Vendrelicence::where("reference_proforma",$id_proforma)->where("reference_licence",$id_element)->delete();
            }
        }
        if($calcule){
            $this->insertActivity("DELETE",[$id_proforma,$id_element]);
            return[
                "status"=>200,
                "id"=>[
                    "reference_proforma"=>$id_proforma,
                    "reference_licence"=>$id_element
                ],
                "success"=>"Licence supprimée avec succés"
            ];
        }else{
            return[
                "status"=>500,
                "id"=>[
                    "reference_proforma"=>$id_proforma,
                    "reference_licence"=>$id_element
                ],
                "error"=>"Operation non réussi!"
            ];
        }
    }
    }
?>