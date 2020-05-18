<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");


function ShowDoc($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $date  = $DATA["date"]==''?date('Y-m-d'):$DATA["date"];
  $type  = $DATA["type"];

  if($type == 1 )
  {
    $select_doc = "SELECT
                    sale_longan.DocNo, 
                    sale_longan.DocDate, 
                    sale_longan.IsStatus, 
                    users.FName
                  FROM
                    sale_longan
                  INNER JOIN users ON users.ID = sale_longan.Customer_ID
                  WHERE sale_longan.DocDate = '$date' AND  sale_longan.IsStatus > 0 ";

       $meQuery = mysqli_query($conn, $select_doc);
       while ($Result = mysqli_fetch_assoc($meQuery))
       {
           $return[$count]['DocNo'] = $Result['DocNo'];
           $return[$count]['DocDate'] = $Result['DocDate'];
           $return[$count]['IsStatus'] = $Result['IsStatus'];
           $return[$count]['FName'] = $Result['FName'];
           $count++;
           $boolean = true;
       }
  }
  else if($type == 2)
  {
    $select_doc = "SELECT
                    sale_rice.DocNo, 
                    sale_rice.DocDate, 
                    sale_rice.IsStatus, 
                    users.FName
                  FROM
                    sale_rice
                  INNER JOIN users ON users.ID = sale_rice.Customer_ID
                  WHERE sale_rice.DocDate = '$date' AND  sale_rice.IsStatus > 0 ";

                $meQuery = mysqli_query($conn, $select_doc);
                while ($Result = mysqli_fetch_assoc($meQuery))
                {
                $return[$count]['DocNo'] = $Result['DocNo'];
                $return[$count]['DocDate'] = $Result['DocDate'];
                $return[$count]['IsStatus'] = $Result['IsStatus'];
                $return[$count]['FName'] = $Result['FName'];
                $count++;
                $boolean = true;
                }
  }

  $return['count']  = $count;

  if ($boolean) 
  {
    $return['status'] = "success";
    $return['form'] = "ShowDoc";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['msg'] = "searchfailed";
    $return['date'] = $date;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}

function show_process($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $DocNo  = $DATA["DocNo"];
  $cnt = 0;

  $count = "SELECT COUNT(DocNo) AS cnt FROM sale_longan WHERE DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $count);
  $Result = mysqli_fetch_assoc($meQuery);
  $cnt = $Result['cnt'];

  if($cnt > 0 )
  {
    $sql = "SELECT
              sale_longan.DocNo,
              sale_longan.DvStartTime,
              sale_longan.DvEndTime,
              TIMEDIFF( sale_longan.DvEndTime, sale_longan.DvStartTime ) AS DvUseTime,
              sale_longan.IsStatus,
              sale_longan.signStart,
              sale_longan.signEnd
            FROM
              sale_longan
            WHERE
              sale_longan.DocNo = '$DocNo' ";
  }
  else
  {
    $sql = "SELECT
              sale_rice.DocNo,
              sale_rice.DvStartTime,
              sale_rice.DvEndTime,
              TIMEDIFF( sale_rice.DvEndTime, sale_rice.DvStartTime ) AS DvUseTime,
              sale_rice.IsStatus,
              sale_rice.signStart,
              sale_rice.signEnd
            FROM
            sale_rice
            WHERE
            sale_rice.DocNo = '$DocNo' ";
  }
  
    $meQuery = mysqli_query($conn, $sql);
    while ($Result = mysqli_fetch_assoc($meQuery))
    {
        $return['DocNo'] = $Result['DocNo'];
        $return['DvStartTime'] = $Result['DvStartTime'];
        $return['DvEndTime'] = $Result['DvEndTime'];
        $return['DvUseTime'] = $Result['DvUseTime'];
        $return['IsStatus'] = $Result['IsStatus'];
        $return['signStart'] = $Result['signStart'];
        $return['signEnd'] = $Result['signEnd'];
        $count++;
        $boolean = true;
    }
    
    $return['count']  = $count;

  if ($boolean) 
  {
    $return['status'] = "success";
    $return['form'] = "show_process";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['msg'] = "searchfailed";
    $return['date'] = $date;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}

function end_send($conn , $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $cnt = 0;

  $count = "SELECT COUNT(DocNo) AS cnt FROM sale_longan WHERE DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $count);
  $Result = mysqli_fetch_assoc($meQuery);
  $cnt = $Result['cnt'];

  if($cnt > 0)
  {
    $Sql = "UPDATE sale_longan SET DvEndTime = NOW() , IsStatus = 3 WHERE DocNo = '$DocNo'";
    mysqli_query($conn, $Sql);
  }
  else
  {
    $Sql = "UPDATE sale_rice SET DvEndTime = NOW() , IsStatus = 3 WHERE DocNo = '$DocNo'";
    mysqli_query($conn, $Sql);
  }


  $return['status'] = "success";
  $return['form'] = "end_send";
  echo json_encode($return);
  mysqli_close($conn);
  die;
}
function view_detail($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $count = 0;
  $cnt = 0;

  $countx = "SELECT COUNT(DocNo) AS cnt FROM sale_longan WHERE DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $countx);
  $Result = mysqli_fetch_assoc($meQuery);
  $cnt = $Result['cnt'];




  if($cnt > 0)
  {
    $Sql = "SELECT
              item.item_name,
              sale_longan_detail.kilo,
              packge_unit.PackgeName,
              stock_package.DocNo
            FROM
              sale_longan_detail
              INNER JOIN item ON item.item_code = sale_longan_detail.item_code
              INNER JOIN packge_unit ON packge_unit.PackgeCode = sale_longan_detail.PackgeCode
              INNER JOIN stock_package ON stock_package.stock_code = sale_longan_detail.stock_code
            WHERE sale_longan_detail.Sale_DocNo = '$DocNo' ";
  }
  else
  {
    $Sql = "SELECT
              item.item_name,
              sale_rice_detail.kilo,
              packge_unit.PackgeName,
              stock_package.DocNo
            FROM
              sale_rice_detail
              INNER JOIN item ON item.item_code = sale_rice_detail.item_code
              INNER JOIN packge_unit ON packge_unit.PackgeCode = sale_rice_detail.PackgeCode
              INNER JOIN stock_package ON stock_package.stock_code = sale_rice_detail.stock_code
            WHERE sale_rice_detail.Sale_DocNo = '$DocNo' ";
  }





  $meQuery = mysqli_query($conn, $Sql);
  while ($Result = mysqli_fetch_assoc($meQuery))
  {
    $return[$count]['item_name'] = $Result['item_name'];
    $return[$count]['kilo'] = $Result['kilo'];
    $return[$count]['PackgeName'] = $Result['PackgeName'];
    $return[$count]['DocNo'] = $Result['DocNo']==null?'ไม่พบรหัสคลังสินค้า':$Result['DocNo'];
    $count++;
  }

  $return['cnt'] = $count;

  if ($count > 0) {
      $return['status'] = "success";
      $return['form'] = "view_detail";
      echo json_encode($return);
      mysqli_close($conn);
      die;
  } else {
      $return['status'] = "success";
      $return['form'] = "view_detail";
      echo json_encode($return);
      mysqli_close($conn);
      die;
  }
}

function view_address($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $count = 0;
  $cnt = 0;

  $countx = "SELECT COUNT(DocNo) AS cnt FROM sale_longan WHERE DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $countx);
  $Result = mysqli_fetch_assoc($meQuery);
  $cnt = $Result['cnt'];


  

  if($cnt > 0)
  {
    $Sql = "SELECT Customer_ID FROM sale_longan WHERE DocNo = '$DocNo'  ";
    $meQuery = mysqli_query($conn, $Sql);
    $Result = mysqli_fetch_assoc($meQuery);
    $Customer_ID = $Result['Customer_ID'];

  }
  else
  {
    $Sql = "SELECT Customer_ID FROM sale_rice WHERE DocNo = '$DocNo'  ";
    $meQuery = mysqli_query($conn, $Sql);
    $Result = mysqli_fetch_assoc($meQuery);
    $Customer_ID = $Result['Customer_ID'];

  }




  $address = "SELECT address FROM users WHERE ID = '$Customer_ID' ";
  $meQuery = mysqli_query($conn, $address);
  while ($Result = mysqli_fetch_assoc($meQuery))
  {
    $return['address'] = $Result['address'];
    $count ++ ;
  }

  $return['cnt'] = $count;

  if ($count > 0) {
      $return['status'] = "success";
      $return['form'] = "view_address";
      echo json_encode($return);
      mysqli_close($conn);
      die;
  } else {
      $return['status'] = "success";
      $return['form'] = "view_address";
      echo json_encode($return);
      mysqli_close($conn);
      die;
  }
}







    $data = $_POST['DATA'];
    $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'ShowDoc') 
      {
        ShowDoc($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'show_process') 
      {
        show_process($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'end_send') 
      {
        end_send($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'view_detail') 
      {
        view_detail($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'view_address') 
      {
        view_address($conn, $DATA);
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