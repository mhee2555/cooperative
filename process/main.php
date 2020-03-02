<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");


function showchartbuy($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $datepicker  = $DATA["datepicker"]==''?date('Y-m-d'):$DATA["datepicker"];

  $Selectchart = "SELECT
                    SUM(kilo) AS kilo,
                    item.item_name,
                    bl.DocDate 
                  FROM
                    buy_longan_detail bld,
                    item,
                    buy_longan bl 
                  WHERE
                    item.item_code = bld.item_code 
                    AND bl.DocNo = bld.Buy_DocNo 
                    AND bl.DocDate = '$datepicker' 
                  GROUP BY
                    bld.item_code ";
  $meQuery = mysqli_query($conn, $Selectchart);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $return[$count]['kilo']         = $Result['kilo']==null?'0':$Result['kilo'];
    $return[$count]['item_name']    = $Result['item_name']==null?'ไม่มีรายการซื้อ':$Result['item_name'];
    $return[$count]['DocDate']      = $Result['DocDate'];

    $count ++ ;
    $boolean = true;
  }
    $return['Row'] = $count;

  if ($boolean) 
  {
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

    $data = $_POST['DATA'];
    $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'showchartbuy') 
      {
        showchartbuy($conn, $DATA);
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