<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use Illuminate\Http\Request;
    use App\Models\Stagere;

    class StagereController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        // private $foreign_columns=["Stagere","utilisateur"];
        private $prefix="_stagere";
        private $table="stageres";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Stagere(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["total"]=Stagere::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Stagere();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["nom"],$data["prenoms"],$data["telephone"],
                $data["email"],$data["type"],$data["sexe"],$data["debut"],
                $data["fin"])){
                $element->{"nom".$this->prefix}=$data["nom"];
                $element->{"prenoms".$this->prefix}=$data["prenoms"];
                $element->{"telephone".$this->prefix}=$data["telephone"];
                $element->{"email".$this->prefix}=$data["email"];
                $element->{"type".$this->prefix}=$data["type"];
                $element->{"date_debut".$this->prefix}=$data["debut"];
                $element->{"date_fin".$this->prefix}=$data["fin"];
                $element->{"sexe".$this->prefix}=$data["sexe"];
                if(isset($data["ecole"])) $element->{"ecole".$this->prefix}=$data["ecole"];
                else $element->{"ecole".$this->prefix}="";
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Stagere enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Satge non enregistré"
                ];
            }
        }
        public function del($id){
            $deleted=Stagere::where("reference".$this->prefix,$id)->delete();
            if($deleted){
                $this->insertActivity("DELETE",$id);
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Stagere supprimée avec succés"
                ];
            }else{
                return[
                    "status"=>500,
                    "id"=>$id,
                    "error"=>"Operation non réussi!"
                ];
            }
        }
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            if(isset($id)){
                $success=false;
                $count=Stagere::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["nom"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["nom".$this->prefix=>$data["nom"]]);
                        $success=true;
                    }
                    if(isset($data["prenoms"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["prenoms".$this->prefix=>$data["prenoms"]]);
                        $success=true;
                    }
                    if(isset($data["sexe"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["sexe".$this->prefix=>$data["sexe"]]);
                        $success=true;
                    }
                    if(isset($data["type"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["type".$this->prefix=>$data["type"]]);
                        $success=true;
                    }
                    if(isset($data["ecole"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["ecole".$this->prefix=>$data["ecole"]]);
                        $success=true;
                    }
                    if(isset($data["date_fin"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["date_fin".$this->prefix=>$data["date_fin"]]);
                        $success=true;
                    }
                    if(isset($data["telephone"])){
                        Stagere::where("reference".$this->prefix,$id)->update(["telephone".$this->prefix=>$data["telephone"]]);
                        $success=true;
                    }
                    if($success){
                        Stagere::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Stagere modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
        }
    }
?>