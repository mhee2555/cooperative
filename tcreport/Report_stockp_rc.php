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
    // $eDate = $_GET['eDate'];
    // $eDate = explode("/",$eDate);
    // $edate = $eDate[0]." ".$datetime->getTHmonthFromnum($eDate[1])." พ.ศ. ".$datetime->getTHyear($eDate[2]);
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
      $this->Cell(0, 10,  "เอกสารรายละเอียดการรับเข้าแปรรูปข้าว", 0, 1, 'C');
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

$eDate = $_GET['eDate'];
$eDate = explode("/",$eDate);
$eDate = $eDate[2].'-'.$eDate[1].'-'.$eDate[0];

$datetime = new DatetimeTH();


$DocNo = $_GET['DocNo'];
// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('P', 'A4');




$Sql = "SELECT
        sl.DocNo,
        sl.DocDate,
        TIME(sl.Modify_Date) AS  Modify_Date, 
        emp.FName AS employee ,
        sl.IsStatus ,
        sl.RefDocNo 
        FROM
        stockp_rice sl
        INNER JOIN employee emp ON emp.ID = sl.Employee_ID
        WHERE sl.DocNo = '$DocNo' ";


$pdf->Ln(20);
$meQuery1 = mysqli_query($conn,$Sql);
$Result1 = mysqli_fetch_assoc($meQuery1);


  $datadocdate = $Result1['DocDate'];
  $employee = $Result1['employee'];
  $Modify_Date = $Result1['Modify_Date'];
  $RefDocNo = $Result1['RefDocNo'];
  


  $datadocdate = explode("-",$datadocdate);
  $datadocdate = $datadocdate[2]." ".$datetime->getTHmonthFromnum($datadocdate[1])." พ.ศ. ".$datetime->getTHyear($datadocdate[0]);

  $pdf->SetFont('thsarabun', 'b', 16);
  $pdf->Cell(35, 7,  "เลขที่เอกสารรับเข้า : ", 0, 0, 'L');
  $pdf->SetFont('thsarabun', '', 16);
  $pdf->Cell(75, 7, $DocNo, 0, 0, 'L');
  
  $pdf->SetFont('thsarabun', 'b', 16);
  $pdf->Cell(45, 7,  "เลขที่เอกสารอ้างอิงแปรรูป : ", 0, 0, 'L');
  $pdf->SetFont('thsarabun', '', 16);
  $pdf->Cell(37, 7, $RefDocNo, 0, 1, 'L');
  

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(45, 7,  "วันที่บันทึกข้อมูลการรับเข้า : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(75, 7, $datadocdate, 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(12, 7,  "เวลา : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(37, 7, $Modify_Date.' น.', 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(28, 7,  "ประเภทสินค้า : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(58, 7,  "ข้าวสาร", 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(70, 7,  "ผู้บันทึกข้อมูลรับเข้า : ", 0, 0, 'R');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(20, 7,$employee, 0, 1, 'L');
$pdf->SetFont('thsarabun', '', 16);


$pdf->Ln(10);
$html = '<table cellspacing="0" cellpadding="2" border="1" >
<thead><tr style="font-size:18px;font-weight: bold;background-color: #8B8989;">
<th  width="15 %" align="center">ลำดับ</th>
<th  width="35 %" align="center">รายการ</th>
<th  width="25 %" align="center">จำนวน</th>
<th  width="25 %" align="center">หน่วยนับ</th>
</tr> </thead>';

  $Sql_Detail="SELECT
              sl.DocNo,
              TIME(sl.Modify_Date) AS Modify_Date,
              emp.FName AS employee,
              sl.RefDocNo,
              stockp_rice_detail.kilo,
              item.item_name,
              item_unit.UnitName
              FROM
              stockp_rice AS sl
              INNER JOIN employee AS emp ON emp.ID = sl.Employee_ID
              INNER JOIN stockp_rice_detail ON sl.DocNo = stockp_rice_detail.stockp_DocNo
              INNER JOIN item ON stockp_rice_detail.item_code = item.item_code
              INNER JOIN item_unit ON stockp_rice_detail.UnitCode = item_unit.UnitCode
              WHERE
              sl.DocNo = '$DocNo'
                          ";
  $meQuery2 = mysqli_query($conn,$Sql_Detail);
while ($Result_Detail = mysqli_fetch_assoc($meQuery2)) {
  $html .= '<tr nobr="true">';
  $html .=   '<td width="15 %" align="center">' . $count . '</td>';
  $html .=   '<td width="35 %" align="left"> '.$Result_Detail['item_name'].'</td>';
  $html .=   '<td width="25 %" align="center">'.$Result_Detail['kilo'].'</td>';
  $html .=   '<td width="25 %" align="center">'.$Result_Detail['UnitName'].'</td>';
  $html .=  '</tr>';
  $count++;
  $sumqty += $Result_Detail['kilo'];
}
// $html .= '<tr nobr="true" style="background-color: #CDCDC1;">';
//   $html .=   '<td width="10 %" align="center"></td>';
//   $html .=   '<td width="40 %" align="center" style="font-weight: bold;">รวม</td>';
//   $html .=   '<td width="25 %" align="center" style="font-weight: bold;">'.$sumqty.'</td>';
//   $html .=   '<td width="25 %" align="center" ></td>';
//   $html .=  '</tr>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');

// $pdf->SetLineWidth(0.3);
// $pdf->sety($pdf->Gety() - 7.0);
// ---------------------------------------------------------

$pdf->Ln(10);
$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(135, 7,  "ผู้รับของเข้าคลัง : ", 0, 0, 'R');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(20, 7,'(. . . . . . . . . . . . . . . . . . . . .)', 0, 1, 'L');
$pdf->Ln(5);
$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(135, 7,  "วันที่ : ", 0, 0, 'R');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(20, 7, '(  . . .  / . . .  / . . . . . .  )', 0, 1, 'L');

//Close and output PDF document
$eDate = $_GET['eDate'];
$eDate=str_replace("/","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('Report_stockp_lg_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
