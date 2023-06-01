<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use App\Models\Licence;
    use Illuminate\Http\Request;
    use App\Models\Proforma;
    use App\Models\Bordereau;
    use App\Models\Client;
use App\Models\Equipement;

    class LicenceController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["utilisateur"];
        private $prefix="_licence";
        private $table="licences";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Licence(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Licence(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->getClient($data["data"]);
                $data["data"]=$this->countMonthsCostumed($data["data"]);
                $data["total"]=Licence::count();
                $data["total_products"]=Licence::count()+Equipement::count();
                $data["total_licenses"]=Licence::count();
                $data["total_equipements"]=Equipement::count();
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Licence();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["libelle"],$data["mois"])){
                $element->{"mois".$this->prefix}=$data["mois"];
                $element->{"designation".$this->prefix}=$data["libelle"];
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
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
    }
?>