<?php
session_start();
require('tcpdf/tcpdf.php');
require('../connect/connect.php');
require('Class.php');
require('baht_text.php');
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
    $eDate = $_GET['eDate'];
    $eDate = explode("-",$eDate);
    $edate = $eDate[2]." ".$datetime->getTHmonthFromnum($eDate[1])." พ.ศ. ".$datetime->getTHyear($eDate[0]);
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
      $this->Cell(0, 10,  "สหกรณ์การเกษรสันป่าตอง จำกัด", 0, 1, 'C');
      $this->SetFont('thsarabun', 'b', 18);
      $this->Cell(0, 10,"ที่ตั้ง เลขที่ 238 ม.10 ต.ยุหว่า อ.สันป่าตอง จ.เชียงใหม่ 50120 โทร.053-106088", 0, 1, 'C');
      $this->SetFont('thsarabun', 'b', 22);
      $this->Cell(0, 10,"ใบสำคัญซื้อสินค้าข้าว", 0, 1, 'C');
      $this->Ln(100);

    }
  }
  // Page footer
  public function Footer()
  {
    $Employee = $_GET['Employee'];
    $this->SetY(-28);
    // Arial italic 8
    $this->SetFont('thsarabun', 'b', 16);
    // Page number
    $this->Cell(180, 12,  "", 0, 0, 'L');
    $this->Cell(35, 12,  "ผู้รับสินค้า ", 0, 0, 'L');
    $this->Cell(60, 12, $Employee, 0, 1, 'L');
    $this->Cell(200, 12,  "", 0, 0, 'L');
    $this->SetFont('thsarabun', '', 16);
    $this->Cell(120, 12,  "(. . . . . . . . . . . . . . . . . . . . . . . . . . )", 0, 1, 'L');
    
  }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('Report Buy Rice');
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
$eDate = explode("-",$eDate);
$eDate = $eDate[0].'-'.$eDate[1].'-'.$eDate[2];

$DocNo = $_GET['DocNo'];
// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('L', 'A4');
$Sum_weight=0;
$Sum=0;
  $query = "SELECT
              buy_rice.DocNo,
              buy_rice_detail.kilo,
              buy_rice_detail.Sumtotal,
              buy_rice_detail.moisture,
              users.ID,
              users.FName AS uname,
              item.item_name,
              buy_rice.DocDate,
              buy_rice.weight_all,
              buy_rice.weight_car,
              buy_rice.DocNo_car,
              employee.FName,
              buy_rice.Total,
              grade_price_rice.Grade
              FROM
              buy_rice
              INNER JOIN buy_rice_detail ON buy_rice.DocNo = buy_rice_detail.Buy_DocNo
              INNER JOIN users ON buy_rice.Customer_ID = users.ID
              INNER JOIN item ON buy_rice_detail.item_code = item.item_code
              INNER JOIN employee ON buy_rice.Employee_ID = employee.ID
              INNER JOIN grade_price_rice ON buy_rice_detail.item_code = grade_price_rice.item_code
              WHERE buy_rice.DocNo='$DocNo'
              ";

    $meQuery = mysqli_query($conn,$query);
    $Result = mysqli_fetch_assoc($meQuery);

    $datetime = new DatetimeTH();
    $Date = $Result['DocDate'];
    $Date = explode("-",$Date);
    $Date = $Date[2]." ".$datetime->getTHmonthFromnum($Date[1])." พ.ศ. ".$datetime->getTHyear($Date[0]);


    $Sum_weight = $Result['weight_all']-$Result['weight_car'];
    $Sum = $Result['Sumtotal']-$Result['Total'];

$pdf->Ln(35);
$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "เลขที่เอกสาร : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,$Result['DocNo'], 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(15, 12,  "วันที่ : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,$Date, 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ผู้ขาย : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,  $Result['uname'], 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(35, 12,  "เลขทะเบียนสมาชิก : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,  $Result['ID'], 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ชนิดสินค้า : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,  $Result['item_name'], 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ทะเบียนรถ : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,  $Result['DocNo_car'], 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(30, 12,  "น้ำหนักบรรทุก : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,  number_format($Result['weight_all'],2) ." กก.", 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(30, 12,  "น้ำหนักรถ : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(115, 12,  number_format($Result['weight_car'],2) ." กก.", 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ราคาต่อหน่วย : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 12,  number_format($Result['Grade'],2), 0, 0, 'R');
$pdf->Cell(10, 12,  "บาท", 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ราคารวม :", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 12,  number_format($Result['Sumtotal'],2), 0, 0, 'R');
$pdf->Cell(10, 12,  "บาท", 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(35, 12,  "น้ำหนักข้าวเปลือก : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(115, 12,  number_format($Result['kilo'],2)." กก.", 0, 0, 'L');


$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "หักความชื่น : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 12,  $Result['moisture'], 0, 0, 'R');
$pdf->Cell(10, 12,  "%", 0, 0, 'L');
$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "จำนวน : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 12,  number_format($Sum,2), 0, 0, 'R');
$pdf->Cell(50, 12,  "บาท", 0, 1, 'L');

$pdf->Cell(25, 12,  "", 0, 0, 'L');
$pdf->Cell(25, 12,  "", 0, 0, 'L');
$pdf->Cell(10, 12,  "", 0, 0, 'L');
$pdf->Cell(50, 12,  "", 0, 0, 'L');
$pdf->Cell(95, 12,  "", 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "คิดเป็นเงิน : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 12, number_format($Result['Total'],2), 0, 0, 'R');
$pdf->Cell(10, 12,  "บาท", 0, 1, 'L');


$textTotal =baht_text( $Result['Total'] );
$pdf->Cell(210, 12,  "", 0, 0, 'L');
$pdf->Cell(120, 12,  "(".$textTotal.")", 0, 1, 'L');
// ---------------------------------------------------------


//Close and output PDF document
$eDate = $_GET['eDate'];
$eDate=str_replace("-","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('Report_Buy_Rice_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
