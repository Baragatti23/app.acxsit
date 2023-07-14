<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Setting;
use App\Models\Categorie;
use App\Models\Equipement;
use App\Models\Fournisseur;
use App\Models\Licence;

class ProductController extends Controller
{
    use Setting;
    public function stats(){
        $data=[];
        if(isset(request()->betweenStart)){
            $tables=["equipements","licences","fournisseurs","categories"];
            foreach ($tables as $key => $elem) {
                $params=json_decode(json_encode($this->validateParams(prefix:substr($elem,0,-1))),true);
                if(isset($params["betweenStartValue"],$params["betweenEndValue"])){
                    $model=$this->__getModel__(ucwords($elem));
                    echo ucwords(substr($elem,0,-1))."\n";
                    if($model){
                        $data[$elem]=$model::whereBetween("created_at_".strtolower(substr($elem,0,-1)),[$params["betweenStartValue"],$params["betweenEndValue"]])->count();
                    }
                }
            }
        }else{
            $data["equipements"]=Equipement::count();
            $data["licences"]=Licence::count();
            $data["suppliers"]=Fournisseur::count();
            $data["categories"]=Categorie::count();
        }
        $data["status"]=200;
        $data["data"]=[];
        return $data;
    }
}
