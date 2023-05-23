<?php

    namespace App\Http\Controllers;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Traits\Setting;
    use App\Models\Utilisateur;
    use App\Models\Proforma;
    use App\Models\Bordereau;
use App\Models\Equipement;
use App\Models\Licence;
use PhpParser\Node\Stmt\Else_;

    class UtilisateurController extends Controller{

        use Setting;

        // PROPERTIES ==============================
        // private $exclude_columns=["reference_statu","reference_profil"];
        private $foreign_columns=["profil","statu"];
        private $prefix="_utilisateur";
        private $table="utilisateurs";
        
        // METHODS =================================
        public function get($id=null){
            $id=$id?["reference".$this->prefix,$id]:[];
            $params=$this->validateParams(new Utilisateur(),$id);
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Utilisateur(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->countProformas(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countBordereaus(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countLicences(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countEquipements(json_decode(json_encode($data["data"]),true));
            }
            return $data;
        }
        public function post(Request $request){
            // $columns=$this->getTableColumns($this->table);
            $data = json_decode($request->getContent(),true);
            // exit;
            $element=new Utilisateur();
            $element->{"reference".$this->prefix}=strtoupper(substr($this->table,0,3)).$this->generateID();
            if(isset($data["name"],$data["lastname"],$data["email"],$data["password"])){
                $element->{"name".$this->prefix}=$data["name"];
                $element->{"lastname".$this->prefix}=$data["lastname"];
                $element->{"email".$this->prefix}=$data["email"];
                $element->{"password".$this->prefix}=$data["password"];
                $element->{"created_by".$this->prefix}="UTI0001";
                $element->{"reference_statu"}=$data["statu"] ?? "STA0001";
                $element->{"reference_profil"}=$data["profil"] ?? "PRO0001";
                $element->{"created_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->{"updated_at".$this->prefix}=date("Y-m-d H:i:s");
                $element->save();
                return[
                    "status"=>200,
                    "id"=>$element->{"reference".$this->prefix},
                    "success"=>"Utilisateur enregistré avec succés"
                ];
            }else{
                return[
                    "status"=>400,
                    "error"=>"Utilisateur non enregistré"
                ];
            }
        }
        public function del($id){
            $result=Utilisateur::where("reference".$this->prefix,$id)->get();
            if(empty(json_decode($result->toJson()))){
                return [
                    "status"=>400,
                    "success"=>"Utilisateur non trouvé",
                    "id"=>$id
                ];
            }else{
                if(Utilisateur::where("reference".$this->prefix,$id)->delete()){
                    return [
                        "status"=>200,
                        "success"=>"Utilisateur supprimer avec succès",
                        "id"=>$id
                    ];
                }else{
                    return [
                        "status"=>500,
                        "success"=>"Erreur inattendu au serveur",
                        "id"=>$id
                    ];
                }
            }
        }
        public function countProformas($users){
            $i=0;
            foreach ($users as $item) {
                $users[$i]["total_proformas"]=Proforma::where("reference_utilisateur",$item["reference"] ?? $item["reference_utilisateur"])
                ->count();
                $i++;
            }
            return $users;
        }
        public function countBordereaus($users){
            $i=0;
            foreach ($users as $item) {
                $users[$i]["total_bordereaus"]=Bordereau::where("reference_utilisateur",$item["reference"] ?? $item["reference_utilisateur"])
                ->count();
                $i++;
            }
            return $users;
        }
        public function countLicences($users){
            $i=0;
            foreach ($users as $item) {
                $users[$i]["total_licences"]=Licence::where("reference_utilisateur",$item["reference"] ?? $item["reference_utilisateur"])
                ->count();
                $i++;
            }
            return $users;
        }
        public function countEquipements($users){
            $i=0;
            foreach ($users as $item) {
                $users[$i]["total_equipements"]=Equipement::where("reference_utilisateur",$item["reference"] ?? $item["reference_utilisateur"])
                ->count();
                $i++;
            }
            return $users;
        }
    }
?>