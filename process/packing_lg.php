<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");


function CreateDocument($conn, $DATA)
{
  $boolean = false;
  $count = 0;
  $Employee_ID   = $DATA["userid"];
  
  // ============CREATEDOCUMENT====================

  $Sql = "SELECT CONCAT('PKL',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM packing_longan
  WHERE DocNo Like CONCAT('PKL',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sqlx = "INSERT INTO packing_longan (
                    DocNo,
                    DocDate,
                    Modify_Date,
                    Employee_ID
                  )
                  VALUES
                    (
                      '$DocNo',
                      DATE(NOW()),
                      TIME(NOW()),
                      $Employee_ID
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

  $Sql = "SELECT
            item.item_code,
            item.item_name,
            item_qty,
            stock_process.stock_code
          FROM
            item
          INNER JOIN stock_process ON stock_process.item_code = item.item_code
          WHERE item_type = '4'  ";
    $meQuery = mysqli_query($conn, $Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['item_code'] = $Result['item_code'];
      $return[$count]['item_name'] = $Result['item_name'];
      $return[$count]['Grade'] = $Result['item_qty'];
      $return[$count]['stock_code'] = $Result['stock_code'];
      $count++;
      $boolean = true;
    }
    $return['Row'] = $count;




    $cntUnit = 0;
    $xSql = "SELECT packge_unit.PackgeCode,packge_unit.PackgeName , packge_unit.Qtyperunit
      FROM packge_unit  ";
      $xQuery = mysqli_query($conn, $xSql);
      while ($xResult = mysqli_fetch_assoc($xQuery))
      {
        $return['Unit'][$cntUnit]['PackgeCode'] = $xResult['PackgeCode'];
        $return['Unit'][$cntUnit]['PackgeName'] = $xResult['PackgeName'];
        $return[$cntUnit]['Qtyperunit'] = $xResult['Qtyperunit'];
        $cntUnit++;
      }

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
  $unit = $DATA["xunit"];
  $stock_code = $DATA["stock_code"];

  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $unitx      = explode(",", $unit);
  $stock_codex  = explode(",", $stock_code);
  #========================================

  foreach ($item_codex as $key => $value)
  {
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
               packing_longan_detail
               WHERE
               Pk_DocNo = '$DocNo'
               AND item_code = '$value' 
               AND stock_code = '$stock_codex[$key]' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  packing_longan_detail
                  SET 
                      Pk_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      UnitCode = '$unitx[$key]',
                      stock_code = '$stock_codex[$key]' ";

                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  packing_longan_detail
                 SET 
                      Pk_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      UnitCode = '$unitx[$key]',
                      stock_code = '$stock_codex[$key]'
                WHERE
                       Pk_DocNo = '$DocNo'
                AND    item_code = '$value' AND stock_code = '$stock_codex[$key]' ";

                  mysqli_query($conn, $update);
    }
  }
  ShowDetail($conn, $DATA);
}

function ShowDetail($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Detail = "SELECT
              pkl.RowID,
              pkl.item_code,
              item.item_name,
              pkl.kilo,
              pkl.UnitCode
            FROM
            packing_longan_detail pkl
            INNER JOIN item ON item.item_code = pkl.item_code
            WHERE Pk_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['UnitCode']      = $Result['UnitCode'];
              $return[$count]['kilo']           = $Result['kilo'];
              $count ++ ;
              $boolean = true;
            }
            $return['Row'] = $count;

                    
            $cntUnit = 0;
            $xSql = "SELECT packge_unit.PackgeCode,packge_unit.PackgeName , packge_unit.Qtyperunit
              FROM packge_unit  ";
              $xQuery = mysqli_query($conn, $xSql);
              while ($xResult = mysqli_fetch_assoc($xQuery))
              {
                $return['Unit'][$cntUnit]['PackgeCode'] = $xResult['PackgeCode'];
                $return['Unit'][$cntUnit]['PackgeName'] = $xResult['PackgeName'];
                $return[$cntUnit]['Qtyperunit'] = $xResult['Qtyperunit'];
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

function ShowSearch($conn, $DATA)
{
  $datepicker  = $DATA["datepicker"]==''?date('Y-m-d'):$DATA["datepicker"];
  $boolean = false;
  $count = 0;

  $Showsearch = "SELECT
                  pkl.DocNo,
                  pkl.DocDate,
                  TIME( pkl.Modify_Date ) AS Modify_Date,
                  emp.FName AS employee,
                  pkl.IsStatus 
                FROM
                  packing_longan pkl
                  INNER JOIN employee emp ON emp.ID = pkl.Employee_ID 
                WHERE
                  pkl.DocDate = '$datepicker' 
                ORDER BY
                  pkl.DocNo DESC";

    $meQuery = mysqli_query($conn, $Showsearch);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
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

  // ========================================
  $ItemCode = explode(",", $ItemCodeArray);
  $Kilo = explode(",", $KiloArray);
  // ========================================



  //UPDATE STATUS 
  $Sql = "UPDATE packing_longan SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE packing_longan.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);


  // SHOW SEARCH
  ShowSearch($conn, $DATA);

}

function Cancelbill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE packing_longan SET IsStatus = 9 WHERE packing_longan.DocNo = '$DocNo'";
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
                  pkl.DocNo,
                  pkl.DocDate,
                  TIME( pkl.Modify_Date ) AS Modify_Date,
                  emp.FName AS employee,
                  pkl.IsStatus 
                FROM
                packing_longan pkl
                  INNER JOIN employee emp ON emp.ID = pkl.Employee_ID 
                WHERE
                 pkl.DocNo = '$DocNo'  ";

    $meQuery = mysqli_query($conn, $ShowDocNo);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
      $return[$count]['employee']      = $Result['employee'];
      $return[$count]['IsStatus']      = $Result['IsStatus'];

      $count ++ ;
      $boolean = true;
  
    }
    $return['Row'] = $count;


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

  $Delete = "DELETE FROM packing_longan_detail WHERE item_code = '$itemcode' AND Pk_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete);
  ShowDetail($conn, $DATA);
}

function check_unit($conn, $DATA)
{
  $i  = $DATA["i"];
  $Packge_unit  = $DATA["unit"];

  $cntUnit = 0;
  $xSql = "SELECT packge_unit.Qtyperunit
    FROM packge_unit  WHERE PackgeCode = '$Packge_unit' ";
    $xQuery = mysqli_query($conn, $xSql);
    while ($xResult = mysqli_fetch_assoc($xQuery))
    {
      $return['Qtyperunit'] = $xResult['Qtyperunit'];
      $cntUnit++;
    }

    if ($cntUnit >=1) 
    {
      $return['i'] = $i;
      $return['status'] = "success";
      $return['form'] = "check_unit";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
    else
    {
      $return['status'] = "failed";
      $return['form'] = "check_unit";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
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
      else if ($DATA['STATUS'] == 'check_unit') 
      {
        check_unit($conn, $DATA);
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