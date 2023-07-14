<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
    use Illuminate\Http\Request;
    use App\Models\Formation;

    class FormationController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        // private $foreign_columns=["Formation","utilisateur"];
        private $prefix="_formation";
        private $table="formations";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Formation(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["total"]=Formation::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            $element=new Formation();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["libelle"],$data["domaine"],$data["duree"],$data["cout"])){
                $element->{"libelle".$this->prefix}=$data["libelle"];
                $element->{"domaine".$this->prefix}=$data["domaine"];
                $element->{"duree".$this->prefix}=$data["duree"];
                $element->{"cout".$this->prefix}=$data["cout"];
                $element->{"reference_utilisateur"}="UTS109083DOM";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $this->insertActivity("CREATE",$element->{"reference".$this->prefix});
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Formation enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Licence non enregistré"
                ];
            }
        }
        public function del($id){
            $deleted=Formation::where("reference".$this->prefix,$id)->delete();
            if($deleted){
                $this->insertActivity("DELETE",$id);
                return[
                    "status"=>200,
                    "id"=>$id,
                    "success"=>"Formation supprimée avec succés"
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
                $count=Formation::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["libelle"])){
                        Formation::where("reference".$this->prefix,$id)->update(["libelle".$this->prefix=>$data["libelle"]]);
                        $success=true;
                    }
                    if(isset($data["domaine"])){
                        Formation::where("reference".$this->prefix,$id)->update(["domaine".$this->prefix=>$data["domaine"]]);
                        $success=true;
                    }
                    if(isset($data["cout"])){
                        Formation::where("reference".$this->prefix,$id)->update(["cout".$this->prefix=>$data["cout"]]);
                        $success=true;
                    }
                    if(isset($data["duree"])){
                        Formation::where("reference".$this->prefix,$id)->update(["duree".$this->prefix=>$data["duree"]]);
                        $success=true;
                    }
                    if($success){
                        Formation::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Formation modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
        }
        public function to_create_calcules($id){
            $list=Formation::select("reference_formation as reference","libelle_formation as content")
            ->get();
            $data=json_decode(json_encode($list),true);
            foreach ($data as $key=>$item) {
                $data[$key]["content"]=$item["reference"]." - ".$item["content"];
            }
            return response()->json([
                "status"=>200,
                "data"=>$data
            ],200);
        }
    }
?>