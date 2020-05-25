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
    $eDate = explode("/",$eDate);
    $edate = $eDate[0]." ".$datetime->getTHmonthFromnum($eDate[1])." พ.ศ. ".$datetime->getTHyear($eDate[2]);
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
      $this->Cell(0, 10,"ใบสำคัญขายสินค้าลำไย", 0, 1, 'C');
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
    $this->Cell(35, 12,  "ผู้ขายสินค้า ", 0, 0, 'L');
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
$pdf->SetTitle('Report Buy Longan');
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

$DocNo = $_GET['DocNo'];
// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('L', 'A4');

  $query = "SELECT
            bp.DocNo,
            bp.DocDate,
            TIME(bp.Modify_Date) AS  Modify_Date, 
            users.ID AS customer,
            emp.FName AS employee ,
            bp.IsStatus,
            bp.Total
            FROM
            sale_longan bp
            INNER JOIN employee emp ON emp.ID = bp.Employee_ID
            INNER JOIN users ON users.ID = bp.Customer_ID
            WHERE bp.DocNo = '$DocNo'
              ";

    $meQuery = mysqli_query($conn,$query);
    $Result = mysqli_fetch_assoc($meQuery);

    $datetime = new DatetimeTH();
    $Date = $Result['DocDate'];
    $Date = explode("-",$Date);
    $Date = $Date[2]." ".$datetime->getTHmonthFromnum($Date[1])." พ.ศ. ".$datetime->getTHyear($Date[0]);



$pdf->Ln(35);
$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "เลขที่เอกสาร : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(160, 12,$DocNo, 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(15, 12,  "วันที่ : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12,$Date, 0, 1, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ชื่อลูกค้า : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(160, 12,  $Result['employee'], 0, 0, 'L');


$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 12,  "ชนิดสินค้า : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(120, 12, "ลำไยอบแห้ง", 0, 1, 'L');
$pdf->Cell(0,0,'','T');  
$pdf->Ln(5);

        $html = '<table cellspacing="0" cellpadding="2" border="0" >
        <tr style="font-size:18px;font-weight: bold;background-color: #a5a5a5;">
        <th  width="10 %" align="center">ลำดับ</th>
        <th  width="20 %" align="left">รายการ</th>
        <th  width="10 %" align="center">จำนวน</th>
        <th  width="13 %" align="center">หน่วยนับ</th>
        <th  width="13 %" align="center">ราคาต่อหน่วย</th>
        <th  width="15 %" align="center">ราคารวม</th>
        <th  width="15 %" align="left">รหัสคลังสินค้า</th>
        </tr>
        ';

        $query_detail = "SELECT
                        sale_longan_detail.Sale_DocNo,
                        item.item_name,
                        sale_longan_detail.kilo,
                        packge_unit.PackgeName,
                        sale_longan_detail.total,
                        stock_package.DocNo,
                        packge_unit.Priceperunit
                        FROM
                        sale_longan_detail
                        INNER JOIN item ON sale_longan_detail.item_code = item.item_code
                        INNER JOIN packge_unit ON sale_longan_detail.PackgeCode = packge_unit.PackgeCode
                        INNER JOIN stock_package ON sale_longan_detail.stock_code = stock_package.stock_code
                        WHERE
                        sale_longan_detail.Sale_DocNo = '$DocNo'
                    ";
$c=1;
        $meQuery = mysqli_query($conn,$query_detail);
        while ($Result2 = mysqli_fetch_assoc($meQuery)) 
        {

  $html .= '
            <tr style="font-size:18px;font-weight:">
            <td  width="10 %" align="center">'.$c.'</td>
            <td  width="20 %" align="left">'.$Result2['item_name'].' </td>
            <td  width="10 %" align="center">'.number_format($Result2['kilo'],0).' </td>
            <td  width="11 %" align="center">'.$Result2['PackgeName'].'</td>
            <td  width="15 %" align="center">'.number_format($Result2['Priceperunit'],2).'</td>
            <td  width="15 %" align="center">'.number_format($Result2['total'],2).'</td>
            <td  width="15 %" align="left">'.$Result2['DocNo'].'</td>
            </tr>
            ';

            $c++;
        }

   $html .= '</table>';

$pdf->SetX(15);   
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Cell(0,0,'','T');
$pdf->Ln();  
$pdf->Cell(25, 12,  "", 0, 0, 'L');
$pdf->Cell(25, 12,  "", 0, 0, 'L');
$pdf->Cell(10, 12,  "", 0, 0, 'L');
$pdf->Cell(50, 12,  "", 0, 0, 'L');
$pdf->Cell(95, 12,  "", 0, 0, 'L');

$pdf->SetFont('thsarabun', 'b', 16);
$pdf->Cell(25, 5,  "คิดเป็นเงิน : ", 0, 0, 'L');
$pdf->SetFont('thsarabun', '', 16);
$pdf->Cell(25, 5, number_format($Result['Total'],2), 0, 0, 'R');
$pdf->Cell(10, 5,  "บาท", 0, 1, 'L');


$textTotal =baht_text( $Result['Total'] );
$pdf->Cell(145, 5,  "", 0, 0, 'L');
$pdf->Cell(120, 5,  "(".$textTotal.")", 0, 1, 'R');
// ---------------------------------------------------------


//Close and output PDF document
$eDate = $_GET['eDate'];
$eDate=str_replace("/","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('Report_Buy_Longan_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
