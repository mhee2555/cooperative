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
    $MonthTH = $_GET['xMonth'];
    $YearTH = $_GET['xYear'];
    $n_date = $datetime->getTHmonthFromnum($MonthTH)." พ.ศ. ".$datetime->getTHyear($YearTH);

    // $sDateTH = $_GET['sDate'];
    // $sDateTH = explode("/",$sDateTH);
    // $sDateTH = $sDateTH[0]." ".$datetime->getTHmonthFromnum($sDateTH[1])." พ.ศ. ".$datetime->getTHyear($sDateTH[2]);

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
      $this->Cell(0, 10,  "รายงานการรับเข้าคลังสินค้าแปรรูป", 0, 1, 'C');
      $this->SetFont('thsarabun', 'b', 20);
      $this->Cell(0, 10,  "ประจำเดือน ".$n_date, 0, 1, 'C');
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

$sMonth = $_GET['xMonth'];
$sYear = $_GET['xYear'];



// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('P', 'A4');







$pdf->Ln(20);
$html = '<table cellspacing="0" cellpadding="2" border="1" >
<thead><tr style="font-size:20px;font-weight: bold;background-color: #8B8989;">
<th  width="10 %" align="center">ลำดับ</th>
<th  width="20 %" align="center">วันที่</th>
<th  width="18 %" align="center">ประเภท</th>
<th  width="18 %" align="center">จำนวน</th>
<th  width="18 %" align="center">จำนวนคงเหลือ</th>
<th  width="18 %" align="center">หน่วยนับ</th>



</tr> </thead>';

  $Sql_Detail="SELECT
                DATE(stock_process.Date_start) AS date_s,
                TIME(stock_process.Date_start) AS date_Ts,
                Sum(stock_process.item_qty) AS total_qty,
                Sum(stock_process.item_ccqty) AS total_qtycc,
                type_item.type_name,
                item_unit.UnitName
                FROM
                stock_process
                INNER JOIN item ON stock_process.item_code = item.item_code
                INNER JOIN type_item ON item.item_type = type_item.id
                INNER JOIN item_unit ON stock_process.UnitCode = item_unit.UnitCode
                WHERE
                MONTH(stock_process.Date_start ) ='$sMonth'
                AND YEAR(stock_process.Date_start ) ='$sYear'
                GROUP BY
                DATE(stock_process.Date_start)
                ORDER BY
                DATE(stock_process.Date_start) ASC,
                stock_process.DocNo ASC
  
              ";
              $sump=0;
              $sumqty=0;
  $meQuery2 = mysqli_query($conn,$Sql_Detail);
while ($Result_Detail = mysqli_fetch_assoc($meQuery2)) {




  $html .= '<tr nobr="true" style="font-size:18px;">';
  $html .=   '<td width="10 %" align="center">' . $count . '</td>';
  $html .=   '<td width="20 %" align="center"> '.$Result_Detail['date_s'].'</td>';
  $html .=   '<td width="18 %" align="center">'.$Result_Detail['type_name'].'</td>';
  $html .=   '<td width="18 %" align="right">'.number_format($Result_Detail['total_qty'],0).'</td>';
  $html .=   '<td width="18 %" align="right">'.number_format($Result_Detail['total_qtycc'],2).'</td>';
  $html .=   '<td width="18 %" align="center">'.$Result_Detail['UnitName'].'</td>';


  $html .=  '</tr>';
  $count++;
  $sump += $Result_Detail['total_qtycc'];
  $sumqty += $Result_Detail['total_qty'];
}
$html .= '<tr nobr="true" style="background-color: #CDCDC1;font-size:18px;" >';
  $html .=   '<td width="10 %" align="center"></td>';
  $html .=   '<td width="20 %" align="center"></td>';
  $html .=   '<td width="18 %" align="center" style="font-weight: bold;">รวม</td>';
  $html .=   '<td width="18 %" align="right" style="font-weight: bold;">'.number_format($sumqty,0).'</td>';
  $html .=   '<td width="18 %" align="right" style="font-weight: bold;">'.number_format($sump,2).'</td>';
  $html .=   '<td width="18 %" align="center"></td>';

  $html .=  '</tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');

// $pdf->SetLineWidth(0.3);
// $pdf->sety($pdf->Gety() - 7.0);
// ---------------------------------------------------------

// $pdf->Ln(10);
// $pdf->SetFont('thsarabun', 'b', 16);
// $pdf->Cell(150, 7,  "ผู้อนุมัติการขอเบิก : ", 0, 0, 'R');
// $pdf->SetFont('thsarabun', '', 16);
// $pdf->Cell(20, 7, $Ap_FName, 0, 1, 'L');
// $pdf->Ln(5);
// $pdf->SetFont('thsarabun', 'b', 16);
// $pdf->Cell(135, 7,  "ผู้ส่งของ : ", 0, 0, 'R');
// $pdf->SetFont('thsarabun', '', 16);
// $pdf->Cell(20, 7,'(. . . . . . . . . . . . . . . . . . . . .)', 0, 1, 'L');

// $pdf->SetFont('thsarabun', 'b', 16);
// $pdf->Cell(135, 7,  "วันที่ : ", 0, 0, 'R');
// $pdf->SetFont('thsarabun', '', 16);
// $pdf->Cell(20, 7, '(  . . .  / . . .  / . . . . . .  )', 0, 1, 'L');

//Close and output PDF document
$eDate = $_GET['eDate'];
$eDate=str_replace("-","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('Report_draw_longan_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
