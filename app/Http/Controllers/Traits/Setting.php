<?php

    namespace App\Http\Controllers\Traits;
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\PDF\ProformaPDF;
use App\Models\Activite;
use Exception;


    trait Setting{
        private $special_tables=["failed_jobs","migrations","password_reset_tokens","personal_access_tokens","users"];
        private $status_tables=["status","estados","levels","stades"];
        private $termination="s"; //suffix of tables (if the table name finish with 's')
        private $suffix_table=true; //suffix of columns (if the column name finish with '_tablename')
        private $PK_prefix="reference";
        private $operator_list=["=","<>","!=","IN","NOT IN",">","<",">=","<=","LIKE","NOT LIKE"];
        private $models=[
            "Utilisateur","Client","Estado","Connexion","Profil","Statu",
            "Proforma","Licence","Level","Bordereau","Equipement","Equiper",
            "Fournisseur","Stade","Categorie","Vendreequipement","Vendrelicence",
            "Activite","Tolicence","Stagere"
        ];
        public function queryParams($prefix=""){
            /*=====================================================
            QUERY PARAMS
            =====================================================*/
            $params=[];
            $params["limit"]=request()->limit ?? "all"; // (int)
            $params["orderBy"]=request()->orderBy ?? "created_at".($prefix!==""?$this->prefix:$prefix); // (string) - delimiter ','
            $params["orderMode"]=request()->orderMode ?? "desc"; //delimiter ','
            $params["offset"]=request()->offset ?? 0; // (int)
            $params["select"]=request()->select ?? ""; // (string) - delimiter ','
            $params["linkTo"]=request()->linkTo ?? ""; // (string) - delimiter ','
            $params["compareTo"]=request()->compareTo ?? ""; // (string) - delimiter '.,'
            $params["operatorTo"]=request()->operatorTo ?? ""; // (string) - delimiter ','
            $params["connectorTo"]=request()->connectorTo ?? ""; //  (string) - delimiter ','
            $params["case"]=request()->case ?? true; //  (string) - delimiter ','
            $params["betweenStart"]=request()->betweenStart ?? null; //  (string) - delimiter ','
            $params["betweenEnd"]=request()->betweenEnd ?? null; //  (string) - delimiter ','
            if(isset(request()->count)) $params["count"]=true;
            $params["foreign"]=isset(request()->foreign) || request()->foreign==true ? true:false; // (boolean or empty)
            $params["foreign_recursive"]=isset(request()->foreign_recursive) || request()->foreign_recursive==true ? true:false; // (boolean or empty)
            return $params;
        }
        public function validateParams($max=PHP_INT_MAX,$id="",$prefix=""){
            $params=$this->queryParams("_".$prefix);
            $params["limit"]=is_string($params["limit"])
            && strlen($params["limit"])==strlen("all")
            && ($params["limit"]==="all" || $params["limit"]==="")? $max:$params["limit"];
            /*=====================================================
            CLEAN AND FORMAT DATA QUERY PARAMS
            =====================================================*/
            /* ---------------- 'SELECT' PARAM --------------------
                - $selectArray: stock the colmuns selected of main table. 
                - $selectArrayForeigns: stock the foreign tables selected. 
                - $selectArrayForeignsColumns: stock the colmuns selected...
                  ...of foreign tables (array associative: ['table1'=>[columns],'table2'=>[columns]])
            */
            $columns_foreign_list=$this->getTableColumnsForeign(isset($this->table)?$this->table:$prefix."s");
            $selectArray=explode(",",$params["select"]);
            $selectArray=array_filter($selectArray);
            $selectArray=array_unique($selectArray);
            $selectArrayForeigns=[];
            // filters the foreign tables of 'select' query param
            foreach ($selectArray as $i=>$value) {
                if(in_array($value,$columns_foreign_list)){
                    $selectArrayForeigns[]=$value;
                    unset($selectArray[$i]);
                }
            }
            // filters the columns of 'select' query param
            $selectArray=$this->validateTableColmuns($selectArray,isset($this->table)?$this->table:$prefix."s");
            if(empty($selectArray)){
                return $this->responseJSON(400,others:["msg"=>"Columns Error in select","select"=>$params["select"]]);
            }
            /* ---------------- 'FOREIGN' PARAM --------------------
                - $foreign: (boolean) - show all foreigns tables info
            */
            if(!$params["foreign"]){
                $foreign_recursive=false;
            }else{
                $selectArrayForeigns=$this->foreign_columns;
            }
            /* ---------------- 'ORDERBY' & 'ORDERMODE' PARAMS --------------------
                - $orderByArray: string[] - colmuns to apply the order
                - $orderModeArray: string[] - colmuns to apply the order mode  - values: asc | desc
            */
            $orderByArray=$this->arrayFilter(explode(",",$params["orderBy"]));
            $orderByArray=$this->validateTableColmuns($orderByArray,isset($this->table)?$this->table:$prefix."s");
            if(!empty($orderByArray) && $orderByArray[0]=="*") $orderByArray=["created_at".isset($this->pefix)?$this->pefix:$prefix."s"];
            else if(empty($orderByArray)){
                return $this->responseJSON(400,others:["msg"=>"Columns Error in orderBy","orderBy"=>$params["orderBy"]]);
            }
            $orderModeArray=explode(",",$params["orderMode"]);
            $countçorderMode=count($orderModeArray);
            foreach ($orderByArray as $i => $value) {
                if(isset($orderModeArray[$i])){
                    if(!in_array(strtoupper($orderModeArray[$i]),["ASC","DESC"])){
                        $orderModeArray=[];
                        break;
                    }else{
                        $orderModeArray[$i]=strtoupper($orderModeArray[$i]);
                    }
                }else{
                    if($countçorderMode==1) $orderModeArray[]=$orderModeArray[0];
                    else $orderModeArray[]="ASC";
                }
            }
            $orderModeArray=array_slice($orderModeArray,0,count($orderByArray));
            if(empty($orderModeArray))  return $this->responseJSON(400,others:["msg"=>"Error format in orderMode","orderMode"=>$params["orderMode"]]);
            /* ---------------- 'LIMIT' & 'OFFSET' PARAMS --------------------
                - $limitValue:
                - $offsetValue:
            */
            $limitValue=(int)$params["limit"]*0==0 ? $params["limit"] :  null;
            $offsetValue=(int)$params["limit"]*0==0 ? $params["offset"] :  null;
            if($limitValue===null){
                return $this->responseJSON(400,others:["msg"=>"Insert int value limit","limit"=>$params["limit"]]);
            }
            if($offsetValue===null){
                return $this->responseJSON(400,others:["msg"=>"Insert int value offset","offset"=>$params["offset"]]);
            }
            /* ---------------- 'LINKTO' && 'COMPARETO' && 'OPERATORTO' && 'CONECTORTO' PARAMS --------------------
                - $linkToArray: string[] - 
                - $equalToArray: string[] - 
                - $compareToArray: string[] - 
                - $connectorToArray: string[] - 
            */
            $linkToArray=$this->arrayFilter(explode(",",$params["linkTo"]),true);
            $linkToArray=$this->validateTableColmuns($linkToArray,isset($this->table)?$this->table:$prefix."s");
            if(empty($linkToArray)){
                return $this->responseJSON(400,others:["msg"=>"Columns error in linkTo","linkTo"=>$params["linkTo"]]);
            }elseif($linkToArray[0]=="*"){
                $linkToArray=[];
            }
            $compareToArray=[...array_filter(explode(".,",$params["compareTo"]))];
            foreach ($linkToArray as $i => $value) {
                if(!isset($compareToArray[$i])){
                    $compareToArray=[];
                    break;
                }
            }
            $compareToArray=array_slice($compareToArray,0,count($linkToArray));
            if(!empty($linkToArray) && empty($compareToArray)){
                return $this->responseJSON(400,others:["msg"=>"Too few values to compareTo","compareTo"=>$params["compareTo"]]);
            }
            $operatorToArray=[...array_filter(explode(",",$params["operatorTo"]))];
            $count_operator=count($operatorToArray);
            foreach ($linkToArray as $i => $value) {
                if(!isset($operatorToArray[$i])){
                    $operatorToArray[$i]="=";
                    if($count_operator==1){
                        $operatorToArray[$i]=strtoupper($operatorToArray[0]);
                    }
                }else if(!in_array(strtoupper($operatorToArray[$i]),$this->operator_list)){
                    $operatorToArray=[];
                    break;
                }else{
                    $operatorToArray[$i]=strtoupper($operatorToArray[$i]);
                }
            }
            $operatorToArray=array_slice($operatorToArray,0,count($linkToArray));
            if(!empty($linkToArray) && empty($operatorToArray)){
                return $this->responseJSON(400,others:["msg"=>"Too few values to operatorTo","operatorTo"=>$params["operatorTo"]]);
            }
            $connectorToArray=[...array_filter(explode(",",$params["connectorTo"]))];
            if(count($linkToArray)>1){
                $count_connector=count($connectorToArray);
                for($i=0;$i<count($linkToArray)-1;$i++) {
                    if(!isset($connectorToArray[$i])){
                        $connectorToArray[$i]="AND";
                        if($count_connector==1){
                            $connectorToArray[$i]=strtoupper($connectorToArray[0]);
                        }
                    }else if(!in_array(strtoupper($connectorToArray[$i]),["AND","OR"])){
                        $connectorToArray=[];
                        break;
                    }else{
                        $connectorToArray[$i]=strtoupper($connectorToArray[$i]);
                    }
                }
            }
            /* ---------------- 'BETWEEN' PARAMS -------------------- */
            $betweenStartValue=$params["betweenStart"] ?? null;
            $betweenEndValue=$params["betweenEnd"] ?? null;
            if($betweenStartValue && $this->check_your_datetime($betweenStartValue)){
                if(!$betweenEndValue || !$this->check_your_datetime($betweenEndValue)){
                    $betweenEndValue=explode(" ",$betweenStartValue)[0]." 23:59:59";
                }
            }else{
                $betweenStartValue=null;
                $betweenEndValue=null;
            }
            return [
                "selectArray"=>$selectArray,
                "linkToArray"=>$linkToArray,
                "compareToArray"=>$compareToArray,
                "operatorToArray"=>$operatorToArray,
                "connectorToArray"=>$connectorToArray,
                "orderByArray"=>$orderByArray,
                "orderModeArray"=>$orderModeArray,
                "limitValue"=>$limitValue,
                "betweenStartValue"=>$betweenStartValue,
                "betweenEndValue"=>$betweenEndValue,
                "offsetValue"=>$offsetValue,
                "selectArrayForeigns"=>$selectArrayForeigns,
                "count"=>isset($params["count"]) ? true : false
            ];
            
        }
        public function executeQuery($instanceModel,$params,$id,$foreign=true){
            // echo json_encode([
            //     "betweenStart"=>$params["betweenStartValue"],
            //     "betweenEnd"=>$params["betweenEndValue"]
            // ]);
            // exit;
            $data=$instanceModel->with($params["selectArrayForeigns"]);
            $data=$data->select($params["selectArray"]);
            foreach ($params["orderByArray"] as $i => $value) {
                $data=$data->orderBy($params["orderByArray"][$i],$params["orderModeArray"][$i]);
            }
            $data=$data->offset($params["offsetValue"]);
            $data=$data->limit($params["limitValue"]);
            if(!empty($id)){
                $data=$data->where(...$id);
            }
            foreach ($params["linkToArray"] as $i => $value) {
                if($params["operatorToArray"][$i]=="IN"){
                    if(isset($params["connectorToArray"][$i-1]) && $params["connectorToArray"][$i-1]=="OR"){
                        $data=$data->orWhereIn($params["linkToArray"][$i],[...array_filter(explode(',,',$params["compareToArray"][$i]))]);
                    }else{
                        $data=$data->whereIn($params["linkToArray"][$i],[...array_filter(explode(',,',$params["compareToArray"][$i]))]);
                    }
                }elseif($params["operatorToArray"][$i]=="NOT IN"){
                    if(isset($params["connectorToArray"][$i-1]) && $params["connectorToArray"][$i-1]=="OR"){
                        $data=$data->orWhereNotIn($params["linkToArray"][$i],[...array_filter(explode(',,',$params["compareToArray"][$i]))]);
                    }else{
                        $data=$data->whereNotIn($params["linkToArray"][$i],[...array_filter(explode(',,',$params["compareToArray"][$i]))]);
                    }
                }elseif($params["operatorToArray"][$i]=="LIKE"){
                    if(isset($params["connectorToArray"][$i-1]) && $params["connectorToArray"][$i-1]=="OR"){
                        $data=$data->orWhere($params["linkToArray"][$i],$params["operatorToArray"][$i],"%".$params["compareToArray"][$i]."%");
                    }else{
                        $data=$data->where($params["linkToArray"][$i],$params["operatorToArray"][$i],"%".$params["compareToArray"][$i]."%");
                    }
                }else{
                    if(isset($params["connectorToArray"][$i-1]) && $params["connectorToArray"][$i-1]=="OR"){
                        $data=$data->orWhere($params["linkToArray"][$i],$params["operatorToArray"][$i],$params["compareToArray"][$i]);
                    }else{
                        $data=$data->where($params["linkToArray"][$i],$params["operatorToArray"][$i],$params["compareToArray"][$i]);
                    }
                }
            }
            if($params["betweenStartValue"]){
                $data=$data->whereBetween("created_at".$this->prefix,[$params["betweenStartValue"],$params["betweenEndValue"]]);
            }
            $data=json_decode($data->get()->toJSON(),true);
            if(count($data)!=0){
                if($foreign) $data=$this->getForeignRecords($data,$this->table);
                $data=$this->cleanPrefix($data,$this->prefix);
            }
            $json=[];
            if(($params["offsetValue"]!=0 && $params["limitValue"]!=$instanceModel::count())
                || ($params["offsetValue"]==0 && $params["limitValue"]!=$instanceModel::count())){
                // $json["next"]="";
                // $json["prev"]="";
            }
            if(!$params["count"])
                return $this->responseJSON(200,$data,$json);
            else
                return $this->responseJSON(200,null,["count"=>count($data)]);
        }
        private int $control_recursive_getForeignRecords=0;
        public function getForeignRecords($data,$table){
            $this->control_recursive_getForeignRecords++;
            if(!$data || !$table) return false;
            $i=0;
            foreach ($data as $item) {
                $foreigns=[];
                foreach ($item as $key => $value) {
                    if(str_contains($key,"reference_") && str_replace("reference_",'',$key)."s"!=$table){
                        $model=$this->__getModel__(str_replace("reference_",'',$key)."s");
                        if($model){
                            $clave=strtolower(str_replace("reference_",'',$key));
                            unset($data[$i][$key]);
                            if($value && $value!=""){
                                $foreign=json_decode($model::where($key,$value)->get()->toJson(),true);
                                if(count($foreign)==1){
                                    if($this->control_recursive_getForeignRecords<=2) $foreign=$this->getForeignRecords([$foreign[0]],$clave."s");
                                    $foreigns[]=["key"=>$clave,"content"=>$foreign[0]];
                                }else{
                                    $foreigns[]=["key"=>$key,"content"=>$value];
                                }
                            }else{
                                $foreigns[]=["key"=>$key,"content"=>null];
                            }
                        }
                    }
                }
                foreach ($foreigns as $elem) {
                    $data[$i][$elem["key"]]=$elem["content"];
                }
                $i++;
            }
            return $data;
        }
        public function cleanPrefix(array $items,$prefix,$exclude_columns=true){
            // $items: data (register of table DB): associate array
            // $prefix: prefix de la table (_table) a supprimer
            // $foreign: get data to foreign_keys tables
            // $recursive_foreign: get recursvie data to foreign_keys tables
            if(count($items)>0){
                $exclude_columns=$this->getExcludedColumns($items[0],$prefix);
                for($i=0;$i<count($items);$i++){
                    $item=$items[$i];
                    if($exclude_columns){
                        $excluded=["id"];
                        if(isset($this->excluded_columns) && is_array($this->excluded_columns)) $excluded=[...$excluded,...$this->excluded_columns];
                        $item=$this->excludeColumns($item,$excluded);
                    }
                    foreach ($item as $col => $value) {
                        if(in_array(ucwords(strtolower($col)),$this->models)){
                            unset($items[$i][$col]);
                            $clave=$col;
                            if(in_array($col."s",$this->status_tables)){
                                $clave='status';
                            }
                            if(is_array($value)){
                                $items[$i][$clave]=$this->cleanPrefix([$value],"_".$col)[0];
                            }else{
                                $items[$i][$clave]=$value;
                            }
                        }else{
                            if(str_contains($col,$prefix)){
                                $items[$i][str_replace($prefix,"",$col)]=$value;
                                unset($items[$i][$col]);
                            }
                        }
                    }
                    // $items[$i]=$this->cleanPrefixForeign($item,$prefix);
                }
            }
            return $items;
        }
        // public function cleanPrefixForeign($data,$prefix,$foreign=false,$recursive_foreign=false){
        //     foreach ($data as $column => $value) {
        //         $col=$column;
        //         if(str_contains($column,$prefix)){
        //             $col=explode($prefix,$column)[0];
        //             $data[$col]=$value;
        //             unset($data[$column]);
        //         }
        //         if(is_array($data[$col])){
        //             $keys=array_keys($data[$col]);
        //             $value=$this->getForeignTables([$value],$col."s")[0];
        //             // if(count($keys)>0 && !is_numeric($keys[0])){
        //             //     if($foreign) $data[$col]=$this->excludeColumns($data[$col],$this->getExcludedColumns($data[$col],"_".$col));
        //             //     $data[$col]=$this->cleanPrefixForeign($data[$col],"_".$col,$foreign);
        //             // }else{
        //             //     $j=0;
        //             //     foreach ($data[$col] as $one) {
        //             //         if($foreign) $data[$col][$j]=$this->excludeColumns($one,$this->getExcludedColumns($one,"_".substr($col,0,-1)));
        //             //         $data[$col][$j]=$this->cleanPrefixForeign($one,"_".substr($col,0,-1),$foreign,$recursive_foreign);
        //             //         $j++;
        //             //     }
        //             // }
        //         }
        //     }
        //     return $data;
        // }
        function check_your_datetime($x) {
            // return (date('Y-m-d H:i:s', strtotime($x)) == $x);
            $part_date=count(explode(" ",$x))>0?explode(" ",$x)[0]:null;
            $part_time=count(explode(" ",$x))>1?explode(" ",$x)[1]:null;
            if($part_date){
                $date=date('Y-m-d H:i:s', strtotime($x));
                $stringdate=[
                    "year"=>explode("-",$part_date)[0],
                    "month"=>explode("-",$part_date)[1],
                    "day"=>explode("-",$part_date)[2]
                ];
                $date=[
                    "year"=>explode("-",explode(" ",$date)[0])[0],
                    "month"=>explode("-",explode(" ",$date)[0])[1],
                    "day"=>explode("-",explode(" ",$date)[0])[2]
                ];
                if($date["year"]===$stringdate["year"] && 
                    $date["month"]===substr("0".$stringdate["month"],-2) &&
                    $date["day"]===substr("0".$stringdate["day"],-2)
                ){
                    if($part_time){
                        $date=date('Y-m-d H:i:s', strtotime($x));
                        $stringdate=[
                            "hour"=>explode(":",$part_time)[0],
                            "min"=>explode(":",$part_time)[1],
                            "sec"=>explode(":",$part_time)[2]
                        ];
                        $date=[
                            "hour"=>explode(":",explode(" ",$date)[1])[0],
                            "min"=>explode(":",explode(" ",$date)[1])[1],
                            "sec"=>explode(":",explode(" ",$date)[1])[2]
                        ];
                        if($date["hour"]===$stringdate["hour"] && 
                            $date["min"]===substr("0".$stringdate["min"],-2) &&
                            $date["sec"]===substr("0".$stringdate["sec"],-2)
                        ){ return true; }
                    }else{ return true; }
                }
            }
            return false;
        }
        public function excludeColumns($data,$columns){
            foreach ($data as $column => $value) {
                if(in_array($column,$columns)){
                    unset($data[$column]);
                }
            }
            return $data;
        }
        public function getExcludedColumns($line_table,$prefix_table){
            $exclude_columns=["id"];
            if(is_array($line_table)){
                foreach ($line_table as $key => $value) {
                    if(str_contains($key,"reference_") && !str_contains($key,$prefix_table)){
                        $exclude_columns[]=$key;
                    }
                }
            }
            return $exclude_columns;
        }
        public function getTableModel($table_or_prefix){
            $model=null;
            foreach ($this->models as $value) {
                if(strtolower($table_or_prefix)===strtolower($value."s")
                    || strtolower(substr($table_or_prefix,1)."s")===strtolower($value."s")){
                        $model='App\Models'.'\\'.$value;
                        break;
                }
            }
            return $model;
        }
        public function getPrefixTable($table,$underscore=true){
            return ($underscore?"_":"").substr($table,0,-1);
        }
        private function getTableColumns($table,$suffix_table=null){
            if($suffix_table===null){
                $suffix_table=$this->suffix_table;
            }elseif($suffix_table){
                $suffix_table = $this->suffix_table ? true : false;
            }
            $columns=DB::getSchemaBuilder()->getColumnListing($table);
            // OR
            // $columns=chema::getColumnListing($table);
            $part=$this->getPrefixTable($table);
            foreach ($columns as $i=>$value) {
                if(!$suffix_table){
                    $columns[$i]=str_replace($part,"",$value);
                }
            }
            return $columns;
        }
        public function getTableColumnsForeign($table){ // FK: reference_profil,reference_statu => profil,statu;
            $columns=$this->getTableColumns($table);
            $foreigns=[];
            foreach ($columns as $i => $value) {
                if(str_contains($value,$this->PK_prefix."_")){
                    $foreign_column=str_replace($this->PK_prefix."_","",$value);
                    $tables=$this->getTables();
                    if(in_array($foreign_column.$this->termination,$tables) && $foreign_column.$this->termination!=$table){
                        $foreigns[]=$foreign_column;
                    }
                }  
            }
            return $foreigns;
        }
        private function getTables(){
            $collection = json_decode(json_encode(DB::select('SHOW TABLES')),true);
            $tables=[];
            for($i=0;$i<count($collection);$i++){
                if(!in_array($collection[$i]["Tables_in_acxs_it"],$this->special_tables)){
                    $tables[]=$collection[$i]["Tables_in_acxs_it"];
                }
            }
            return $tables;
        }
        public function getSpecialTables(){
            return $this->special_tables;
        }
        public function getTermination(){
            return $this->termination;
        }
        public function responseJSON($status_code,$data=null,$others=[]){
            $json=["status"=>$status_code];
            if(isset($data)){
                $json["data"]=[];
                if($data!=null){
                    $json["data"]=json_decode(json_encode($data));
                    $json["total_results"]=count($data);
                }
            }
            foreach ($others as $key => $value) {
                if(!isset($json[$key])) $json[$key]=$value;
            }
            return $json;
        }
        public function validateTableColmuns($columns,$table){
            $array=[];
            $columns_list=$this->getTableColumns($table,true);
            foreach ($columns as $i=>$value) {
                if(!in_array($value,$columns_list) && !in_array($value.(isset($this->prefix)?$this->prefix:"_".substr($table,0,-1)),$columns_list)){
                    return [];
                }else{
                    $index=array_search($value,$columns_list);
                    if(!$index){
                        $index=array_search($value.(isset($this->prefix)?$this->prefix:"_".substr($table,0,-1)),$columns_list);
                    }
                    $array[]=$columns_list[$index];
                }
            }
            if(empty($array)) $array=["*"];
            return $array;
        }
        public function arrayFilter($array,$duplicates=false){
            $array=array_filter($array);
            if(!$duplicates) $array=array_unique($array);
            $array=[...$array];
            return $array;
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
                  $random=rand(0, count($alphabet)-1);
                  $newMask.=strtoupper($alphabet[$random]);
               }
            }
            return $newMask;
         }
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
        public function create_proforma_pdf($proforma,$calcules=[],$download=false){
            $pdf_proforma=[];
            $pdf_proforma["client"]=$proforma['client']["name"];
            $pdf_proforma["subject"]=$proforma['sujet'];
            $pdf_proforma["date"]=implode("/",array_reverse(explode("-",explode(" ",$proforma['created_at'])[0])));
            $pdf_proforma["reference"]=$proforma['reference'];
            $pdf_proforma["total_ht"]=$this->format_number($proforma['total_ht']);
            $pdf_proforma["total_ttc"]=$this->format_number($proforma['total_ttc']);
            $pdf_proforma["total_ttc_letters"]=$this->convertNumberToWords(intval($proforma['total_ttc']));
            $pdf_proforma["delivery"]=$proforma["livraison"]==0?"Immédiatement":($proforma["livraison"]." Jours");
            $pdf_proforma["validity"]=$proforma['validate'] . " mois";
            $pdf_proforma["warranty"]=$proforma['garantie'] . " mois";
            $pdf_proforma["modality"]="100% à la livraison";
            $pdf_proforma["versement"]="chèque/virement";
            $data=[];
            foreach ($calcules as $item) {
                $data[]=[
                    "designation" => $item["equipement"]["designation"],
                    "unity" => "U",
                    "quantity" => $item["unites"],
                    "total_ht" => $this->format_number(abs(intval($item["montant_unitaire_ht"]))),
                    "tva" => $this->format_number(abs(intval($item["tva"]))),
                    "total_ttc" => $this->format_number(abs(intval($item["montant_total_ttc"]))),
                ];
            }
            $pdf = new ProformaPDF($pdf_proforma);
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('arial','',9);
            $pdf->table($data);
            if($download) $pdf->Output("D","Proforma_".$proforma["reference"].".pdf");
            else $pdf->Output();
        }
        public function clearData($result){
            $words=explode(' ',$result);
            for($i=0;$i<count($words);$i++){
                if($words[$i]=="un" && $i+1<count($words)){
                    if($words[$i+1]=="cent") $words[$i]="";
                    if($words[$i+1]=="mille" && $i-1<0) $words[$i]="";
                }
            }
           return implode(" ",$words);
        }
        public function convertNumberToWords($number){
            $words = array(
                0 => 'Zero',
                1 => 'un',
                2 => 'deux',
                3 => 'trois',
                4 => 'quatre',
                5 => 'cinq',
                6 => 'six',
                7 => 'sept',
                8 => 'huit',
                9 => 'neuf',
                10 => 'dix',
                11 => 'onze',
                12 => 'douze',
                13 => 'treize',
                14 => 'quatorze',
                15 => 'quinze',
                16 => 'seize',
                17 => 'dix-sept',
                18 => 'dix-huit',
                19 => 'dix-neuf',
                20 => 'vingt',
                30 => 'trente',
                40 => 'quarante',
                50 => 'cinquante',
                60 => 'soixante',
                70 => 'soixante-dix',
                80 => 'quatre-vingts',
                90 => 'quatre-vingt-dix'
            );
        
            if (!is_numeric($number)) {
                return false;
            }else{
                $number=intval($number);
            }
        
            if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
                throw new Exception('Number is too small.');
            }
        
            if ((int)$number > PHP_INT_MAX) {
                throw new Exception('Number is too large.');
            }
        
            if ($number < 21) {
                return $words[abs($number)];
            }
        
            if ($number < 100) {
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                if ($units) {
                    return $words[$tens] . '-' . $words[$units];
                } else {
                    return $words[$tens];
                }
            }
        
            if ($number < 1000) {
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $result = $words[$hundreds] . ' cent';
                if ($remainder) {
                    $result .= ' ' . $this->convertNumberToWords($remainder);
                }
                return $result;
            }
        
            if ($number < 1000000) {
                $thousands = $number / 1000;
                $remainder = $number % 1000;
                $result = $this->convertNumberToWords($thousands) . ' mille';
                if ($remainder) {
                    $result .= ' ' . $this->convertNumberToWords($remainder);
                }
                return $this->clearData($result);
            }
        
            if ($number < 1000000000) {
                $millions = $number / 1000000;
                $remainder = $number % 1000000;
                $result = $this->convertNumberToWords($millions) . ' million';
                if ($millions > 1) {
                    $result .= 's';
                }
                if ($remainder) {
                    $result .= ' ' . $this->convertNumberToWords($remainder);
                }
                return $this->clearData($result);
            }
        
            $billions = floor($number / 1000000000);
            $remainder = $number % 1000000000;
            $result = $this->convertNumberToWords($billions).' milliard';
            if ($billions > 1) {
                $result .= 's';
            }
            if ($remainder) {
                $result .= ' ' . $this->convertNumberToWords($remainder);
            }
            return $this->clearData($result);
        }
        public function setPK(array $elements,$pks){
            $i=0;
            foreach ($elements as $item) {
                $excess=true;
                foreach ($pks as $key) {
                    if(!isset($item[$key])){
                        $excess=false;
                        break;
                    }
                }
                foreach ($pks as $value) {
                    if($excess){
                        $elements[$i]["_pk_"][$value]=$item[$value];
                    }else{
                        $key=str_replace("reference_","",$value);
                        $elements[$i]["_pk_"][$value]=$item[$key]["reference"];
                    }
                }
                $i++;
            }
            return $elements;
        }
        public function format_number($number,$point=false){
            $parse_int="".intval($number);
            $parse_float="".floatval($number);
            $returne="";
            for($i = strlen($parse_int)-1,$j=1; $i >=0; $i--,$j++) {
                $returne=$parse_int[$i].$returne;
                if($j%3==0 && strlen($parse_int)>$j) $returne=($point?".":" ").$returne;
            }
            // if($parse_float>0) $returne.=",".$parse_float;
            return $returne;
        }
        public function generateCode($table){
            $alphabet=["A","B","C","D","E","F","G","H","I","J","K","L"];
            $start=date("Y:m:1 00:00:00");
            $end=date("Y:m:j 23:59:59");
            $model=$this->__getModel__($table);
            if($model){
                $total=$model::whereBetween("created_at".$this->prefix,[$start,$end])->count();
                $last=$model::whereBetween("created_at".$this->prefix,[$start,$end])->orderBy("created_at".$this->prefix,"DESC")->first();
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
                    $count=$model::where("reference".$this->prefix,$code)->count();
                }while($count!=0);
                return $code;
            }else{
                return false;
            }
        }
        public function insertActivity($action,$reference_element,$user="",$table=""){
            if($table==="") $table=$this->table;
            if($user==="") $user="UTS109083DOM";
            $item=new Activite();
            $prefix="_activite";
            $item->{"reference".$prefix}="ACT".$this->generateID();
            $item->{"reference_utilisateur"}=$user;
            $item->{"table".$prefix}=strtoupper($table);
            $item->{"action".$prefix}=$action;
            $item->{"element".$prefix}=$reference_element;
            // $item->{"created_at".$prefix}=date("Y-m-d H:i:s");
            // $item->{"updated_at".$prefix}=date("Y-m-d H:i:s");
            $item->save();
        }
    }

?>