<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Bordereau;
    use App\Models\Calcule;
use App\Models\Livrer;
use App\Models\Proforma;
    use App\Models\Stade;
    use Illuminate\Http\Request;

    class ProformaController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["client","utilisateur"];
        private $prefix="_proforma";
        private $table="proformas";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Proforma(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Proforma(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->getTotals(json_decode(json_encode($data["data"]),true));
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $alphabet=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","Y","Z"];
            $element=new Proforma();
            $start=date("Y:m:1 00:00:00");
            $end=date("Y:m:j 23:59:59");
            $total=Proforma::whereBetween("created_at_proforma",[$start,$end])->count()+1;
            $elems=json_decode(Proforma::whereBetween("created_at_proforma",[$start,$end])->orderBy("created_at_proforma","desc")->orderBy("reference_proforma","desc")->get()->toJson(),true);
            if(count($elems)==0){
                $total=1;
                $element->{"reference".$this->prefix}=$alphabet[((int)date("m"))-1].substr(date('Y'),-2)."-".substr("000".$total,-3);
            }else{
                $total=intval(explode("-",$elems[0]["reference_proforma"])[1])+1;
                $element->{"reference".$this->prefix}=$alphabet[((int)date("m"))-1].substr(date('Y'),-2)."-".substr("000".$total,-3);
            }
            if(isset($data["subject"],$data["client"],$data["validate"])){
                $element->{"sujet".$this->prefix}=$data["subject"];
                $element->{"validate".$this->prefix}=$data["validate"];
                $element->{"garantie".$this->prefix}=$data["warranty"] ?? "";
                $element->{"livraison".$this->prefix}=$data["livraison"] ?? "";
                $element->{"reference_client"}=$data["client"];
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"reference_stade"}="STA0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"proforma enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"proforma non enregistré"
                ];
            }
        }
        public function getTotals($proformas){
            $i=0;
            foreach ($proformas as $item) {
                $proformas[$i]["total_ht"]=Calcule::where("reference_proforma",$item["reference"])
                ->sum("montant_total_ht_calcule");
                $proformas[$i]["total_ttc"]=Calcule::where("reference_proforma",$item["reference"])
                ->sum("montant_total_ttc_calcule");
                
                $i++;
            }
            return $proformas;
        }
        public function del($id){
            $count=Proforma::where("reference_proforma",$id)->count();
            $proforma=false;
            $bordereau=false;
            if($count>0){
                Calcule::where("reference_proforma",$id)->delete();
                $bordereau=Bordereau::select("reference_bordereau")->where("reference_proforma",$id)->get();
                foreach($bordereau as $item){
                    Livrer::where("reference_bordereau",$item->reference_bordereau)->delete();
                }
                $bordereau=Bordereau::where("reference_proforma",$id)->delete();
                $proforma=Proforma::where("reference_proforma",$id)->delete();
            }
            if($proforma){
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Proforma supprimée avec succés"
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
            $stade=Stade::where("reference_stade",$status)->count();
            if($stade>0){
                $proforma=Proforma::where("reference_proforma",$id)->update(["reference_stade" => $status]);
            }
            if($stade>0){
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
        public function getPDF($id){
            if(Proforma::where("reference_proforma",$id)->count()==1){
                $proforma=$this->get($id)["data"][0];
                $proforma=$this->getTotals([$proforma])[0];
                $params=$this->validateParams(new Calcule(),$id);
                $params["orderByArray"][0]="created_at_calcule";
                $params["linkToArray"][0]="reference_proforma";
                $params["operatorToArray"][0]="=";
                $params["compareToArray"][0]=$proforma["reference"];
                $calcules=$this->executeQuery(new calcule(),$params,"")["data"];
                $calcules=$this->clean(json_decode(json_encode($calcules),true),"_calcule");
                $this->create_proforma_pdf($proforma,$calcules);
            }else{
                
            }
        }
        public function downloadPDF($id){
            if(Proforma::where("reference_proforma",$id)->count()==1){
                $proforma=$this->get($id)["data"][0];
                $proforma=$this->getTotals([$proforma])[0];
                $params=$this->validateParams(new Calcule(),$id);
                $params["orderByArray"][0]="created_at_calcule";
                $params["linkToArray"][0]="reference_proforma";
                $params["operatorToArray"][0]="=";
                $params["compareToArray"][0]=$proforma["reference"];
                $calcules=$this->executeQuery(new calcule(),$params,"")["data"];
                $calcules=$this->clean(json_decode(json_encode($calcules),true),"_calcule");
                $this->create_proforma_pdf($proforma,$calcules,true);
            }else{

            }
        }
    }
?>