<?php
    namespace App\Http\Controllers\PDF;
    use App\Http\Controllers\FPDF185\FPDF;
    use App\Http\Controllers\Traits\Setting;
    
    require('fpdf185/fpdf.php');
   
    class ProformaPDF extends FPDF{
        use Setting;
        private $paper=["width"=>210,"height"=>297,"ratio"=>210/97];
        private $margin=["top"=>30,"left"=>8,"right"=>18,"bottom"=>20];
        private $client="";
        private $subject="";
        private $date="";
        private $reference="";
        private $total_ht="";
        private $tva="";
        private $total_ttc="";
        private $total_ttc_letters="";
        private $delivery="";
        private $validity="";
        private $warranty="";
        private $modality="";
        private $versement="";
        public function __construct($proforma){
            $this->setProforma($proforma);
            parent::__construct();
        }
        private function setProforma($proforma){
            $this->client=$proforma['client'];
            $this->subject=$proforma['subject'];
            $this->date=$proforma['date'];
            $this->reference=$proforma['reference'];
            $this->total_ht=$proforma['total_ht'];
            $this->total_ttc=$proforma['total_ttc'];
            $this->total_ttc_letters=$proforma['total_ttc_letters'];
            $this->delivery=$proforma['delivery'];
            $this->validity=$proforma['validity'];
            $this->warranty=$proforma['warranty'];
            $this->modality=$proforma['modality'];
            $this->versement=$proforma['versement'];
        }
         // Cabecera de página
         
         function Header(){
             // Logo
             $this->SetFont('Arial','B',27);
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]);
             $this->Cell(58,14,'FACTURE',0,0,'C');
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]+12);
             $this->Cell(58,15,'PRO-FORMA');
             $this->SetXY($this->margin["left"],$this->margin["top"]+1);
             $this->SetX($this->paper["width"]-67);
             $this->Image('../App/Http/Controllers/PDF/imgs/logo.png',152,30,30);
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]+14);
             $this->SetFont('Arial','B',9);
             $this->Cell(0,10,utf8_decode('Intégration de solutions informatiques'),0,0,'R');
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]+18);
             $this->Cell(0,10,utf8_decode('Ventes d\'équipements informatiques'),0,0,'R');
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]+22);
             $this->Cell(0,10,utf8_decode('Ingéniérie réseau et systems'),0,0,'R');
             $this->SetXY($this->margin["left"]+8,$this->margin["top"]+26);
             $this->Cell(0,10,utf8_decode('Formation réseau'),0,0,'R');
 
             $this->SetFont('Arial','',9);
             $this->SetXY($this->margin["left"],$this->margin["top"]+35);
             $this->Cell(0,10,utf8_decode("RC: RB/COT/15 B 12992 réseau"),0,0,'L');
             $this->Cell(0,10,utf8_decode('Quartier DAGBEDJI, 931- Cotonou-BENIN'),0,0,'R');
             $this->SetXY($this->margin["left"],$this->margin["top"]+39.5);
             $this->Cell(0,10,utf8_decode('IFU: 3201500445819'),0,0,'L');
             $this->Cell(0,10,utf8_decode('Rue de la pharmacie de l\'Etoile'),0,0,'R');
             $this->SetXY($this->margin["left"],$this->margin["top"]+44);
             $this->Cell(0,10,utf8_decode('Numéro de compte: NSIA N° 264004231014'),0,0,'L');
             $this->Cell(0,10,utf8_decode('+229 69 61 78 78/ 91 26 30 22/63 11 22 22'),0,0,'R');
 
 
 
             $this->SetXY($this->margin["left"],$this->margin["top"]+58);
             $this->SetFont('Arial','UB',9);
             $this->Cell(23,10,utf8_decode("CLIENT :"));
             $this->SetFont('Arial','',8);
             $this->Cell(54,10,utf8_decode($this->client));
 
             $this->SetFont('Arial','UB',9);
             $this->Cell(15,10,utf8_decode("DATE :"));
             $this->SetFont('Arial','',8);
             $this->Cell(38,10,utf8_decode($this->date));
 
             $this->SetFont('Arial','UB',9);
             $this->Cell(25,10,utf8_decode("REFERENCE :"));
             $this->SetFont('Arial','',8);
             $this->Cell(38,10,utf8_decode($this->reference));
 
 
             $this->SetXY($this->margin["left"],$this->margin["top"]+63);
             $this->SetFont('Arial','UB',9);
             $this->Cell(23,10,utf8_decode("OBJET :"));
             $this->SetFont('Arial','',8);
             $this->Cell(50,10,utf8_decode($this->subject));
 
 
             $this->SetXY($this->margin["left"],$this->margin["top"]+68);
             $this->SetFont('Arial','UB',9);
             $this->Cell(23,10,utf8_decode("DEVISE :"));
             $this->SetFont('Arial','',8);
             $this->Cell(50,10,utf8_decode('XOF'));
             // Arial bold 15
             //  $this->SetFont('Arial','B',15);
             // Movernos a la derecha
             //  $this->Cell(80);
             //  $this->Cell(30,10,'Title',1,0,'C');
             // Título
             // Salto de línea
             $this->Ln(20);
             $w = array(70, 13, 13, 30,30,34);
             $header=array('DESIGNATION', 'Unité', 'Qté', 'Prix Unit. HT',"TVA", "TOTAL TTC");
             // Cabeceras
             $this->SetFillColor(240, 245, 249);
             for($i=0;$i<count($header);$i++)
                 $this->Cell($w[$i],5,utf8_decode($header[$i]),1,0,'C',1);
             $this->Ln();
         }
         public static function encoding($item){
             return mb_convert_encoding($item, "UTF", mb_detect_encoding($item));
         }
         // Pie de página
         function Footer(){
             // Posición: a 1,5 cm del final
             $this->SetY(-15);
             // Arial italic 8
             $this->SetFont('Arial','I',8);
             // Número de página
             $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
         }
         // Una tabla más completa
         function table($data){
             // Anchuras de las columnas
             $w = array(70, 13, 13, 30,30,34);
             $width_table=array_sum($w);
             // Datos
             $i=0;
            $counterItemsPage=0;
            $page=1;
             foreach($data as $row){
                 $text=[$row["designation"],$row["unity"],$row["quantity"],$row["total_ht"],$row["tva"],$row["total_ttc"]];
                 if($i%2==0) $this->SetFillColor(252);
                 else $this->SetFillColor(255);
                 for($l=0;$l<count($w);$l++){
                    $border='TLB';
                     if($l==count($w)-1){
                         $border='TBLR';
                    }
                    $this->Cell($w[$l],12,utf8_decode($text[$l]),$border,0,'C',1);
                 }
                 $this->Ln();
                 if($this->PageNo()!=$page){
                    $counterItemsPage=0;
                    $page=$this->PageNo();
                }
                $counterItemsPage++;
                 $i++;
             }
             if($counterItemsPage>=6){
                $this->AddPage();
            }
             // Foot table: Total HT, TVA, Total TTC
             $this->SetFillColor(0);
             $this->Cell($width_table,.2,"",'L',0,'C',1);
             $this->Ln();
             $this->SetFillColor(252);
             $this->Cell($width_table/2,5,"Total HT",'L',0,'L',1);
             $this->Cell($width_table/2,5,$this->total_ht,'R',0,'R',1);
             $this->Ln();
             $this->SetFillColor(0);
             $this->Cell($width_table,.2,"",'LR',0,'C',1);
             $this->Ln();
             $this->Cell($width_table/2,5,"TVA",'L',0,'L');
             $this->Cell($width_table/2,5,$this->tva,'R',0,'R');
             $this->Ln();
             $this->SetFillColor(0);
             $this->Cell($width_table,.21,"",'LR',0,'C',1);
             $this->Ln();
             $this->SetFillColor(252);
             $this->Cell($width_table/2,5,"Total TTC (en chiffres)",'L',0,'L',1);
             $this->Cell($width_table/2,5,$this->total_ttc,'R',0,'R',1);
             $this->Ln();
             $this->Cell($width_table/2,9,"Total TTC (en lettres)",'L',0,'L');
             $this->SetFont('Arial','B',10);
             $this->setTextColor(249, 162, 69);
             $this->Cell($width_table/2,9,$this->total_ttc_letters,'R',0,'R');
             $this->Ln();
             $this->setTextColor(0);
             // Línea de cierre
             $this->Cell(array_sum($w),0,'','T');
 
             // Foot table: Total HT, TVA, Total TTC
             $width_table=$width_table*0.625;
             $this->Ln();
             $this->SetFillColor(0);
             $this->Cell($width_table,5,"",'',0,'C');
             $this->Ln();
             $this->SetFont('Arial','U',8);
             $this->setFillColor(250);
             $this->Cell($width_table,5,"CONDITIONS DE VENTE",'TBRL',0,'L',1);
             $this->Ln();
             $this->SetFont('Arial','',9);
             $this->Cell(40,5,"Delai de livraison",'TBL',0,'L');
             $this->Cell($width_table-40,5,utf8_decode($this->delivery),'TBR',0,'L');
             $this->Ln();
             $this->SetFont('Arial','',9);
             $this->Cell(40,5,utf8_decode("Validité"),'BL',0,'L');
             $this->Cell($width_table-40,5,utf8_decode($this->validity),'BR',0,'L');
             $this->Ln();
             $this->SetFont('Arial','',9);
             $this->Cell(40,5,utf8_decode("Garantie"),'BL',0,'L');
             $this->Cell($width_table-40,5,utf8_decode($this->warranty),'BR',0,'L');
             $this->Ln();
             $this->SetFont('Arial','',9);
             $this->Cell(40,5,utf8_decode("Modalités"),'BL',0,'L');
             $this->Cell($width_table-40,5,utf8_decode($this->modality),'BR',0,'L');
             $this->Ln();
             $this->SetFont('Arial','',9);
             $this->Cell(40,5,utf8_decode("Versement"),'BL',0,'L');
             $this->Cell($width_table-40,5,utf8_decode($this->versement),'BR',0,'L');
             $this->Ln();
             $this->setXY($this->GetPageWidth()-55,$this->getY()+10);
            $this->SetFont('Arial','UB',9);
            $this->Cell($width_table-40,5,utf8_decode("La Direction"),'',0,'L');
            $this->Ln();
             $this->Image('../App/Http/Controllers/PDF/imgs/signature.png',$this->GetPageWidth()-60,$this->getY()+2,30);
            $this->Image('../App/Http/Controllers/PDF/imgs/cachette.png',$this->GetPageWidth()-60,$this->getY()+13,30);
         }
     }
?>