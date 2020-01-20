<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");


function CreateDocument($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $Employee_ID   = $DATA["userid"];
  $Customer_ID   = $DATA["Customer"];

  // ============CREATEDOCUMENT====================

  $Sql = "SELECT CONCAT('LY',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM buy_product
  WHERE DocNo Like CONCAT('LY',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
  ORDER BY DocNo DESC LIMIT 1";

  $meQuery = mysqli_query($conn, $Sql);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $DocNo = $Result['DocNo'];
    $return[0]['DocNo']   = $Result['DocNo'];
    $return[0]['DocDate'] =  $Result['DocDate'];
    $return[0]['RecNow']  = $Result['RecNow'];
    $count = 1;
  }

  if ($count == 1) 
  {
      $Sql = "INSERT INTO buy_product (
                    DocNo,
                    DocDate,
                    Modify_Date,
                    Employee_ID,
                    Customer_ID
                  )
                  VALUES
                    (
                      '$DocNo',
                      DATE(NOW()),
                      TIME(NOW()),
                      $Employee_ID,
                      $Customer_ID
                    )";

        mysqli_query($conn, $Sql);

        $Sql = "SELECT
                    users.FName
                    FROM
                    users
                    WHERE users.ID = $Employee_ID ";
        $meQuery = mysqli_query($conn, $Sql);
        while ($Result = mysqli_fetch_assoc($meQuery)) 
        {
          $return[0]['Record']   = $Result['FName'];
        }
        $boolean = true;
  } 
  else 
  {
    $boolean = false;
  }

    if ($boolean) 
    {
      $return['status'] = "success";
      $return['form'] = "CreateDocument";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    } 
    else 
    {
      $return['status'] = "failed";
      $return['form'] = "CreateDocument";
      $return['msg'] = 'cantcreate';
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
}

function ShowItem($conn, $DATA)
{
  $count = 0;
  $boolean = false;

  $Sql = "SELECT
                item.item_code,
                item.item_name,
                grade_price.Grade
              FROM
                item
              INNER JOIN grade_price ON grade_price.item_code = item.item_code  ";
    $meQuery = mysqli_query($conn, $Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['item_code'] = $Result['item_code'];
      $return[$count]['item_name'] = $Result['item_name'];
      $return[$count]['Grade'] = $Result['Grade'];
      $count++;
      $boolean = true;
    }
    $return['Row'] = $count;

    if ($boolean)
    {
      $return['status'] = "success";
      $return['form'] = "ShowItem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    } 
    else 
    {
      $return['status'] = "success";
      $return['form'] = "ShowItem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }

}

function Importdata($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $item_code = $DATA["item_code"];
  $kilo = $DATA["kilo"];
  $total = $DATA["total"];

  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $totalx      = explode(",", $total);
  #========================================

  foreach ($item_codex as $key => $value)
  {
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
                buy_product_detail
               WHERE
                Buy_DocNo = '$DocNo'
               AND item_code = '$value' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  buy_product_detail
                  SET 
                      Buy_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      total = '$totalx[$key]' ";

                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  buy_product_detail
                 SET 
                      Buy_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      total = (total + '$totalx[$key]' ) 
                WHERE
                      Buy_DocNo = '$DocNo'
                AND    item_code = '$value'  ";

                  mysqli_query($conn, $update);
    }

    # ผลรวม ราคา        
    $sumtotal +=$totalx[$key];
  }
  ShowDetail($conn, $DATA);
}

function ShowDetail($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Detail = "SELECT
  	          bpd.RowID,
              bpd.item_code,
              item.item_name,
              bpd.kilo,
              bpd.total,
	            grade_price.Grade
            FROM
              buy_product_detail bpd
            INNER JOIN item ON item.item_code = bpd.item_code
            INNER JOIN grade_price ON grade_price.item_code = item.item_code
            WHERE Buy_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['total']          = $Result['total'];
              $return[$count]['Grade']          = $Result['Grade'];
              $Total += $Result['total'];
              $count ++ ;
              $boolean = true;
            }
            $return['Total'] = $Total;
            $return['Row'] = $count;

                      
            #UPDATE TOTAL
            $updatetotal = "UPDATE buy_product 
            SET 
                Total = $Total 
            WHERE DocNo ='$DocNo' ";
          mysqli_query($conn, $updatetotal);
          // 

  if ($boolean) 
  {
    $return['status'] = "success";
    $return['form'] = "ShowDetail";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['form'] = "ShowDetail";
    $return['msg'] = "Detailfail";
    $return['DocNo'] = $DocNo;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}

function Showuser($conn, $DATA)
{
  $boolean = false;
  $count = 0;

  $Selectuser = "SELECT
                  users.ID,
                  users.FName
                FROM
                  users ";

  $meQuery = mysqli_query($conn, $Selectuser);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $return[$count]['ID']          = $Result['ID'];
    $return[$count]['FName']      = $Result['FName'];

    $count ++ ;
    $boolean = true;

  }
    $return['Row'] = $count;

  if ($boolean) 
  {
    $return['status'] = "success";
    $return['form'] = "Showuser";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['form'] = "Showuser";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}

function ShowSearch($conn, $DATA)
{
  $datepicker  = $DATA["datepicker"]==''?date('Y-m-d'):$DATA["datepicker"];
  $boolean = false;
  $count = 0;

  $Showsearch = "SELECT
                  bp.DocNo,
                  bp.DocDate,
                  TIME(bp.Modify_Date) AS  Modify_Date, 
                  users.FName AS customer,
                  emp.FName AS employee ,
                  bp.IsStatus
                FROM
                  buy_product bp
                INNER JOIN employee emp ON emp.ID = bp.Employee_ID
                INNER JOIN users ON users.ID = bp.Customer_ID
                WHERE bp.DocDate = '$datepicker' ORDER BY bp.DocNo DESC ";

    $meQuery = mysqli_query($conn, $Showsearch);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
      $return[$count]['customer']      = $Result['customer'];
      $return[$count]['employee']      = $Result['employee'];
      $return[$count]['IsStatus']      = $Result['IsStatus'];

      $count ++ ;
      $boolean = true;
  
    }
    $return['Row'] = $count;

  if ($boolean) 
  {
    $return['status'] = "success";
    $return['form'] = "ShowSearch";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['msg'] = "searchfailed";
    $return['status'] = "failed";
    $return['date'] = $datepicker;
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
}

function Savebill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE buy_product SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE buy_product.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);

  ShowSearch($conn, $DATA);

}

function Cancelbill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE buy_product SET IsStatus = 9 WHERE buy_product.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);

  ShowSearch($conn, $DATA);

}

function ShowDocNo($conn, $DATA)
{
  $DocNo  = $DATA["DocNochk"];
  $boolean = false;
  $count = 0;
  $countuser = 0;


  $ShowDocNo = "SELECT
                  bp.DocNo,
                  bp.DocDate,
                  TIME(bp.Modify_Date) AS  Modify_Date, 
                  users.ID AS customer,
                  emp.FName AS employee ,
                  bp.IsStatus
                FROM
                  buy_product bp
                INNER JOIN employee emp ON emp.ID = bp.Employee_ID
                INNER JOIN users ON users.ID = bp.Customer_ID
                WHERE bp.DocNo = '$DocNo' ";

    $meQuery = mysqli_query($conn, $ShowDocNo);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
      $return[$count]['customer']      = $Result['customer'];
      $return[$count]['employee']      = $Result['employee'];
      $return[$count]['IsStatus']      = $Result['IsStatus'];

      $count ++ ;
      $boolean = true;
  
    }
    $return['Row'] = $count;

    // ===========
    $Selectuser = "SELECT
                    users.ID,
                    users.FName
                  FROM
                    users ";

                $meQuery = mysqli_query($conn, $Selectuser);
                while ($Result = mysqli_fetch_assoc($meQuery)) 
                {
                $return[$countuser]['ID']         = $Result['ID'];
                $return[$countuser]['FName']      = $Result['FName'];

                $countuser ++ ;
                $boolean = true;

                }
                $return['Rowuser'] = $countuser;

    // ===========

    if ($boolean) 
    {
      $return['status'] = "success";
      $return['form'] = "ShowDocNo";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
    else
    {
      $return['status'] = "failed";
      $return['form'] = "ShowDocNo";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
}

function Deleteitem($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $itemcode  = $DATA["itemcode"];

  $Delete = "DELETE FROM buy_product_detail WHERE item_code = '$itemcode' AND Buy_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete);

  ShowDetail($conn, $DATA);

}

    $data = $_POST['DATA'];
    $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'CreateDocument') 
      {
        CreateDocument($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowItem') 
      {
        ShowItem($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Importdata') 
      {
        Importdata($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowDetail') 
      {
        ShowDetail($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Showuser') 
      {
        Showuser($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowSearch') 
      {
        ShowSearch($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Savebill') 
      {
        Savebill($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Cancelbill') 
      {
        Cancelbill($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowDocNo') 
      {
        ShowDocNo($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Deleteitem') 
      {
        Deleteitem($conn, $DATA);
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