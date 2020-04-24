<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");

  
function showchartbuy($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $DateBuy_Start   = $DATA["DateBuy_Start"];
  $DateBuy_End     = $DATA["DateBuy_End"];
  $type_buy_chart  = $DATA["type_buy_chart"];
  $type_buy_chart_select  = $DATA["type_buy_chart_select"];

  if($type_buy_chart =='longan') 
  {
    $Selectchart = "SELECT
                      SUM(Total) AS Total ,
                      DocDate
                    FROM
                      buy_longan 
                    WHERE
                      buy_longan.DocDate BETWEEN '$DateBuy_Start' AND '$DateBuy_End'
                    GROUP BY DocDate ";
  }
  else
  {
    $Selectchart = "SELECT
                      SUM(Total) AS Total ,
                      DocDate
                    FROM
                      buy_rice 
                    WHERE
                      buy_rice.DocDate BETWEEN '$DateBuy_Start' AND '$DateBuy_End'
                    GROUP BY DocDate ";
  }
  

  $meQuery = mysqli_query($conn, $Selectchart);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $return[$count]['Total']         = $Result['Total'];
    $return[$count]['DocDate']      = $Result['DocDate'];

    $count ++ ;
    $boolean = true;
  }
    $return['Row'] = $count;

  if ($boolean) 
  {
     
    $return['type_chart'] = $type_buy_chart_select;
    $return['status'] = "success";
    $return['form'] = "showchartbuy";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['msg'] = "showcharterror";
    $return['date'] = $datepicker;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}


function showchartsale($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $DateSale_Start   = $DATA["DateSale_Start"];
  $DateSale_End     = $DATA["DateSale_End"];
  $type_sale_chart  = $DATA["type_sale_chart"];
  $type_sale_chart_select  = $DATA["type_sale_chart_select"];

  if($type_sale_chart =='longan') 
  {
    $Selectchart = "SELECT
                      SUM(Total) AS Total ,
                      DocDate
                    FROM
                      sale_longan 
                    WHERE
                    sale_longan.DocDate BETWEEN '$DateSale_Start' AND '$DateSale_End'
                    GROUP BY DocDate ";
  }
  else
  {
    $Selectchart = "SELECT
                      SUM(Total) AS Total ,
                      DocDate
                    FROM
                      sale_rice 
                    WHERE
                      sale_rice.DocDate BETWEEN '$DateSale_Start' AND '$DateSale_End'
                    GROUP BY DocDate ";
  }
  

  $meQuery = mysqli_query($conn, $Selectchart);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $return[$count]['Total']         = $Result['Total'];
    $return[$count]['DocDate']      = $Result['DocDate'];

    $count ++ ;
    $boolean = true;
  }
    $return['Row'] = $count;

  if ($boolean) 
  {
     
    $return['type_chart'] = $type_sale_chart_select;
    $return['status'] = "success";
    $return['form'] = "showchartsale";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['msg'] = "showcharterror";
    $return['date'] = $datepicker;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}
    $data = $_POST['DATA'];
    $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'showchartbuy') 
      {
        showchartbuy($conn, $DATA);
      }
      else if($DATA['STATUS'] == 'showchartsale')
      {
        showchartsale($conn, $DATA);
      }
      else
      {
          $return['status'] = "error";
          $return['msg'] = 'noinput';
          echo json_encode($return);
          mysqli_close($conn);
          die;
      }

?>