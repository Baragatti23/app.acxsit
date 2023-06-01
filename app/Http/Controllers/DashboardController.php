<?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Traits\Setting;
use App\Models\Bordereau;
use App\Models\Client;
use App\Models\Equipement;
use App\Models\Licence;
use App\Models\Proforma;
    use Illuminate\Http\Request;

    class DashboardController extends Controller{
        use Setting;
        public function get(){
            $stats_sixmonths=[];
            $stats_year=[
                "proformas"=>[],
                "bordereaux"=>[]
            ];
            $part=intval(date("m")/6)==0?1:2;
            $year=date("Y");
            for ($i=1; $i < 13; $i++) {
                if(in_array($i,[1,3,5,7,8,10,12])) $length=31;
                elseif(in_array($i,[4,6,9,11])) $length=30;
                elseif(($i==2 && $year%4==0) || ($i==2 && $year%100==0 && $year%400==0)) $length=29;
                else $length=28;
                $start=$year."-".substr("0".$i,-2)."-01 00:00:00";
                $end=$year."-".substr("0".$i,-2)."-".$length." 23:59:59";
                // echo $start."  ---  ".$end."\n";
                $stats_year["proformas"][]=Proforma::whereBetween("created_at_proforma",[$start,$end])->count();
                $stats_year["bordereaux"][]=Bordereau::whereBetween("created_at_bordereau",[$start,$end])->count();
                if($i<=6 && $part==1) $stats_sixmonths[]=Proforma::whereBetween("created_at_proforma",[$start,$end])->count();
                elseif($i>6 && $part==2) $stats_sixmonths[]=Proforma::whereBetween("created_at_proforma",[$start,$end])->count();
            }
            // exit;
            return response()->json([
                "status"=>200,
                "data"=>[],
                "proformas"=>Proforma::count(),
                "bordereaux_livraisons"=>Bordereau::count(),
                "licences_sell"=>0,
                "products"=>Equipement::count()+Licence::count(),
                "stats_middle_year"=>$stats_sixmonths,
                "stats_year"=>$stats_year
            ],200);
        }
    }
?>