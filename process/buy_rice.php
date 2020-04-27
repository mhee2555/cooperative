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

  $Sql = "SELECT CONCAT('RC',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM buy_rice
  WHERE DocNo Like CONCAT('RC',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sql = "INSERT INTO buy_rice (
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
              grade_price_rice.Grade
            FROM
              item
            INNER JOIN grade_price_rice ON grade_price_rice.item_code = item.item_code
            WHERE item.item_code='6'  ";
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

function Importdata($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $item_code = $DATA["item_code"];
  $kilo = $DATA["kilo"];
  $moisture = $DATA["moisture"];
  $total = $DATA["total"];
  $totalSum = $DATA["totalSum"];
  $unit = $DATA["xunit"];
  #========================================
  $item_codex  = explode(",", $item_code);
  $kilox       = explode(",", $kilo);
  $moisture    = explode(",", $moisture);
  $totalx      = explode(",", $total);
  $totalSumx      = explode(",", $totalSum);
  $unitx      = explode(",", $unit);
  #========================================

  foreach ($item_codex as $key => $value)
  {
    // checkinsert
    $count = " SELECT
                COUNT(*) AS Cnt
               FROM
                buy_rice_detail
               WHERE
                Buy_DocNo = '$DocNo'
               AND item_code = '$value' ";
              $meQuery = mysqli_query($conn, $count);
              $Result = mysqli_fetch_assoc($meQuery);
              $chkUpdate = $Result['Cnt'];

    if ($chkUpdate == 0) 
    {
      $insert = "INSERT INTO  buy_rice_detail
                  SET 
                      Buy_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = '$kilox[$key]',
                      moisture = '$moisture[$key]',
                      Sumtotal = '$totalSumx[$key]',
                      total = '$totalx[$key]',
                      UnitCode = '$unitx[$key]'  ";
                  mysqli_query($conn, $insert);
    }
    else
    {
      $update = "UPDATE  buy_rice_detail
                 SET 
                      Buy_DocNo = '$DocNo',
                      item_code = '$value',
                      kilo = ( kilo + '$kilox[$key]' ),
                      moisture = ( moisture + '$moisture[$key]' ),
                      Sumtotal = ( Sumtotal + '$totalSumx[$key]' ),
                      total = (total + '$totalx[$key]' ) ,
                      UnitCode = '$unitx[$key]'
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
                    bpd.Sumtotal,
                    bpd.moisture,
                    bpd.UnitCode,
                    grade_price_rice.Grade
                  FROM
                    buy_rice_detail bpd
                  INNER JOIN item ON item.item_code = bpd.item_code
                  INNER JOIN grade_price_rice ON grade_price_rice.item_code = item.item_code
                  WHERE Buy_DocNo = '$DocNo' ";
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['UnitCode']      = $Result['UnitCode'];
              $return[$count]['moisture']           = $Result['moisture'];
              $return[$count]['total']          = $Result['total'];
              $return[$count]['Sumtotal']          = $Result['Sumtotal'];
              $return[$count]['Grade']          = $Result['Grade'];
              $Total += $Result['total'];
              $count ++ ;
              $boolean = true;
            }
            $return['Total'] = $Total;
            $return['Row'] = $count;

                      
            #UPDATE TOTAL
            $updatetotal = "UPDATE buy_rice
            SET 
                Total = $Total 
            WHERE DocNo ='$DocNo' ";
          mysqli_query($conn, $updatetotal);
          // 
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
                  buy_rice bp
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

  // ========================================
  $ItemCode = explode(",", $ItemCodeArray);
  $Kilo = explode(",", $KiloArray);
  // ========================================


  // INSERT STOCK
  foreach ($ItemCode as $key => $value)
  {
    $INSERT_STOCK = "INSERT INTO 
                        stock_unprocess
                    SET  
                        item_code = '$value',
                        item_qty = '$Kilo[$key]',
                        item_ccqty = '$Kilo[$key]',
                        Date_start = NOW(),
                        Date_exp = NOW() + INTERVAL 180 DAY,
                        DocNo = '$DocNo' ";  

    mysqli_query($conn, $INSERT_STOCK);
  }

  //UPDATE STATUS 
  $Sql = "UPDATE buy_rice SET IsStatus = 1 , Modify_Date = TIME(NOW())  WHERE buy_rice.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);


  // Show SEARCH
  ShowSearch($conn, $DATA);


}

function Cancelbill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE buy_rice SET IsStatus = 9 WHERE buy_rice.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);

  ShowSearch($conn, $DATA);

}
function Import_weight($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $DocNo_mcar  = $DATA["DocNo_mcar"];
  $weight_mcar  = $DATA["weight_mcar"];
  $weight_mall  = $DATA["weight_mall"];


  $Sql = "UPDATE buy_rice SET DocNo_car = '$DocNo_mcar',weight_car = '$weight_mcar',weight_all = '$weight_mall' WHERE buy_rice.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);



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
                  bp.weight_all,
                  bp.weight_car,
                  bp.DocNo_car,
                  TIME(bp.Modify_Date) AS  Modify_Date, 
                  users.ID AS customer,
                  emp.FName AS employee ,
                  bp.IsStatus
                FROM
                  buy_rice bp
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
      $return[$count]['weight_all']      = $Result['weight_all'];
      $return[$count]['weight_car']      = $Result['weight_car'];
      $return[$count]['DocNo_car']      = $Result['DocNo_car'];

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

  $Delete = "DELETE FROM buy_rice_detail WHERE item_code = '$itemcode' AND Buy_DocNo = '$DocNo' ";
  mysqli_query($conn, $Delete);

  ShowDetail($conn, $DATA);

}
function Sumitem($conn, $DATA)
{
  $SUM  = $DATA["SUM"];
  $moisture  = $DATA["moisture"];

    if($moisture>=14.1 && $moisture<=14.5){
        $id_moisture=1;
    }else if($moisture>=14.6 && $moisture<=15.0){
        $id_moisture=2;
    }else if($moisture>=15.1 && $moisture<=15.5){
        $id_moisture=3;
    }else if($moisture>=15.6 && $moisture<=16.0){
        $id_moisture=4;
    }else if($moisture>=16.1 && $moisture<=16.5){
        $id_moisture=5;
    }else if($moisture>=16.6 && $moisture<=17.0){
        $id_moisture=6;
    }else if($moisture>=17.1 && $moisture<=17.5){
        $id_moisture=7;
    }else if($moisture>=17.6 && $moisture<=18.0){
        $id_moisture=8;
    }else{
      $id_moisture=0;
    }


    $Selectmoisture = "SELECT
                            deduct_price
                          FROM
                            moisture
                          WHERE
                          ID_moisture=$id_moisture ";

    $meQuery = mysqli_query($conn, $Selectmoisture);
    $Result = mysqli_fetch_assoc($meQuery);
    
    $deduct_price  = $Result['deduct_price'];
    
   
    $SUM_total=0;
    if($deduct_price!=null){
      $SUM_total= $SUM-(($SUM*$deduct_price)/100);
    }else{
      $SUM_total=$SUM;
    }
    $boolean = true;


  if ($boolean) 
  {
    $return['id_moisture']  = $id_moisture;
    $return['rowid']  = $DATA["rowid"];
    $return['SUM_total'] = $SUM_total;
    $return['total_p'] = $SUM;
    $return['status'] = "success";
    $return['form'] = "Sumitem";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['form'] = "Sumitem";
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
      else if ($DATA['STATUS'] == 'Sumitem') 
      {
        Sumitem($conn, $DATA);
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
      else if ($DATA['STATUS'] == 'Import_weight') 
      {
        Import_weight($conn, $DATA);
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