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

function showpro($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $type_Pro   = $DATA["type_Pro"];
  $Date_Pro   = $DATA["Date_Pro"];

  if($type_Pro == 'longan')
  {
    $Sql = "SELECT
              d.DocNo,
              d.DocDate,
              TIME(d.Modify_Date) AS  Modify_Date, 
              d.IsStatus
            FROM
              draw d
            WHERE d.DocDate = '$Date_Pro'
            AND  (d.IsStatus = 1 OR d.IsStatus = 2)
            AND IsRef = 0 
            ORDER BY d.DocNo DESC ";
  }
  else
  {
    $Sql = "SELECT
            d.DocNo,
            d.DocDate,
            TIME(d.Modify_Date) AS  Modify_Date, 
            d.IsStatus
          FROM
            draw_rice d
          WHERE d.DocDate = '$Date_Pro'
          AND  (d.IsStatus = 1 OR d.IsStatus = 2)
          AND IsRef = 0 
          ORDER BY d.DocNo DESC ";
  }


 $meQuery = mysqli_query($conn, $Sql);
 while ($Result = mysqli_fetch_assoc($meQuery)) 
 {
   $return[$count]['DocNo']         = $Result['DocNo'];
   $return[$count]['IsStatus']      = $Result['IsStatus'];
   $return[$count]['Modify_Date']   = $Result['Modify_Date'];

   $count ++ ;
   $boolean = true;
 }

 $return['Row'] = $count;

 if ($boolean) 
 {
    
   $return['status'] = "success";
   $return['form'] = "showpro";
   echo json_encode($return);
   mysqli_close($conn);
   die;
 }
 else
 {
   $return['status'] = "success";
   $return['form'] = "showpro";
   echo json_encode($return);
   mysqli_close($conn);
   die;
 }
}

function showpack($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $type_Pack   = $DATA["type_Pack"];
  $Date_Pack   = $DATA["Date_Pack"];

  if($type_Pack == 'longan')
  {
    $Sql = "SELECT
              d.DocNo,
              d.DocDate,
              TIME(d.Modify_Date) AS  Modify_Date, 
              d.IsStatus
            FROM
            packing_longan d
            WHERE d.DocDate = '$Date_Pack'
            AND  d.IsStatus = 1 
            AND IsRef = 0 
            ORDER BY d.DocNo DESC ";
  }
  else
  {
    $Sql = "SELECT
            d.DocNo,
            d.DocDate,
            TIME(d.Modify_Date) AS  Modify_Date, 
            d.IsStatus
          FROM
          packing_rice d
          WHERE d.DocDate = '$Date_Pro'
          AND  d.IsStatus = 1 
          AND IsRef = 0 
          ORDER BY d.DocNo DESC ";
  }


 $meQuery = mysqli_query($conn, $Sql);
 while ($Result = mysqli_fetch_assoc($meQuery)) 
 {
   $return[$count]['DocNo']         = $Result['DocNo'];
   $return[$count]['IsStatus']      = $Result['IsStatus'];
   $return[$count]['Modify_Date']   = $Result['Modify_Date'];

   $count ++ ;
   $boolean = true;
 }

 $return['Row'] = $count;

 if ($boolean) 
 {
    
   $return['status'] = "success";
   $return['form'] = "showpack";
   echo json_encode($return);
   mysqli_close($conn);
   die;
 }
 else
 {
   $return['status'] = "success";
   $return['form'] = "showpack";
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
      else if($DATA['STATUS'] == 'showpro')
      {
        showpro($conn, $DATA);
      }

      else if($DATA['STATUS'] == 'showpack')
      {
        showpack($conn, $DATA);
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