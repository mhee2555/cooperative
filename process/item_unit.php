<?php
session_start();
require '../connect/connect.php';

function Showitem($conn, $DATA)
  {
    $count = 0;

        $Showitem = "SELECT
                      UnitCode,
                      UnitName,	
                      Qtyperunit
                    FROM
                      item_unit ";

 
      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['UnitCode']          = $Result['UnitCode'];
        $return[$count]['UnitName']          = $Result['UnitName'];
        $return[$count]['Qtyperunit']        = $Result['Qtyperunit'];
        $count++;
      }
      $return['count']  = $count;
    if($count>0){
      $return['status'] = "success";
      $return['form'] = "Showitem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }else{
      $return['status'] = "notfound";
      $return['msg'] = "notfound";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }

  }
function show_detail_item($conn, $DATA)
  {
    $UnitCode = $DATA["UnitCode"];
    $Showunit = " SELECT
                        item_unit.UnitCode,
                        item_unit.UnitName,	
                        item_unit.Qtyperunit 
                      FROM
                       item_unit
                      WHERE item_unit.UnitCode =  $UnitCode ";
    $meQuery = mysqli_query($conn, $Showunit);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['UnitCode']    = $Result['UnitCode'];
      $return['UnitName'] = $Result['UnitName'];
      $return['Qtyperunit'] = $Result['Qtyperunit'];
      $count=1;
      
    if($count>0){
      $return['status'] = "success";
      $return['sel'] = $sel;
      $return['form'] = "show_detail_item";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }else{
      $return['status'] = "notfound";
      $return['msg'] = "notfound";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
  }
function edit_item($conn, $DATA)
  {
    $UnitCode = $DATA["UnitCode"];
    $Qtyperunit = $DATA["Qtyperunit"];



    $editunit = " UPDATE item_unit SET item_unit.Qtyperunit ='$Qtyperunit'
                      WHERE item_unit.UnitCode='$UnitCode' ";
    mysqli_query($conn, $editunit);


    $return['status'] = "success";
    $return['form'] = "edit_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
function delete_item($conn, $DATA)
  {
    $UnitCode = $DATA["UnitCode"];

    $delete_unit = "DELETE FROM item_unit WHERE UnitCode = $UnitCode  ";
    mysqli_query($conn, $delete_unit);

    $return['status'] = "success";
    $return['form'] = "delete_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
function add_item($conn, $DATA)
  {
    $UnitName_add         = $DATA["UnitName_add"];
    $Qtyperunit_add           = $DATA["Qtyperunit_add"];

    // ===================
    $Sql_ID = "SELECT
                          (UnitCode) + 1 AS ID
                        FROM
                          item_unit
                        ORDER BY
                          UnitCode DESC
                        LIMIT 1";
    $meQuery = mysqli_query($conn, $Sql_ID);
    $Result = mysqli_fetch_assoc($meQuery); 
    $ID =  $Result['ID'];
    // ===================

    if($ID==null){
      $ID=1;
    }else{
      $ID =  $Result['ID']; 
    }
    
    $addunit = "INSERT INTO item_unit (
                                    UnitCode,
                                    UnitName,
                                    Qtyperunit
                                  )
                                  VALUES
                                    (
                                      $ID,
                                      '$UnitName_add',
                                      $Qtyperunit_add
                                    ) ";

    mysqli_query($conn, $addunit);



    $return['status'] = "success";
    $return['form'] = "add_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }






  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'Showitem'){
        Showitem($conn, $DATA);
      }elseif ($DATA['STATUS'] == 'show_detail_item') {
        show_detail_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'edit_item') {
        edit_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'delete_item') {
        delete_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'add_item') {
        add_item($conn, $DATA);  
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