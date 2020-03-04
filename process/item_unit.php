<?php
session_start();
require '../connect/connect.php';

function Showitem($conn, $DATA)
  {
    $count = 0;

        $Showitem = "SELECT
                      PackgeCode,
                      PackgeName,	
                      Qtyperunit
                    FROM
                      packge_unit ";

 
      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['PackgeCode']          = $Result['PackgeCode'];
        $return[$count]['PackgeName']          = $Result['PackgeName'];
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
    $PackgeCode = $DATA["PackgeCode"];
    $Showunit = " SELECT
                        packge_unit.PackgeCode,
                        packge_unit.PackgeName,	
                        packge_unit.Qtyperunit 
                      FROM
                       packge_unit
                      WHERE packge_unit.PackgeCode =  $PackgeCode ";
    $meQuery = mysqli_query($conn, $Showunit);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['PackgeCode']    = $Result['PackgeCode'];
      $return['PackgeName'] = $Result['PackgeName'];
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
    $PackgeCode = $DATA["PackgeCode"];
    $Qtyperunit = $DATA["Qtyperunit"];



    $editunit = " UPDATE packge_unit SET packge_unit.Qtyperunit ='$Qtyperunit'
                      WHERE packge_unit.PackgeCode='$PackgeCode' ";
    mysqli_query($conn, $editunit);


    $return['status'] = "success";
    $return['form'] = "edit_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
function delete_item($conn, $DATA)
  {
    $PackgeCode = $DATA["PackgeCode"];

    $delete_unit = "DELETE FROM packge_unit WHERE PackgeCode = $PackgeCode  ";
    mysqli_query($conn, $delete_unit);

    $return['status'] = "success";
    $return['form'] = "delete_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
function add_item($conn, $DATA)
  {
    $PackgeName_add         = $DATA["PackgeName_add"];
    $Qtyperunit_add           = $DATA["Qtyperunit_add"];

    // ===================
    $Sql_ID = "SELECT
                          (PackgeCode) + 1 AS ID
                        FROM
                          packge_unit
                        ORDER BY
                          PackgeCode DESC
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
    
    $addunit = "INSERT INTO packge_unit (
                                    PackgeCode,
                                    PackgeName,
                                    Qtyperunit
                                  )
                                  VALUES
                                    (
                                      $ID,
                                      '$PackgeName_add',
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