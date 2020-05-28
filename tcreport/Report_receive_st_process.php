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
    // $eDateTH = $_GET['eDate'];
    // $eDateTH = explode("/",$eDateTH);
    // $eDateTH = $eDateTH[0]." ".$datetime->getTHmonthFromnum($eDateTH[1])." พ.ศ. ".$datetime->getTHyear($eDateTH[2]);

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
      $this->Cell(0, 10,  "รายงานการรับเข้าคลังสินค้าแปรรูป", 0, 1, 'C');
      $this->SetFont('thsarabun', 'b', 20);
      $this->Cell(0, 10,  "ประจำวันที่ ".$sDateTH, 0, 1, 'C');
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

// $eDate = $_GET['eDate'];
// $eDate = explode("/",$eDate);
// $eDate = $eDate[2].'-'.$eDate[1].'-'.$eDate[0];



// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('P', 'A4');







$pdf->Ln(20);
$html = '<table cellspacing="0" cellpadding="2" border="1" >
<thead><tr style="font-size:18px;font-weight: bold;background-color: #8B8989;">
<th  width="8 %" align="center">ลำดับ</th>
<th  width="12 %" align="center">รหัสคลังสินค้า</th>
<th  width="11 %" align="center">รายการ</th>
<th  width="12 %" align="center">เวลารับเข้า</th>
<th  width="12 %" align="center">จำนวน</th>
<th  width="16 %" align="center">จำนวนคงเหลือ</th>
<th  width="15 %" align="center">หน่วยนับ</th>
<th  width="15 %" align="center">เลขที่เอกสาร</th>

</tr> </thead>';

  $Sql_Detail="SELECT
                stock_process.stock_code,
                TIME(stock_process.Date_start) AS tDate,
                stock_process.item_qty,
                stock_process.item_ccqty,
                stock_process.DocNo,
                item.item_name,
                item_unit.UnitName
                FROM
                stock_process
                INNER JOIN item ON stock_process.item_code = item.item_code
                INNER JOIN item_unit ON stock_process.UnitCode = item_unit.UnitCode
                WHERE
                DATE(stock_process.Date_start) = '$sDate'
                ORDER BY
                DATE(stock_process.Date_start) ASC,
                stock_process.DocNo ASC,
                stock_process.item_code ASC
              ";
              $sump=0;
              $sumqty=0;
  $meQuery2 = mysqli_query($conn,$Sql_Detail);
while ($Result_Detail = mysqli_fetch_assoc($meQuery2)) {




  $html .= '<tr nobr="true" style="font-size:16px;">';
  $html .=   '<td width="8 %" align="center">' . $count . '</td>';
  $html .=   '<td width="12 %" align="center"> '.$Result_Detail['stock_code'].'</td>';
  $html .=   '<td width="11 %" align="center">'.$Result_Detail['item_name'].'</td>';
  $html .=   '<td width="12 %" align="center">'.$Result_Detail['tDate'].'</td>';
  $html .=   '<td width="12 %" align="right">'.number_format($Result_Detail['item_qty'],0).'</td>';
  $html .=   '<td width="16 %" align="right">'.number_format($Result_Detail['item_ccqty'],0).'</td>';
  $html .=   '<td width="15 %" align="center">'.$Result_Detail['UnitName'].'</td>';
  $html .=   '<td width="15 %" align="center">'.$Result_Detail['DocNo'].'</td>';

  $html .=  '</tr>';
  $count++;
  $sump += $Result_Detail['item_ccqty'];
  $sumqty += $Result_Detail['item_qty'];
}
$html .= '<tr nobr="true" style="background-color: #CDCDC1;font-size:16px;" >';
  $html .=   '<td width="8 %" align="center"></td>';
  $html .=   '<td width="12 %" align="center"></td>';
  $html .=   '<td width="11 %" align="center"></td>';
  $html .=   '<td width="12 %" align="center" style="font-weight: bold;">รวม</td>';
  $html .=   '<td width="12 %" align="right" style="font-weight: bold;">'.number_format($sumqty,0).'</td>';
  $html .=   '<td width="16 %" align="right" style="font-weight: bold;">'.number_format($sump,0).'</td>';
  $html .=   '<td width="15 %" align="center"></td>';
  $html .=   '<td width="15 %" align="center" ></td>';
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
