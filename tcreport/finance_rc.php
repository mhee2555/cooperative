<?php
session_start();
require('tcpdf/tcpdf.php');
require('../connect/connect.php');
require('Class.php');
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/Bangkok");
//--------------------------------------------------------------------------




//print_r($data);

//--------------------------------------------------------------------------
class MYPDF extends TCPDF
{
  protected $last_page_flag = false;

  public function Close()
  {
    $this->last_page_flag = true;
    parent::Close();
  }
  //Page header
  public function Header()
  {
    $datetime = new DatetimeTH();
    $eDateTH = $_GET['eDate'];
    $eDateTH = explode("-",$eDateTH);
    $eDateTH = $eDateTH[2]." ".$datetime->getTHmonthFromnum($eDateTH[1])." พ.ศ. ".$datetime->getTHyear($eDateTH[0]);

    $sDateTH = $_GET['sDate'];
    $sDateTH = explode("-",$sDateTH);
    $sDateTH = $sDateTH[2]." ".$datetime->getTHmonthFromnum($sDateTH[1])." พ.ศ. ".$datetime->getTHyear($sDateTH[0]);

    if ($this->page == 1)
    {
      // Logo
      // $image_file = "../report_linen/images/Nhealth_linen 4.0.png";
      // $this->Image($image_file, 10, 10, 33, 12, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
      // Set font
      $printdate = date('d')." ".$datetime->getTHmonth(date('F'))." พ.ศ. ".$datetime->getTHyear(date('Y'));

      $this->Ln(5);
      $this->SetFont('thsarabun', '', 12);
      // Title
      $this->Cell(0, 10,  "วันที่พิมพ์รายงาน " . $printdate, 0, 1, 'R');

      $this->SetFont('thsarabun', 'b', 22);
      $this->Cell(0, 10,  "รายงานรายรับรายจ่ายการซื้อขาย ข้าว", 0, 1, 'C');
      $this->SetFont('thsarabun', 'b', 20);
      $this->Cell(0, 10,  "ประจำวันที่ ".$sDateTH." ถึง ".$eDateTH, 0, 1, 'C');
      $this->Ln(10);

    }
  }
  // Page footer
  public function Footer()
  {

    $this->SetY(-25);
    // Arial italic 8
    $this->SetFont('thsarabun', 'i', 12);
    // Page number

    $this->Cell(190, 10,  "หน้า" . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
  }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Report Cost Department');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 30);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// ------------------------------------------------------------------------------


$count = 1;
// ------------------------------------------------------------------------------

$sDate = $_GET['sDate'];
$sDate = explode("-",$sDate);
$sDate = $sDate[0].'-'.$sDate[1].'-'.$sDate[2];

$eDate = $_GET['eDate'];
$eDate = explode("-",$eDate);
$eDate = $eDate[0].'-'.$eDate[1].'-'.$eDate[2];



// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('P', 'A4');


$begin = new DateTime( $sDate );
$end = new DateTime( $eDate );
$end = $end->modify( '1 day' );

$interval = new DateInterval('P1D');
$period = new DatePeriod($begin, $interval ,$end);
foreach ($period as $key => $value)
{
  $date[] = $value->format('Y-m-d');
}


$Sum_total_buy=0;

$pdf->Ln(20);
$pdf->SetFont('thsarabun', 'B', 16);
$pdf->Cell(45, 10,  "รายรับรายจ่ายการซื้อขาย :", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(0, 10,  "ข้าว", 0, 1, 'L');

$html = '<table cellspacing="0" cellpadding="2" border="1" >
<thead><tr style="font-size:20px;font-weight: bold;background-color: #8B8989;">
<th  width="10 %" align="center">ลำดับ</th>
<th  width="18 %" align="center">วันที่</th>
<th  width="18 %" align="center">รายรับ</th>
<th  width="18 %" align="center">รายจ่าย</th>
<th  width="18 %" align="center">ส่วนต่าง</th>
<th  width="18 %" align="center">หน่วยนับ</th>
</tr> </thead>';
$count=1;
$total_b_rc=0;
$total_s_rc=0;
$total_sum2=0;
foreach ($date as $key => $value)
{
  $Sql_buy_rice="SELECT
                    buy_rice.DocDate,
                    SUM(buy_rice.Total) AS b_rc
                    FROM
                    buy_rice
                    WHERE DATE(buy_rice.DocDate) = '$value' 
                    GROUP BY  buy_rice.DocDate
                    ORDER BY buy_rice.DocDate ASC
              ";
  
  $meQuery_buy_rice = mysqli_query($conn,$Sql_buy_rice);
  $Result_buy_rice = mysqli_fetch_assoc($meQuery_buy_rice);

  $b_rc = $Result_buy_rice['b_rc'];

  $Sql_sale_rice="SELECT
                  sale_rice.DocDate,
                  SUM(sale_rice.Total) AS s_rc
                  FROM
                  sale_rice
                  WHERE DATE(sale_rice.DocDate) = '$value' 
                  GROUP BY  sale_rice.DocDate
                  ORDER BY sale_rice.DocDate ASC
                  ";

  $meQuery_sale_rice = mysqli_query($conn,$Sql_sale_rice);
  $Result_sale_rice = mysqli_fetch_assoc($meQuery_sale_rice);

  $s_rc = $Result_sale_rice['s_rc'];

  $total_sum = $s_rc-$b_rc;

  $html .= '<tr nobr="true" style="font-size:18px;">';
  $html .=   '<td width="10 %" align="center">' . $count . '</td>';
  $html .=   '<td width="18 %" align="center"> '.$value.'</td>';
  $html .=   '<td width="18 %" align="right">'.number_format($s_rc,2).'</td>';
  $html .=   '<td width="18 %" align="right">'.number_format($b_rc,2).'</td>';
  $html .=   '<td width="18 %" align="right">'.number_format($total_sum,2).'</td>';
  $html .=   '<td width="18 %" align="center">บาท</td>';
  $html .=  '</tr>';
  $count++;
  $total_b_rc +=  $b_rc;
  $total_s_rc += $s_rc;

  $total_sum2 +=  $total_sum ; 
}
$html .= '<tr nobr="true" style="background-color: #CDCDC1;font-size:18px;" >';
  $html .=   '<td width="10 %" align="center"></td>';
  $html .=   '<td width="18 %" align="center" style="font-weight: bold;">รวม</td>';
  $html .=   '<td width="18 %" align="right" style="font-weight: bold;">'.number_format($total_s_rc,2).'</td>';
  $html .=   '<td width="18 %" align="right" style="font-weight: bold;">'.number_format($total_b_rc,2).'</td>';
  $html .=   '<td width="18 %" align="right" style="font-weight: bold;">'.number_format($total_sum2,2).'</td>';
  $html .=   '<td width="18 %" align="center"></td>';
  $html .=  '</tr>';
$html .= '</table>';
$pdf->writeHTML($html, true, false, false, false, '');

$pdf->Ln();
$pdf->SetFont('thsarabun', 'B', 18);
$pdf->Cell(0, 10,  "สรุป", 0, 1, 'L');

$html = '<table cellspacing="0" cellpadding="2" border="1" >
<thead><tr style="font-size:20px;font-weight: bold;background-color: #8B8989;">
<th  width="25 %" align="center">รวมรายได้ขายผลผลิต</th>
<th  width="25 %" align="center">รวมค่าใช้จ่ายซื้อผลผลิต</th>
<th  width="25 %" align="center">รวมส่วนต่าง</th>
<th  width="20 %" align="center">หน่วยนับ</th>
</tr> </thead>';

  $html .= '<tr nobr="true" style="font-size:18px;">';
  $html .=   '<td width="25 %" align="center">' .number_format($total_s_rc,2). '</td>';
  $html .=   '<td width="25 %" align="center"> '.number_format($total_b_rc,2).'</td>';
  $html .=   '<td width="25 %" align="center"> '.number_format($total_sum2,2).'</td>';
//   $html .=   '<td width="25 %" align="right">'.number_format($Result_Detail['t_lg'],2).'</td>';
  $html .=   '<td width="20 %" align="center">บาท</td>';
  $html .=  '</tr>';
  $count++;
  $total_lg += $Result_Detail['t_lg'];

$html .= '</table>';
$pdf->writeHTML($html, true, false, false, false, '');

//Close and output PDF document
$eDate = $_GET['eDate'];
$eDate=str_replace("-","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('finance_rc_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
