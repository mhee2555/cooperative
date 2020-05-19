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

  $Sql = "SELECT CONCAT('SLG',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM sale_longan
  WHERE DocNo Like CONCAT('SLG',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sqlx = "INSERT INTO sale_longan (
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

        mysqli_query($conn, $Sqlx);

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
      $return['sql'] = $Sqlx;
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
      $return['sql'] = $Sqlx;
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
}

function ShowItem($conn, $DATA)
{
  $count = 0;
  $boolean = false;
  $datestock   = $DATA["datestock"]==''?date('Y-m-d'):$DATA["datestock"];
  $chk   = $DATA["chk"];

  if($chk ==1 )
  {
    $Sql = "SELECT
    item.item_name ,
    item.item_code ,
    sup.stock_code,
    sup.item_qty,
    sup.item_ccqty,
    TIME(sup.Date_exp) as date_exp,
    sup.DocNo,
    sup.PackgeCode,
    packge_unit.Priceperunit
    FROM
    stock_package sup
    INNER JOIN item ON item.item_code = sup.item_code 
    INNER JOIN packge_unit ON packge_unit.PackgeCode = sup.PackgeCode 
    WHERE 
     sup.item_ccqty <> 0 
    AND TIMEDIFF(sup.Date_exp , NOW() ) > 0
    AND item.item_type = '4' ";
  }
  else
  {
    $Sql = "SELECT
    item.item_name ,
    item.item_code ,
    sup.stock_code,
    sup.item_qty,
    sup.item_ccqty,
    TIME(sup.Date_exp) as date_exp,
    sup.DocNo,
    sup.PackgeCode,
    packge_unit.Priceperunit
    FROM
    stock_package sup
    INNER JOIN item ON item.item_code = sup.item_code 
    INNER JOIN packge_unit ON packge_unit.PackgeCode = sup.PackgeCode 
    WHERE 
     sup.item_ccqty <> 0 
    AND TIMEDIFF(sup.Date_exp , NOW() ) > 0
    AND item.item_type = '4'
    AND DATE(sup.Date_start) = '$datestock'";

  }


    $meQuery = mysqli_query($conn, $Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo'] = $Result['DocNo'];
      $return[$count]['item_name'] = $Result['item_name'];
      $return[$count]['item_code'] = $Result['item_code'];
      $return[$count]['stock_code'] = $Result['stock_code'];
      $return[$count]['item_qty'] = $Result['item_qty'];
      $return[$count]['item_ccqty'] = $Result['item_ccqty'];
      $return[$count]['date_exp'] = $Result['date_exp'];
      $return[$count]['PackgeCode'] = $Result['PackgeCode'];
      $return[$count]['Priceperunit'] = $Result['Priceperunit'];
      $count++;
      $boolean = true;
    }
    $return['Row'] = $count;

    $cntUnit = 0;
    $xSql = "SELECT packge_unit.PackgeCode,packge_unit.PackgeName
      FROM packge_unit  ";
      $xQuery = mysqli_query($conn, $xSql);
      while ($xResult = mysqli_fetch_assoc($xQuery))
      {
        $return['Unit'][$cntUnit]['PackgeCode'] = $xResult['PackgeCode'];
        $return['Unit'][$cntUnit]['PackgeName'] = $xResult['PackgeName'];
        $cntUnit++;
      }

    if ($boolean)
    {
      $return['sql'] = $Sql;
      $return['status'] = "success";
      $return['form'] = "ShowItem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    } 
    else 
    {
      $return['sql'] = $Sql;
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
  $stock_code = $DATA["stock_code"];
  $unit = $DATA["xunit"];
  $price = $DATA["price"];

  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $stock_codex = explode(",", $stock_code);
  $unitx      = explode(",", $unit);
  $pricex      = explode(",", $price);
  #========================================

  foreach ($item_codex as $key => $value)
  {
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
                sale_longan_detail
               WHERE
                  Sale_DocNo = '$DocNo'
               AND item_code = '$value'
               AND PackgeCode = '$unitx[$key]'
               AND stock_code = '$stock_codex[$key]' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  sale_longan_detail
                  SET 
                      Sale_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      PackgeCode = '$unitx[$key]',
                      stock_code = '$stock_codex[$key]',
                      total = '$pricex[$key]' ";

                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  sale_longan_detail
                 SET 
                      Sale_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      PackgeCode = '$unitx[$key]' ,
                      total = ( total + '$pricex[$key]' )

                WHERE
                        Sale_DocNo = '$DocNo'
                AND    item_code = '$value'
                AND    PackgeCode = '$unitx[$key]'  ";

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
              dd.RowID,
              dd.item_code,
              item.item_name,
              dd.kilo,
              dd.total,
              dd.PackgeCode,
              stock_package.stock_code
            FROM
              sale_longan_detail dd
            INNER JOIN item ON item.item_code = dd.item_code
            INNER JOIN stock_package ON stock_package.stock_code = dd.stock_code
            WHERE dd.Sale_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['total']           = $Result['total'];
              $return[$count]['PackgeCode']       = $Result['PackgeCode'];
              $return[$count]['stock_code']       = $Result['stock_code'];
              $Total +=  $Result['total'];
              $return['total']           = $Total;

              $count ++ ;
              $boolean = true;
            }
            $return['Row'] = $count;



            $UPDATE = "UPDATE sale_longan SET Total = $Total WHERE DocNo = '$DocNo' ";
            mysqli_query($conn, $UPDATE);








            $cntUnit = 0;
            $xSql = "SELECT packge_unit.PackgeCode,packge_unit.PackgeName
              FROM packge_unit  ";
              $xQuery = mysqli_query($conn, $xSql);
              while ($xResult = mysqli_fetch_assoc($xQuery))
              {
                $return['Unit'][$cntUnit]['PackgeCode'] = $xResult['PackgeCode'];
                $return['Unit'][$cntUnit]['PackgeName'] = $xResult['PackgeName'];
                $cntUnit++;
              }



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
                  users
                WHERE  type = '2'  ";

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
                  sale_longan bp
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
  $KiloArray  = $DATA["Kilo"];
  $ItemCodeArray  = $DATA["ItemCode"];
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;
  $stock_codeA  = $DATA["stock_code"];
  
  // ========================================
  $ItemCode = explode(",", $ItemCodeArray);
  $Kilo = explode(",", $KiloArray);
  $stock_code = explode(",", $stock_codeA);
  // ========================================


  // INSERT STOCK
  foreach ($ItemCode as $key => $value)
  {
    $INSERT_STOCK = "UPDATE 
                        stock_package
                    SET  
                        item_ccqty = (item_ccqty  - '$Kilo[$key]' )
                   WHERE stock_code = '$stock_code[$key]' ";  

    mysqli_query($conn, $INSERT_STOCK);
  }

  //UPDATE STATUS 
  $Sql = "UPDATE sale_longan SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE sale_longan.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);


  // Show SEARCH
  ShowSearch($conn, $DATA);

}

function Cancelbill($conn, $DATA)
{
    $DocNo  = $DATA["DocNo"];
    $boolean = false;
    $count = 0;

    $Sql_stock = "SELECT
                    sale_longan_detail.kilo,
                    sale_longan_detail.stock_code,
                    sale_longan.IsStatus
                  FROM
                    sale_longan
                    INNER JOIN sale_longan_detail ON sale_longan_detail.Sale_DocNo = sale_longan.DocNo
                  WHERE sale_longan_detail.Sale_DocNo = '$DocNo'";
  $meQuery = mysqli_query($conn, $Sql_stock);
  while ($Result = mysqli_fetch_assoc($meQuery)) 
  {
    $kilo        = $Result['kilo'];
    $stock_code  = $Result['stock_code'];
    $IsStatus  = $Result['IsStatus'];

    if($IsStatus > 0)
    {
      $update_stock = "UPDATE stock_package SET item_ccqty = (item_ccqty + '$kilo' ) WHERE  stock_code = '$stock_code' ";
      mysqli_query($conn, $update_stock);
    }

  }






  $Sql = "UPDATE sale_longan SET IsStatus = 9 WHERE sale_longan.DocNo = '$DocNo'";
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
                  sale_longan bp
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


  $Sql_stock = "SELECT
                  sale_longan_detail.kilo,
                  sale_longan_detail.stock_code,
                  sale_longan.IsStatus
                FROM
                  sale_longan
                  INNER JOIN sale_longan_detail ON sale_longan_detail.Sale_DocNo = sale_longan.DocNo
                WHERE sale_longan_detail.Sale_DocNo = '$DocNo'
                AND sale_longan_detail.item_code = '$itemcode'   ";
                $meQuery = mysqli_query($conn, $Sql_stock);
                while ($Result = mysqli_fetch_assoc($meQuery)) 
                {
                  $kilo        = $Result['kilo'];
                  $stock_code  = $Result['stock_code'];
                  $IsStatus  = $Result['IsStatus'];

                  if($IsStatus > 0)
                  {
                    $update_stock = "UPDATE stock_package SET item_ccqty = (item_ccqty + '$kilo' ) WHERE  stock_code = '$stock_code' ";
                    mysqli_query($conn, $update_stock);
                  }

                }






  $Delete = "DELETE FROM sale_longan_detail WHERE item_code = '$itemcode' AND Sale_DocNo = '$DocNo' ";
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