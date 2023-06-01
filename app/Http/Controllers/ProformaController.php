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
                $data["data"]=$this->set_ttc_ht_totals(json_decode(json_encode($data["data"]),true));
                if(empty($id)){
                    if($params["betweenStartValue"]){
                        $data=[...$data,...[
                            "total"=>Proforma::whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "pending"=>Proforma::where("reference_stade","STA0001")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "validated"=>Proforma::where("reference_stade","STA0002")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "rejected"=>Proforma::where("reference_stade","STA0003")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "sended"=>Proforma::where("reference_stade","STA0004")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "incompleted"=>Proforma::where("reference_stade","STA0005")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count(),
                            "completed"=>Proforma::where("reference_stade","STA0006")
                            ->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]])
                            ->count()
                        ]];
                    }else{
                        $data=[...$data,...[
                            "total"=>Proforma::count(),
                            "pending"=>Proforma::where("reference_stade","STA0001")->count(),
                            "validated"=>Proforma::where("reference_stade","STA0002")->count(),
                            "rejected"=>Proforma::where("reference_stade","STA0003")->count(),
                            "sended"=>Proforma::where("reference_stade","STA0004")->count(),
                            "incompleted"=>Proforma::where("reference_stade","STA0005")->count(),
                            "completed"=>Proforma::where("reference_stade","STA0006")->count()
                        ]];
                    }
                }else{
                    if(!empty($data["data"])){
                       
                        $data["data"][0]["calcules"]=$this->cleanPrefix(json_decode(json_encode(Calcule::where(...$id)->get()),true),"_calcule");
                    }else{
                        $data["data"][0]["calcules"]=[];
                    }
                }
            }
            return $data;
        }
        public function to_create_bordereaus(){
            $list=Proforma::select("reference_proforma as reference","sujet_proforma as content")
            ->where("reference_stade","STA0002")->orWhere("reference_stade","STA0005")->get();
            $data=json_decode(json_encode($list),true);
            foreach ($data as $key=>$item) {
                $count=Calcule::where("reference_proforma",$item["reference"])->count();
                if($count==0){
                    unset($data[$key]);
                }else{
                    $data[$key]["content"]=$item["reference"]." - ".$item["content"];
                }
            }
            return response()->json([
                "status"=>200,
                "data"=>$data
            ],200);
        }
        public function get_status($id){
            $proforma=Proforma::where("reference_proforma",$id)->first();
            $data=Stade::where("reference_stade","!=",$proforma["reference_stade"])
            ->where("reference_stade","!=","STA0005")
            ->where("reference_stade","!=","STA0006");
            $count=Calcule::where("reference_proforma",$proforma["reference_proforma"])->count();
            if($count==0){
                $data=$data->where("reference_stade","!=","STA0002");
            }
            $data=$data->get();
            $data=$this->cleanPrefix(json_decode(json_encode($data),true),"_stade");
            return response()->json([
                "status"=>200,
                "data"=>$data
            ],200);
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $alphabet=["A","B","C","D","E","F","G","H","I","J","K","L"];
            $start=date("Y:m:1 00:00:00");
            $end=date("Y:m:j 23:59:59");
            $total=Proforma::whereBetween("created_at".$this->prefix,[$start,$end])->count();
            $last=Proforma::whereBetween("created_at".$this->prefix,[$start,$end])->orderBy("created_at".$this->prefix,"DESC")->first();
            if(!empty($last)){
                $last_code=explode("-",$last["reference".$this->prefix]);
                if(count($last_code)>1){
                    $last_code=intval($last_code[1]);
                    $total=$last_code;
                }
            }
            do{
                $total=$total+1;
                $code=$alphabet[intval(date("m")-1)].substr("0".substr(date("Y"),-2),-3)."-".substr("00".$total,-3);
                $count=Proforma::where("reference".$this->prefix,$code)->count();
            }while($count!=0);
            $element=new Proforma();
            $element->{"reference".$this->prefix}=$code;
            if(isset($data["subject"],$data["client"],$data["validity"])){
                $element->{"sujet".$this->prefix}=$data["subject"];
                $element->{"validate".$this->prefix}=$data["validity"];
                $element->{"garantie".$this->prefix}=$data["warranty"] ?? 0;
                $element->{"livraison".$this->prefix}=$data["livraison"] ?? 0;
                $element->{"modalite".$this->prefix}=$data["modality"] ?? "";
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
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            // echo json_encode($data);
            // exit;
            if(isset($id)){
                $success=false;
                $count=Proforma::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["subject"])){
                        Proforma::where("reference".$this->prefix,$id)->update(["sujet".$this->prefix=>$data["subject"]]);
                        $success=true;
                    }
                    if(isset($data["delivery"])){
                        Proforma::where("reference".$this->prefix,$id)->update(["livraison".$this->prefix=>$data["delivery"]]);
                        $success=true;
                    }
                    if(isset($data["warranty"])){
                        Proforma::where("reference".$this->prefix,$id)->update(["garantie".$this->prefix=>$data["warranty"]]);
                        $success=true;
                    }
                    if(isset($data["validity"])){
                        Proforma::where("reference".$this->prefix,$id)->update(["validate".$this->prefix=>$data["validity"]]);
                        $success=true;
                    }
                    if(isset($data["modality"])){
                        Proforma::where("reference".$this->prefix,$id)->update(["modalite".$this->prefix=>$data["modality"]]);
                        $success=true;
                    }
                    if(isset($data["status"]) || isset($data["stade"])){
                        $count=Stade::where("reference_stade",$data["status"] ?? $data["stade"])->count();
                        if($count>0){
                            Proforma::where("reference".$this->prefix,$id)->update(["reference_stade"=>$data["status"] ?? $data["stade"]]);
                            $success=true;
                        }
                    }
                    if($success){
                        Proforma::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Proforma modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
        }
        public function set_ttc_ht_totals($proformas){
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
                $proforma=$this->set_ttc_ht_totals([$proforma])[0];
                $calcules=$proforma["calcules"];
                $calcules=$this->getForeignRecords($calcules,'calcules');
                $calcules=$this->cleanPrefix(json_decode(json_encode($calcules),true),"_calcule");
                // echo json_encode($calcules);
                // exit;
                $this->create_proforma_pdf($proforma,$calcules);
            }else{
                
            }
        }
        public function downloadPDF($id){
            if(Proforma::where("reference_proforma",$id)->count()==1){
                $proforma=$this->get($id)["data"][0];
                $proforma=$this->set_ttc_ht_totals([$proforma])[0];
                $params=$this->validateParams(new Calcule(),$id);
                $params["orderByArray"][0]="created_at_calcule";
                $params["linkToArray"][0]="reference_proforma";
                $params["operatorToArray"][0]="=";
                $params["compareToArray"][0]=$proforma["reference"];
                $calcules=$this->executeQuery(new calcule(),$params,"")["data"];
                $calcules=$this->cleanPrefix(json_decode(json_encode($calcules),true),"_calcule");
                $this->create_proforma_pdf($proforma,$calcules,true);
            }else{

            }
        }
    }
?>