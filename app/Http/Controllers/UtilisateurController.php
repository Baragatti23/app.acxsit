<?php

    namespace App\Http\Controllers;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Traits\Setting;
    use App\Models\Utilisateur;
    use App\Models\Proforma;
    use App\Models\Bordereau;
use App\Models\Equipement;
use App\Models\Licence;
use App\Models\Statu;
use App\Models\User;
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
            $params=$this->validateParams();
            if(isset($params["status"]) && $params["status"]=400){
                return $params;
            }
            $data=$this->executeQuery(new Utilisateur(),$params,$id);
            if($data["status"]==200 && isset($data["data"])){
                $data["data"]=$this->countProformas(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countBordereaus(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countLicences(json_decode(json_encode($data["data"]),true));
                $data["data"]=$this->countEquipements(json_decode(json_encode($data["data"]),true));
                $data["total"]=Utilisateur::count();
                $data["data"]=$this->setPK(json_decode(json_encode($data["data"]),true),["reference"]);
            }
            return $data;
        }
        public function del($id){
            $user=Utilisateur::where("reference".$this->prefix,$id)->first();
            if(empty(json_decode($user->toJson()))){
                return [
                    "status"=>400,
                    "success"=>"Utilisateur non trouvé",
                    "id"=>$id
                ];
            }else{
                if(User::where("email",$user["email".$this->prefix])->delete() &&
                    Utilisateur::where("reference".$this->prefix,$id)->delete()){
                    $this->insertActivity("DELETE",$id);
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
        public function put($id,Request $request){
            $data = json_decode($request->getContent(),true);
            // echo json_encode($data);
            // exit;
            if(isset($id)){
                $success=false;
                $count=Utilisateur::where("reference".$this->prefix,$id)->count();
                if($count>0){
                    if(isset($data["name"])){
                        Utilisateur::where("reference".$this->prefix,$id)->update(["name".$this->prefix=>$data["name"]]);
                        $success=true;
                    }
                    if(isset($data["lastname"])){
                        Utilisateur::where("reference".$this->prefix,$id)->update(["lastname".$this->prefix=>$data["lastname"]]);
                        $success=true;
                    }
                    if(isset($data["email"])){
                        Utilisateur::where("reference".$this->prefix,$id)->update(["email".$this->prefix=>$data["email"]]);
                        $success=true;
                    }
                    if(isset($data["status"])){
                        $count=Statu::where("reference_statu",$data["status"])->count();
                        if($count>0){
                            Utilisateur::where("reference".$this->prefix,$id)->update(["reference_statu"=>$data["status"]]);
                            $success=true;
                        }
                    }
                    if($success){
                        Utilisateur::where("reference".$this->prefix,$id)->update(["updated_at".$this->prefix=>date("Y-m-d H:i:s")]);
                        $this->insertActivity("UPDATE",$id);
                        return[
                            "status"=>200,
                            "id"=>$id,
                            "success"=>"Utilisateur modifié avec succés"
                        ];
                    }
                }
            }
            return[
                "status"=>400,
                "error"=>"Modification non réussi"
            ];
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