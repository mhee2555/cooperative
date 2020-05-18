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

  $Sql = "SELECT CONCAT('PLG',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'-',
  LPAD( (COALESCE(MAX(CONVERT(SUBSTRING(DocNo,10,5),UNSIGNED INTEGER)),0)+1) ,5,0)) AS DocNo,DATE(NOW()) AS DocDate,
  CURRENT_TIME() AS RecNow
  FROM process_longan
  WHERE DocNo Like CONCAT('PLG',SUBSTRING(YEAR(DATE(NOW())),3,4),LPAD(MONTH(DATE(NOW())),2,0),'%')
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
      $Sqlx = "INSERT INTO process_longan (
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
function ShowDetail($conn, $DATA)
{
  $DocNo = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Detail = "SELECT
  	          pld.RowID,
              pld.item_code,
              item.item_name,
              pld.kilo,
              pld.UnitCode,
              pld.stock_code,
              process_longan.IsRef_Status
            FROM
              process_longan_detail pld 
            INNER JOIN item ON item.item_code = pld.item_code
            INNER JOIN process_longan ON process_longan.DocNo = pld.Lg_DocNo
            WHERE Lg_DocNo = '$DocNo' ";
            
            $meQuery = mysqli_query($conn, $Detail);
            while ($Result = mysqli_fetch_assoc($meQuery)) 
            {
              $return[$count]['RowID']          = $Result['RowID'];
              $return[$count]['item_code']      = $Result['item_code'];
              $return[$count]['item_name']      = $Result['item_name'];
              $return[$count]['kilo']           = $Result['kilo'];
              $return[$count]['UnitCode']       = $Result['UnitCode'];
              $return[$count]['stock_code']     = $Result['stock_code'];
              $IsRef_Status                     = $Result['IsRef_Status'];
              $count ++ ;
              $boolean = true;
            }
            $return['Row'] = $count;



            $cntUnit = 0;
            if($IsRef_Status == 1 )
            {
              $xSql = "SELECT item_unit.UnitCode,item_unit.UnitName
              FROM item_unit  ";
              $xQuery = mysqli_query($conn, $xSql);
              while ($xResult = mysqli_fetch_assoc($xQuery))
              {
                $return['Unit'][$cntUnit]['UnitCode'] = $xResult['UnitCode'];
                $return['Unit'][$cntUnit]['UnitName'] = $xResult['UnitName'];
                $cntUnit++;
              }
            }
            else
            {
              $xSql = "SELECT packge_unit.PackgeCode,packge_unit.PackgeName , packge_unit.Qtyperunit
              FROM packge_unit  ";
              $xQuery = mysqli_query($conn, $xSql);
              while ($xResult = mysqli_fetch_assoc($xQuery))
              {
                $return['Unit'][$cntUnit]['UnitCode'] = $xResult['PackgeCode'];
                $return['Unit'][$cntUnit]['UnitName'] = $xResult['PackgeName'];
                $return[$cntUnit]['Qtyperunit'] = $xResult['Qtyperunit'];
                $cntUnit++;
              }
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
                  pl.DocNo,
                  pl.DocDate,
                  TIME(pl.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  pl.IsStatus ,
                  pl.RefDocNo
                FROM
                  process_longan pl
                INNER JOIN employee emp ON emp.ID = pl.Employee_ID
                WHERE pl.DocDate = '$datepicker' ORDER BY pl.DocNo DESC ";

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
  $chk_ref_status  = $DATA["chk_ref_status"];

  if($chk_ref_status ==1)
  {
    $chk_ref_str = 'เอกสารสั่งแปรรูป' ;
  }
  else if ($chk_ref_status == 2)
  {
    $chk_ref_str = 'เอกสารสั่งบรรจุภัณฑ์' ;
  }
  // ===========================================

  if($chk_ref_status == 1 )
  {
    $SelectDraw = "SELECT
                    DocNo,
                    DocDate 
                  FROM
                    draw 
                  WHERE
                    IsStatus = 2 AND IsRef = 0 AND DocDate = '$dateRefDocNo' ";
  }
  else if($chk_ref_status == 2)
  {
    $SelectDraw = "SELECT
                    DocNo,
                    DocDate 
                  FROM
                    packing_longan 
                  WHERE
                    IsStatus = 1 AND IsRef = 0 AND DocDate = '$dateRefDocNo' ";
  }
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
      $return['chk_ref_str'] = $chk_ref_str;
      $return['dateRefDocNo'] = $dateRefDocNo;
      $return['status'] = "failed";
      $return['form'] = "ShowRefDocNo";
      $return['msg'] = "Reffail";
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
  $chk_ref_status  = $DATA["chk_ref_status"];
  // ===========================================
  if($chk_ref_status ==1)
  {
    $updateRef = "UPDATE process_longan , draw SET process_longan.RefDocNo = '$RefDocNo' , draw.IsRef = 1 , process_longan.IsRef_Status = $chk_ref_status WHERE process_longan.DocNo = '$DocNo' AND draw.DocNo = '$RefDocNo' "; 
  }
  else
  {
    $updateRef = "UPDATE process_longan , packing_longan SET process_longan.RefDocNo = '$RefDocNo' , packing_longan.IsRef = 1 , process_longan.IsRef_Status = $chk_ref_status WHERE process_longan.DocNo = '$DocNo' AND packing_longan.DocNo = '$RefDocNo' "; 
  }
  mysqli_query($conn, $updateRef);
  // ==========================================
  if($chk_ref_status ==1)
  {
    $slectdraw = "SELECT
                    draw_detail.item_code, 
                    draw_detail.kilo,
                    draw_detail.UnitCode
                  FROM
                    draw_detail
                  WHERE draw_DocNo = '$RefDocNo' " ;
  }
  else
  {
    $slectdraw = "SELECT
                    packing_longan_detail.item_code, 
                    packing_longan_detail.kilo,
                    packing_longan_detail.UnitCode,
                    packing_longan_detail.stock_code
                  FROM
                    packing_longan_detail
                  WHERE Pk_DocNo = '$RefDocNo' " ;
  }
      $meQuery = mysqli_query($conn, $slectdraw);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $item_code  = $Result['item_code'];
        $kilo       = $Result['kilo'];
        $UnitCode   = $Result['UnitCode'];
        $stock_code   = $Result['stock_code']==null?0:$Result['stock_code'];

        // insert draw detail to proces_lg
        $insertpro = "INSERT INTO process_longan_detail SET Lg_DocNo = '$DocNo' , item_code = '$item_code' , kilo = '$kilo' , UnitCode = '$UnitCode' , stock_code = '$stock_code'";
        mysqli_query($conn, $insertpro);

        $boolean = true;
      }

      if ($boolean) 
      {
        $return['chk_ref_status'] = $chk_ref_status;
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
function Cancelbill($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE process_longan SET IsStatus = 9 WHERE process_longan.DocNo = '$DocNo'";
  mysqli_query($conn, $Sql);

  ShowSearch($conn, $DATA);

}
function Startprocess($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE process_longan SET start_process = NOW() , IsStatus = 1 WHERE process_longan.DocNo = '$DocNo' ";
  mysqli_query($conn, $Sql);


  // SELECT DATE 
  $selectdate = "SELECT start_process FROM process_longan WHERE process_longan.DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $selectdate);
  $Result = mysqli_fetch_assoc($meQuery);
  $return['start_process'] = $Result['start_process'];

  if (mysqli_query($conn, $Sql)) 
  {
    $return['status'] = "success";
    $return['form'] = "Startprocess";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['form'] = "Startprocess";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }


}
function Endprocess($conn, $DATA)
{
  $DocNo  = $DATA["DocNo"];
  $boolean = false;
  $count = 0;

  $Sql = "UPDATE process_longan SET end_process = NOW() , IsStatus = 2 WHERE process_longan.DocNo = '$DocNo' ";
  mysqli_query($conn, $Sql);


  // SELECT DATE 
  $selectdate = "SELECT end_process FROM process_longan WHERE process_longan.DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $selectdate);
  $Result = mysqli_fetch_assoc($meQuery);
  $return['end_process'] = $Result['end_process'];

  if (mysqli_query($conn, $Sql)) 
  {
    $return['status'] = "success";
    $return['form'] = "Endprocess";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  else
  {
    $return['status'] = "failed";
    $return['form'] = "Endprocess";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }


}
function Successprocess($conn, $DATA)
{
  $KiloArray  = $DATA["Kilo"];
  $ItemCodeArray  = $DATA["ItemCode"];
  $UnitArray  = $DATA["Unit"];
  $DocNo  = $DATA["DocNo"];
  $stock_code  = $DATA["stock_code"];
  $boolean = false;
  $count = 0;

  // ========================================
  $ItemCode = explode(",", $ItemCodeArray);
  $Kilo = explode(",", $KiloArray);
  $Unit = explode(",", $UnitArray);
  $stock_codex = explode(",", $stock_code);
  // ========================================


  //==========================================================================================================
  $Sql_status = "SELECT IsRef_Status FROM process_longan WHERE DocNo = '$DocNo' ";
  $meQuery = mysqli_query($conn, $Sql_status);
  $Result = mysqli_fetch_assoc($meQuery); 
  $Status_ref = $Result['IsRef_Status'];

  // 2 = สั่งบรรจุภัณฑ์
  if($Status_ref == 2)
  {
    // INSERT STOCK
    foreach ($ItemCode as $key => $value)
    {
      // $SELECT_COUNT = "SELECT COUNT(*) AS cnt FROM stock_package WHERE item_code = '$value' AND PackgeCode = '$Unit[$key]' ";
      // $meQuery = mysqli_query($conn, $SELECT_COUNT);
      // $Result = mysqli_fetch_assoc($meQuery); 
      // $cnt = $Result['cnt'];

      // if($cnt >= 1)
      // {
      //   $UPDATE_STOCK = "UPDATE 
      //                       stock_package
      //                   SET  
      //                       item_code = '$value',
      //                       item_qty = ( item_qty + '$Kilo[$key]' ),
      //                       item_ccqty = ( item_ccqty + '$Kilo[$key]' ),
      //                       PackgeCode = '$Unit[$key]',
      //                       Date_start = NOW(),
      //                       Date_exp = NOW() + INTERVAL 1 DAY 
      //                   WHERE item_code = '$value' ";  
      //               mysqli_query($conn, $UPDATE_STOCK);
                    
      //     // ========================================================================================
      //               $selectunit = "SELECT
      //                                 packge_unit.Qtyperunit 
      //                               FROM
      //                                 packge_unit 
      //                               WHERE
      //                                 PackgeCode = '$Unit[$key]' ";
      //               $meQuery = mysqli_query($conn, $selectunit);
      //               $Result = mysqli_fetch_assoc($meQuery);
      //               $Qtyperunit = $Result['Qtyperunit'];
      //               $total_gram = ($Kilo[$key] * $Qtyperunit) / 1000 ;
      
      //               $UPDATE_STOCKx = "UPDATE 
      //                                   stock_process
      //                               SET  
      //                                   item_ccqty = ( item_ccqty - '$total_gram' )
      //                               WHERE stock_code = '$stock_codex[$key]' ";  
      //                           mysqli_query($conn, $UPDATE_STOCKx);

      //      // ========================================================================================
                   
      // }
      // else
      // {
        $INSERT_STOCK = "INSERT INTO 
                          stock_package
                         SET  
                          item_code = '$value',
                          item_qty = '$Kilo[$key]',
                          item_ccqty = '$Kilo[$key]',
                          PackgeCode = '$Unit[$key]',
                          DocNo = '$DocNo',
                          Date_start = NOW(),
                          Date_exp = NOW() + INTERVAL 365 DAY ";  
                  mysqli_query($conn, $INSERT_STOCK);

          // ========================================================================================
          $selectunit = "SELECT
                          packge_unit.Qtyperunit 
                        FROM
                          packge_unit 
                        WHERE
                          PackgeCode = '$Unit[$key]' ";
                $meQuery = mysqli_query($conn, $selectunit);
                $Result = mysqli_fetch_assoc($meQuery);
                $Qtyperunit = $Result['Qtyperunit'];
                $total_gram = ($Kilo[$key] * $Qtyperunit) / 1000 ;

                $UPDATE_STOCKx = "UPDATE 
                            stock_process
                        SET  
                            item_ccqty = ( item_ccqty - '$total_gram' )
                        WHERE stock_code = '$stock_codex[$key]' ";  
                    mysqli_query($conn, $UPDATE_STOCKx);

  // ========================================================================================

      // }
    }
  }
  else
  {
    // สั่งแปรรูป นำstatus ไปอ้่างอิงต่อ 
    $Sql = "UPDATE process_longan SET IsStatus = 3 WHERE process_longan.DocNo = '$DocNo' ";
    mysqli_query($conn, $Sql);
  }



  //==========================================================================================================



  ShowSearch($conn, $DATA);

}
function ShowDocNo($conn, $DATA)
{
  $DocNo  = $DATA["DocNochk"];
  $boolean = false;
  $count = 0;
  $countuser = 0;


  $ShowDocNo = "SELECT
                  pl.DocNo,
                  pl.DocDate,
                  TIME(pl.Modify_Date) AS  Modify_Date, 
                  emp.FName AS employee ,
                  pl.IsStatus ,
                  pl.RefDocNo ,
                  pl.start_process ,
                  pl.end_process ,
                  pl.IsRef_Status
                FROM
                  process_longan pl
                INNER JOIN employee emp ON emp.ID = pl.Employee_ID
                WHERE pl.DocNo = '$DocNo' ";

    $meQuery = mysqli_query($conn, $ShowDocNo);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
    {
      $return[$count]['DocNo']         = $Result['DocNo'];
      $return[$count]['RefDocNo']      = $Result['RefDocNo'];
      $return[$count]['DocDate']       = $Result['DocDate'];
      $return[$count]['Modify_Date']   = $Result['Modify_Date'];
      $return[$count]['employee']      = $Result['employee'];
      $return[$count]['IsStatus']      = $Result['IsStatus'];
      $return[$count]['IsRef_Status']      = $Result['IsRef_Status'];
      $return[$count]['start_process']      = $Result['start_process']==null?"":$Result['start_process'];
      $return[$count]['end_process']      = $Result['end_process']==null?"":$Result['end_process'];


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

  $Delete = "DELETE FROM process_longan_detail WHERE item_code = '$itemcode' AND Lg_DocNo = '$DocNo' ";
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
      else if ($DATA['STATUS'] == 'Startprocess') 
      {
        Startprocess($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Endprocess') 
      {
        Endprocess($conn, $DATA);
      }
      else if ($DATA['STATUS'] == 'Successprocess') 
      {
        Successprocess($conn, $DATA);
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