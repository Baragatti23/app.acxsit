<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Client;
use App\Models\Proforma;
use App\Models\Licence;
use App\Models\Bordereau;
use App\Models\Equipement;
use App\Models\Calcul;
use App\Models\Connexion;
use App\Models\Equiper;
use App\Models\Fournisseur;
use App\Models\Profil;
use Symfony\Component\EventDispatcher\GenericEvent;

use function PHPUnit\Framework\stringContains;

class ApiController extends Controller
{   
    private $models=["Utilisateur","Client","Calcul","Connexion","Profil",
    "Proforma","Licence","Bordereau","Equipement","Equiper","Fournisseur"];
    // private $mymodels=json_decode(json_encode(DB::select('SHOW TABLES')),true);
    private $exlude_columns=["password"];
    private $PK_prefix="reference";
    private $exlude_tables=["failed_jobs","migrations","password_reset_tokens","personal_access_tokens","users"];
    public function __getModel__($table){
        $model=null;
        foreach ($this->models as $value) {
            if(strtolower($table)===strtolower($value."s")){
                $model='App\Models'.'\\'.$value;
                break;
            }
        }
        return $model;
    }
    public function get($table,$id=null,$json=true){
        $model=$this->__getModel__($table);
        if($model){
            if(!$id){
                $list=self::removeExcludedsColumns($model::all(),$table);
                if($json){
                    return [
                        "status"=>200,
                        "data"=>self::removeSuffix($list,$table),
                        "total"=>count($list)
                    ];
                }else{
                    return $list;
                }
            }else{
                $columns=self::getTableColumns($table);
                $col_id=$this->PK_prefix.self::getPrefixTable($table);
                if(in_array($col_id,$columns)){
                    $user=self::removeExcludedsColumns([$model::where($col_id,$id)->first()],$table)[0];
                }else{
                    $user=self::removeExcludedsColumns([$model::where("id",$id)->first()],$table)[0];
                }
                $user=self::removeSuffix([$user],$table)[0];
                $user=self::foreignTables([$user])[0];
                if($json){
                    return [
                        "status"=>200,
                        "data"=>$user
                    ];
                }else{
                    return $user;
                }
            }
        }else{
            if($json){
                return [
                    "status"=>400,
                    "error"=>"Unknown table $table"
                ];
            }else{
                return null;
            }
        }
    }
    public function pagination($table){
        $model=$this->__getModel__($table);
        if($model){
            $items_per_page=10;
            $list=$model::paginate($items_per_page);
            $list=json_decode(json_encode($list));
            $list->data=self::removeExcludedsColumns($list->data,$table);
            $list->data=json_decode(json_encode($list->data));
            $list->data=self::removeSuffix($list->data,$table);
            $list->data=self::foreignTables($list->data);
            return [
                "status"=>200,
                "data"=>$list->data,
                "current_page"=>$list->current_page,
                "last_page"=>$list->last_page,
                "per_page"=>$list->per_page,
                "total"=>$list->total
            ];
        }else{
            return [
                "status"=>400,
                "table"=>$table,
                "error"=>"Unknown table"
            ];
        }
    }
    public function post($table,Request $request){
        $prefix_table=self::getPrefixTable($table);
        $columns=$this->getTableColumns($table);
        $model=$this->__getModel__($table);
        $data = json_decode($request->getContent(), true);
        if($model){
            $register=new $model();
            if(in_array($this->PK_prefix.$prefix_table,$columns))
            $register->{$this->PK_prefix.$prefix_table}=strtoupper(substr($table,0,3)).$this->generateID();
            if(strtolower($table)=="proformas"){
                
            }
            $columns=$this->getTableColumns($table);
            $exclude_columns=["id","created_at","updated_at"];
            $validate_columns=true;
            foreach($data as $key => $value){
                if(!in_array($key.$prefix_table,$columns)) $validate_columns=false;
            }
            if($validate_columns){
                foreach($data as $key => $value){
                    if(!in_array($key.$prefix_table,$exclude_columns)) $register->{$key.$prefix_table}=$value;
                }
                if($table=="utilisateurs"){
                    $register->{"reference_status"}="STA001";
                    $register->{"reference_profil"}="PRO001";
                }elseif($table=="clients"){
                    $register->{"reference_utilisateur"}="CLI001";
                }
                $register->save();
                return [
                    "status"=>200,
                    "success"=>ucfirst($this->getPrefixTable($table,false))." ajouté avec succès",
                    "id"=>$register->id
                ];
            }else{
                return [
                    "status"=>400,
                    "coloumns"=>array_keys($data),
                    "error"=>"Columns error",
                ];
            }
        }else{
            return [
                "status"=>400,
                "table"=>$table,
                "error"=>"Unknown table"
            ];
        }
    }
    public function put($table,Request $request){
        $model=$this->__getModel__($table);
        if($model){
            $user=Utilisateur::findOrFail($request->id);
            $user->name=$request->name;
            $user->lastname=$request->lastname;
            $user->email=$request->email;
            $user->password=$request->password;
            $user->profile="ADMIN";
            $user->status="ACTIVE";
            $user->save();
            return [
                "status"=>200,
                "success"=>"Utilisateur supprimer avec succès",
                "id"=>$request->id
            ];
        }else{
            return [
                "status"=>400,
                "error"=>"Unknown table"
            ];
        }
    }
    public function del($table,$id){
        $model=$this->__getModel__($table);
        if($model){
            Utilisateur::destroy($id);
            return [
                "status"=>200,
                "success"=>"Utilisateur modifié avec succès",
                "id"=>$id
            ];
        }else{
            return [
                "status"=>400,
                "error"=>"Unknown table"
            ];
        }
    }
    private function getTableColumns($table){
        return DB::getSchemaBuilder()->getColumnListing($table);
        // OR
        // return Schema::getColumnListing($table);
    }
    private function removeExcludedsColumns($data,$table){
        foreach ($data as $item) {
            foreach ($this->exlude_columns as $key) {
                if(isset($item->{$key.substr($table,0,-1)})){
                    unset($item->{$key.substr($table,0,-1)});
                }elseif(isset($item->{$key})){
                    unset($item->{$key});
                }
            }
        }
        return $data;
    }
    private function removeSuffix($data,$table){
        $data=json_decode(json_encode($data),true);
        $prefix_table=self::getPrefixTable($table);
        // echo json_encode($data)."<br/>";
        for ($i=0;$i<count($data);$i++) {
            $elem=$data[$i];
            foreach ($elem as $key => $value) {
                if(str_contains($key,$prefix_table)){
                    $newkey=str_replace($prefix_table,"",$key);
                    unset($data[$i][$key]);
                    $data[$i][$newkey]=$value;
                }
            }
        }
        // exit;
        return json_decode(json_encode($data));
    }
    public function getPrefixTable($table,$underscore=true){
        return ($underscore?"_":"").substr($table,0,-1);
    }
    private function getTables(){
        $collection = json_decode(json_encode(DB::select('SHOW TABLES')),true);
        $tables=[];
        for($i=0;$i<count($collection);$i++){
            $tables[]=$collection[$i]["Tables_in_acxs_it"];
        }
        return $tables;
    }
    private function foreignTables($data){
        $data=json_decode(json_encode($data),true);
        $tables=self::getTables();
        $tables_colmuns=[];
        foreach ($tables as $value) {
            if(in_array($value,$this->exlude_tables)){
                continue;
            }
            foreach ($data as $content) {
                $keys=array_keys($content);
                foreach ($keys as $cel) {
                    if(str_contains($cel,self::getPrefixTable($value)) && $cel!==$this->PK_prefix.self::getPrefixTable($value)){
                        $tables_colmuns[]=[$value=>$cel];
                    }
                }
                break;
            }
        }
        foreach ($tables_colmuns as $item) {
            foreach ($item as $tb => $col) {
                for($i=0;$i<count($data);$i++){
                    $value=$data[$i][$col];
                    unset($data[$i][$col]);
                    $data[$i][self::getPrefixTable($tb,false)]=self::get($tb,$value);
                }
            }
        }
        return $data;
    }
    private function generateID($mask="000000XXX"){
        $newMask="";
        $alphabet=["A","B","C","D","E","F","G","H","I","J","K"
        ,"L","M","N","O","Q","R","S","U","V","W","X","Y","Z"];
        $length=strlen($mask);
        for($i=0;$i<$length;$i++){
           if(in_array($mask[$i],["0",1,2,3,4,5,6,7,8,9])){
              $digit=rand(0, 9);
              $newMask.=$digit;
           }else{
              $random=rand(0, count($alphabet));
              $newMask.=strtoupper($alphabet[$random]);
           }
        }
        return $newMask;
     }
}
