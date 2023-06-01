<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use Illuminate\Http\Request;
    use App\Models\Calcule;

    class CalculeController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        private $foreign_columns=["proforma","utilisateur"];
        private $prefix="_calcule";
        private $table="calcules";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Calcule(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            return $this->executeQuery(new Calcule(),$params,$id);
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Calcule();
            if(
                isset(
                    $data["proforma"]
                    ,$data["equipement"]
                    ,$data["gpl_amount"]
                    ,$data["discount"]
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
                $element->{"reference_equipement"}=$data["equipement"];
                $element->{"reference_proforma"}=$data["proforma"];
                $element->{"discount_percent".$this->prefix}=$data["discount"];
                $element->{"gpl_equipement".$this->prefix}=$data["gpl_amount"]*$divise;
                $element->{"transport".$this->prefix}=$data["transport"]*$divise;
                $element->{"douane_percent".$this->prefix}=$data["douane"];
                $element->{"unites".$this->prefix}=$data["unites"];
                $element->{"marge_percent".$this->prefix}=$data["marge"];
                $element->{"currency".$this->prefix}=$data["currency"];
                $element->{"reference_type"}=$data["type"];
                $element->{"reference_utilisateur"}="UTI0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Equipement ajouté"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Equipement non enregistré"
                ];
            }
        }
        public function put($id_proforma,$id_equipement,Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $count=Calcule::where("reference_proforma",$id_proforma)->where("reference_equipement",$id_equipement)->count();
            if(
                isset(
                    $data["proforma"]
                    ,$data["equipement"]
                    ,$data["gpl_amount"]
                    ,$data["discount"]
                    ,$data["transport"]
                    ,$data["marge"]
                ) && $count>0
            ){
                if($data["currency"]=="USD"){
                    $divise=550;
                }elseif($data["currency"]=="EUR"){
                    $divise=650;
                }else{
                    $divise=1;
                }
                $element=Calcule::where("reference_proforma",$id_proforma)
                ->where("reference_equipement",$id_equipement)
                ->update(
                    ["discount_percent".$this->prefix=>$data["discount"],
                    "gpl_equipement".$this->prefix=>$data["gpl_amount"]*$divise,
                    "transport".$this->prefix=>$data["transport"]*$divise,
                    "douane_percent".$this->prefix=>$data["douane"],
                    "unites".$this->prefix=>$data["unites"],
                    "marge_percent".$this->prefix=>$data["marge"],
                    "currency".$this->prefix=>$data["currency"],
                    "reference_utilisateur"=>"UTI0001",
                    "updated_at".$this->prefix=>date("Y-m-d H:i:s")
                    ]
                );
                if($element){
                    return[
                        "status"=>200,
                        "id"=>[$id_proforma,$id_equipement],
                        "success"=>"Equipement ajouté"
                    ];
                }else{
                    return[
                        "status"=>400,
                        "error"=>"Equipement non enregistré"
                    ]; 
                }
            }else{
                return[
                    "status"=>400,
                    "error"=>"Equipement non enregistré"
                ];
            }
        }
    }
?>