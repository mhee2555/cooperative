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

  $Sql = "SELECT CONCAT('DW',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM draw_rice
  WHERE DocNo Like CONCAT('DW',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sql = "INSERT INTO draw_rice (
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

        mysqli_query($conn, $Sql);

        $Sql_user = "SELECT
                    users.FName
                    FROM
                    users
                    WHERE users.ID = $Employee_ID ";
        $meQuery = mysqli_query($conn, $Sql_user);
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
      $return['sql'] = $Sql;
      $return['status'] = "success";
      $return['form'] = "CreateDocument";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    } 
    else 
    {
      $return['sql'] = $Sql;
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
    DATE(sup.Date_exp) as date_exp
    FROM
    stock_unprocess sup
    INNER JOIN item ON item.item_code = sup.item_code 
    WHERE item.item_type = 1
    AND sup.item_ccqty <> 0 
    AND TIMEDIFF(sup.Date_exp , NOW() ) > 0
    ORDER BY DATE(sup.Date_exp) ASC";
  }
  else
  {
      $Sql = "SELECT
      item.item_name ,
      item.item_code ,
      sup.stock_code,
      sup.item_qty,
      sup.item_ccqty,
      TIME(sup.Date_exp) as date_exp
      FROM
      stock_unprocess sup
      INNER JOIN item ON item.item_code = sup.item_code 
      WHERE item.item_type = 1
      AND sup.item_ccqty <> 0 
      AND TIMEDIFF(sup.Date_exp , NOW() ) > 0
      AND DATE(sup.Date_start) = '$datestock'
      ORDER BY TIME(sup.Date_exp) ASC";
  }


    $meQuery = mysqli_query($conn, $Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['item_name'] = $Result['item_name'];
      $return[$count]['item_code'] = $Result['item_code'];
      $return[$count]['stock_code'] = $Result['stock_code'];
      $return[$count]['item_qty'] = $Result['item_qty'];
      $return[$count]['item_ccqty'] = $Result['item_ccqty'];
      $return[$count]['date_exp'] = $Result['date_exp'];
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

  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $stock_codex = explode(",", $stock_code);
  $unitx      = explode(",", $unit);
  #========================================

  foreach ($item_codex as $key => $value)
  {

    $select_stock = "SELECT item_ccqty FROM stock_unprocess WHERE stock_code = '$stock_codex[$key]' ";
    $meQuery = mysqli_query($conn, $select_stock);
    $Result = mysqli_fetch_assoc($meQuery);
    $item_ccqty = $Result['item_ccqty'];


    
    // insert_chk
    $insert_chk = "INSERT INTO  draw_rice_detail_sub
    SET 
        draw_DocNo = '$DocNo',
        item_code = '$value',
        kilo = '$kilox[$key]',
        stock_code = '$stock_codex[$key]',
        UnitCode = '$unitx[$key]',
        item_ccqty = '$item_ccqty' ";

    mysqli_query($conn, $insert_chk);
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
               draw_rice_detail
               WHERE
                draw_DocNo = '$DocNo'
               AND item_code = '$value' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

              $update_stock = "UPDATE stock_unprocess SET item_ccqty = (item_ccqty - '$kilox[$key]' ) WHERE  stock_code = '$stock_codex[$key]' ";
              mysqli_query($conn, $update_stock);

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  draw_rice_detail
                  SET 
                      draw_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      UnitCode = '$unitx[$key]' ";

                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  draw_rice_detail
                 SET 
                      draw_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      UnitCode = '$unitx[$key]'
                WHERE
                      draw_DocNo = '$DocNo'
                AND    item_code = '$value'  ";

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
              dd.RowID,
              dd.item_code,
              item.item_name,
              dd.kilo,
              dd.UnitCode
            FROM
            draw_rice_detail dd
            INNER JOIN item ON item.item_code = dd.item_code
            WHERE dd.draw_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['UnitCode']       = $Result['UnitCode'];
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
                  d.DocNo,
                  d.DocDate,
                  TIME(d.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  d.IsStatus
                FROM
                draw_rice d
                INNER JOIN employee emp ON emp.ID = d.Employee_ID
                WHERE d.DocDate = '$datepicker' ORDER BY d.DocNo DESC ";

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
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;


  //UPDATE STATUS 
  $Sql = "UPDATE draw_rice SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE draw_rice.DocNo = '$DocNo'";
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
                  draw_detail_sub.kilo,
                  draw_detail_sub.stock_code
                FROM
                  draw_detail_sub
                WHERE draw_detail_sub.draw_DocNo = '$DocNo'";
                $meQuery = mysqli_query($conn, $Sql_stock);
                while ($Result = mysqli_fetch_assoc($meQuery)) 
                {
                  $kilo        = $Result['kilo'];
                  $stock_code  = $Result['stock_code'];
                  $update_stock = "UPDATE stock_unprocess SET item_ccqty = (item_ccqty + '$kilo' ) WHERE  stock_code = '$stock_code' ";
                  mysqli_query($conn, $update_stock);
                }




  $Sql = "UPDATE draw_rice SET IsStatus = 9 WHERE draw_rice.DocNo = '$DocNo'";
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
                  d.DocNo,
                  d.DocDate,
                  TIME(d.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  d.IsStatus
                FROM
                draw_rice d
                INNER JOIN employee emp ON emp.ID = d.Employee_ID
                WHERE d.DocNo = '$DocNo' ";

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
  $select_stock = "SELECT
                      draw_rice_detail_sub.kilo,
                      draw_rice_detail_sub.stock_code 
                    FROM
                    draw_rice_detail_sub 
                    WHERE
                      item_code = '$itemcode' 
                      AND draw_DocNo = '$DocNo' ";

    $meQuery = mysqli_query($conn, $select_stock);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $kilo         = $Result['kilo'];
      $stock_code   = $Result['stock_code'];
      
      $update_stock = "UPDATE stock_unprocess SET item_ccqty = (item_ccqty + '$kilo' ) WHERE  stock_code = '$stock_code' ";
      mysqli_query($conn, $update_stock);
    }



  $Delete = "DELETE FROM draw_rice_detail WHERE item_code = '$itemcode' AND draw_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete);



  $Delete_sub = "DELETE FROM draw_rice_detail_sub WHERE item_code = '$itemcode' AND draw_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete_sub);



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