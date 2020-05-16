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
    $eDateTH = explode("/",$eDateTH);
    $eDateTH = $eDateTH[0]." ".$datetime->getTHmonthFromnum($eDateTH[1])." พ.ศ. ".$datetime->getTHyear($eDateTH[2]);

    $sDateTH = $_GET['sDate'];
    $sDateTH = explode("/",$sDateTH);
    $sDateTH = $sDateTH[0]." ".$datetime->getTHmonthFromnum($sDateTH[1])." พ.ศ. ".$datetime->getTHyear($sDateTH[2]);

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
      $this->Cell(0, 10,  "รายงานการขอเบิกลำใย", 0, 1, 'C');
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



// ------------------------------------------------------------------------------

$sDate = $_GET['sDate'];
$sDate = explode("/",$sDate);
$sDate = $sDate[2].'-'.$sDate[1].'-'.$sDate[0];

$eDate = $_GET['eDate'];
$eDate = explode("/",$eDate);
$eDate = $eDate[2].'-'.$eDate[1].'-'.$eDate[0];



// --------------------------------------------------------
// set font
// add a page
$pdf->AddPage('P', 'A4');


    $Sql_itemCode="SELECT
                    draw_detail_sub.item_code,
                    item.item_name
                    FROM
                    draw
                    INNER JOIN draw_detail_sub ON draw.DocNo = draw_detail_sub.draw_DocNo
                    INNER JOIN item ON draw_detail_sub.item_code = item.item_code
                    WHERE
                    DATE( draw.DocDate ) BETWEEN '$sDate' AND '$eDate'
                    GROUP BY
                    draw_detail_sub.item_code
                    ORDER BY
                    draw_detail_sub.item_code ASC
                ";
 $pdf->Ln(20);
    $meQuery = mysqli_query($conn,$Sql_itemCode);
    while ($Result_itemCode = mysqli_fetch_assoc($meQuery)) {
   
        $item_code = $Result_itemCode['item_code'];

    $pdf->Ln(5);
    $pdf->SetFont('thsarabun', 'B', 16);
    $pdf->Cell(15, 10,  "สินค้า :", 0, 0, 'L');
    $pdf->SetFont('thsarabun', '', 16);
    $pdf->Cell(0, 10,$Result_itemCode['item_name'], 0, 1, 'L');

       
        $html = '<table cellspacing="0" cellpadding="2" border="1" >
        <thead><tr style="font-size:20px;font-weight: bold;background-color: #8B8989;">
        <th  width="10 %" align="center">ลำดับ</th>
        <th  width="12 %" align="center">วันที่</th>
        <th  width="15 %" align="center">เลขที่เอกสาร</th>
        <th  width="13 %" align="center">จำนวนขอเบิก</th>
        <th  width="12 %" align="center">หน่วยนับ</th>
        <th  width="15 %" align="center">พนักงานซื้อ</th>
        <th  width="13 %" align="center">สถานะ</th>
        <th  width="13 %" align="center">ผู้อนุมัติ</th>
        </tr> </thead>';

        $Sql_Detail="SELECT
                        draw.DocNo,
                        draw.DocDate,
                        Sum(draw_detail_sub.kilo) AS total_qty,
                        draw.IsStatus,
                        draw.Approver_ID,
                        item_unit.UnitName,
                        employee.FName
                        FROM
                        draw
                        INNER JOIN draw_detail_sub ON draw.DocNo = draw_detail_sub.draw_DocNo
                        INNER JOIN item_unit ON draw_detail_sub.UnitCode = item_unit.UnitCode
                        INNER JOIN employee ON draw.Employee_ID = employee.ID 
                        WHERE
                        DATE( draw.DocDate ) BETWEEN '$sDate' AND '$eDate'
                        AND draw_detail_sub.item_code='$item_code'
                        GROUP BY
                        draw.DocNo
                        ORDER BY
                        draw.DocDate ASC
                    ";
                    $sump=0;
                    $sumqty=0;
                    $count = 1;
        $meQuery2 = mysqli_query($conn,$Sql_Detail);
        while ($Result_Detail = mysqli_fetch_assoc($meQuery2)) {

        $Approver_ID = $Result_Detail['Approver_ID'];

            $Sql_ap="
            SELECT
            employee.FName
            FROM
            employee
            WHERE employee.ID='$Approver_ID'";

            $meQuery_ap = mysqli_query($conn,$Sql_ap);
            $Result_ap = mysqli_fetch_assoc($meQuery_ap);

            $ap_name = $Result_ap['FName'];

        $html .= '<tr nobr="true" style="font-size:18px;">';
        $html .=   '<td width="10 %" align="center">' . $count . '</td>';
        $html .=   '<td width="12 %" align="center"> '.$Result_Detail['DocDate'].'</td>';
        $html .=   '<td width="15 %" align="center">'.$Result_Detail['DocNo'].'</td>';
        $html .=   '<td width="13 %" align="right">'.number_format($Result_Detail['total_qty'],0).'</td>';
        $html .=   '<td width="12 %" align="center">'.$Result_Detail['UnitName'].'</td>';
        $html .=   '<td width="15 %" align="center">'.$Result_Detail['FName'].'</td>';
        
        if($Result_Detail['IsStatus']==0)
        {
          $IsStatus="ยังไม่ได้บันทึก";
        }
        else if($Result_Detail['IsStatus']==1)
        {
          $IsStatus="รออนุมัติ";
        }
        else if($Result_Detail['IsStatus']==2)
        {
          $IsStatus="อนุมัติเรียบร้อย";
        }
        else if($Result_Detail['IsStatus']==8)
        {
          $IsStatus="ปฎิเสธการขอเบิก";
        }
        else if($Result_Detail['IsStatus']==9)
        {
          $IsStatus="ยกเลิกเอกสาร";
        }
        $html .=   '<td width="13 %" align="center">'.$IsStatus.'</td>';
        $html .=   '<td width="13 %" align="center">'.$ap_name.'</td>';
        $html .=  '</tr>';
        $count++;
        $sump += $Result_Detail['Total'];
        $sumqty += $Result_Detail['total_qty'];
        }
        $html .= '<tr nobr="true" style="background-color: #CDCDC1;font-size:18px;" >';
        $html .=   '<td width="10 %" align="center"></td>';
        $html .=   '<td width="12 %" align="center"></td>';
        $html .=   '<td width="15 %" align="center" style="font-weight: bold;">รวม</td>';
        $html .=   '<td width="13 %" align="right" style="font-weight: bold;">'.number_format($sumqty,0).'</td>';
        $html .=   '<td width="12 %" align="center"></td>';
        $html .=   '<td width="15 %" align="center" ></td>';
        $html .=   '<td width="13 %" align="center" ></td>';
        $html .=   '<td width="13 %" align="center" ></td>';
        $html .=  '</tr>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, false, false, '');
}
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
$eDate=str_replace("/","_",$eDate);
$ddate = date('d_m_Y');
$pdf->Output('Report_draw_longan_' . $eDate . '.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
