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

  $Sql = "SELECT CONCAT('SRC',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM stockp_rice
  WHERE DocNo Like CONCAT('SRC',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sqlx = "INSERT INTO stockp_rice (
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
function Savebill($conn, $DATA)
{
  $KiloArray  = $DATA["Kilo"];
  $ItemCodeArray  = $DATA["ItemCode"];
  $DocNo  = $DATA["DocNo"];
  $UnitArray  = $DATA["UnitCode"];
  $boolean = false;
  $count = 0;

  // ========================================
  $ItemCode = explode(",", $ItemCodeArray);
  $Kilo = explode(",", $KiloArray);
  $UnitCode = explode(",", $UnitArray);
  // ========================================


  // INSERT STOCK
  foreach ($ItemCode as $key => $value)
  {
    $INSERT_STOCK = "INSERT INTO 
                        stock_process
                    SET  
                        item_code = '$value',
                        item_qty = '$Kilo[$key]',
                        item_ccqty = '$Kilo[$key]',
                        UnitCode = '$UnitCode[$key]',
                        Date_start = NOW(),
                        Date_exp = NOW() + INTERVAL 90 DAY,
                        DocNo = '$DocNo' ";  

    mysqli_query($conn, $INSERT_STOCK);
  }

  //UPDATE STATUS 
  $Sql = "UPDATE stockp_rice SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE stockp_rice.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);


  // Show SEARCH
  ShowSearch($conn, $DATA);

}
function ShowDetail($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Detail = "SELECT
  	          sld.RowID,
              sld.item_code,
              item.item_name,
              sld.kilo,
              sld.UnitCode
            FROM
            stockp_rice_detail sld
            INNER JOIN item ON item.item_code = sld.item_code
            WHERE stockp_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['UnitCode']           = $Result['UnitCode'];
              $count ++ ;
              $boolean = true;
            }
            $return['Row'] = $count;

            
          $cntUnit = 0;
          $xSql = "SELECT item_unit.UnitCode,item_unit.UnitName
            FROM item_unit  ";
            $xQuery = mysqli_query($conn, $xSql);
            while ($xResult = mysqli_fetch_assoc($xQuery))
            {
              $return['Unit'][$cntUnit]['UnitCode'] = $xResult['UnitCode'];
              $return['Unit'][$cntUnit]['UnitName'] = $xResult['UnitName'];
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
                  sl.DocNo,
                  sl.DocDate,
                  TIME(sl.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  sl.IsStatus ,
                  sl.RefDocNo
                FROM
                  stockp_rice sl
                INNER JOIN employee emp ON emp.ID = sl.Employee_ID
                WHERE sl.DocDate = '$datepicker' ORDER BY sl.DocNo DESC ";

    $meQuery = mysqli_query($conn, $Showsearch);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['RefDocNo']      = $Result['RefDocNo']==null?'ไม่มีเอกสารขอเบิก':$Result['RefDocNo'];
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
function ShowRefDocNo($conn, $DATA)
{
  $count = 0;
  $boolean = false;
  $dateRefDocNo  = $DATA["dateRefDocNo"]==''?date('Y-m-d'):$DATA["dateRefDocNo"];
  // ===========================================
  $SelectDraw = "SELECT
                  DocNo,
                  DocDate 
                FROM
                  process_rice
                WHERE
                  IsStatus = 3 AND IsRef = 0 AND DocDate = '$dateRefDocNo' ";
      $meQuery = mysqli_query($conn, $SelectDraw);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['DocNo']         = $Result['DocNo'];
        $return[$count]['DocDate']       = $Result['DocDate'];
        $count ++ ;
        $boolean = true;
      }
      $return['Row'] = $count;
  
      // ========================================
    if ($boolean) 
    {
      $return['sql'] = $SelectDraw;
      $return['status'] = "success";
      $return['form'] = "ShowRefDocNo";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
    else
    {
      $return['sql'] = $SelectDraw;
      $return['status'] = "success";
      $return['form'] = "ShowRefDocNo";
      $return['msg'] = "Reffail";
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
            item.item_name
          FROM
            item
          WHERE item_type = '3' ";
    $meQuery = mysqli_query($conn, $Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['item_code'] = $Result['item_code'];
      $return[$count]['item_name'] = $Result['item_name'];
      $count++;
      $boolean = true;
    }
    $return['Row'] = $count;

    $cntUnit = 0;
    $xSql = "SELECT item_unit.UnitCode,item_unit.UnitName
      FROM item_unit  ";
      $xQuery = mysqli_query($conn, $xSql);
      while ($xResult = mysqli_fetch_assoc($xQuery))
      {
        $return['Unit'][$cntUnit]['UnitCode'] = $xResult['UnitCode'];
        $return['Unit'][$cntUnit]['UnitName'] = $xResult['UnitName'];
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
function SaveRefDocNo($conn, $DATA)
{
  $count = 0;
  $boolean = false;
  $RefDocNo  = $DATA["RefDocNo"];
  $DocNo  = $DATA["DocNo"];
  // ===========================================
  $updateRef = "UPDATE stockp_rice , process_rice SET stockp_rice.RefDocNo = '$RefDocNo' , process_rice.IsRef = 1 WHERE stockp_rice.DocNo = '$DocNo' AND process_rice.DocNo = '$RefDocNo' "; 
  mysqli_query($conn, $updateRef);
  $boolean = true;
  // ==========================================
  

      if ($boolean) 
      {
        $return['RefDocNo'] = $RefDocNo;
        $return['status'] = "success";
        $return['form'] = "SaveRefDocNo";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }
      else
      {
        $return['status'] = "failed";
        $return['form'] = "SaveRefDocNo";
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

  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $unitx      = explode(",", $unit);
  #========================================

  foreach ($item_codex as $key => $value)
  {
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
               stockp_rice_detail
               WHERE
                stockp_DocNo = '$DocNo'
               AND item_code = '$value' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  stockp_rice_detail
                  SET 
                      stockp_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      UnitCode = '$unitx[$key]'  ";

                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  stockp_rice_detail
                 SET 
                      stockp_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      UnitCode = '$unitx[$key]' 
                WHERE
                       stockp_DocNo = '$DocNo'
                AND    item_code = '$value'  ";

                  mysqli_query($conn, $update);
    }
  }
  ShowDetail($conn, $DATA);
}
function Cancelbill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE stockp_rice SET IsStatus = 9 WHERE stockp_rice.DocNo = '$DocNo'";
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
                  sl.DocNo,
                  sl.DocDate,
                  TIME(sl.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  sl.IsStatus ,
                  sl.RefDocNo 
                FROM
                  stockp_rice sl
                INNER JOIN employee emp ON emp.ID = sl.Employee_ID
                WHERE sl.DocNo = '$DocNo' ";

    $meQuery = mysqli_query($conn, $ShowDocNo);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['RefDocNo']      = $Result['RefDocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
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

  $Delete = "DELETE FROM stockp_rice_detail WHERE item_code = '$itemcode' AND stockp_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete);

  ShowDetail($conn, $DATA);

}

    $data = $_POST['DATA'];
    $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'CreateDocument') 
      {
        CreateDocument($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowDetail') 
      {
        ShowDetail($conn, $DATA);
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
      else if ($DATA['STATUS'] == 'ShowRefDocNo') 
      {
        ShowRefDocNo($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'SaveRefDocNo') 
      {
        SaveRefDocNo($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'ShowItem') 
      {
        ShowItem($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Importdata') 
      {
        Importdata($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Savebill') 
      {
        Savebill($conn, $DATA);
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