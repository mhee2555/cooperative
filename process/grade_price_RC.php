<?php
session_start();
require '../connect/connect.php';

function Showitem($conn, $DATA)
  {
    $count = 0;

        $Showitem = "SELECT 
                            moisture.ID_moisture,
                            moisture.deduct_price,
                            moisture.moisture_name 
                     FROM moisture ";

      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['ID_moisture']          = $Result['ID_moisture'];
        $return[$count]['moisture_name']          = $Result['moisture_name'];
        $return[$count]['deduct_price']          = $Result['deduct_price'];
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
  function Showitem_rice($conn, $DATA)
  {
    $count = 0;

        $Showitem = "SELECT
                          grade_price_rice.ID_Grade,
                          item.item_name,
                          grade_price_rice.Grade
                          
                      FROM
                          grade_price_rice
                          INNER JOIN item ON grade_price_rice.item_code = item.item_code
                      ";

      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['ID_Grade']          = $Result['ID_Grade'];
        $return[$count]['item_name']          = $Result['item_name'];
        $return[$count]['Grade']          = $Result['Grade'];
        $count++;
      }
      $return['count']  = $count;
    if($count>0){
      $return['status'] = "success";
      $return['form'] = "Showitem_rice";
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
    $ID = $DATA["ID"];
    $Showcustomer = " SELECT ID_Grade,Grade 
                      FROM grade_price_rice
                      WHERE ID_Grade= $ID ";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['ID_Grade']    = $Result['ID_Grade'];
      $return['Grade'] = $Result['Grade'];
      $count=1;
      
    if($count>0){
      $return['status'] = "success";
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
  function show_detail_moisture($conn, $DATA)
  {
    $ID = $DATA["ID"];
    $Showcustomer = " SELECT 
                            moisture.ID_moisture,
                            moisture.moisture_name,
                            moisture.deduct_price 
                        FROM moisture
                        WHERE moisture.ID_moisture = $ID";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['ID_moisture']    = $Result['ID_moisture'];
      $return['moisture_name'] = $Result['moisture_name'];
      $return['deduct_price']    = $Result['deduct_price'];

      $count=1;
      
    if($count>0){
      $return['status'] = "success";
      $return['form'] = "show_detail_moisture";
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
    $ID = $DATA["ID"];
    $item_pirce_edit = $DATA["item_pirce_edit"];


    $editcustomer = " UPDATE grade_price_rice SET grade_price_rice.Grade =$item_pirce_edit
                      WHERE grade_price_rice.ID_Grade = $ID ";
    mysqli_query($conn, $editcustomer);


    $return['status'] = "success";
    $return['form'] = "edit_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function edit_moisture($conn, $DATA)
  {
    $ID = $DATA["ID"];
    $deduct_price_edit = $DATA["deduct_price_edit"];


    $editcustomer = " UPDATE moisture SET moisture.deduct_price =$deduct_price_edit
                      WHERE moisture.ID_moisture = $ID ";
    mysqli_query($conn, $editcustomer);


    $return['status'] = "success";
    $return['form'] = "edit_moisture";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function delete_item($conn, $DATA)
  {
    $ID = $DATA["ID"];
    $num = $DATA["num"];
    if($num==2){
      $delete_customer = "DELETE FROM grade_price_rice WHERE ID_Grade = $ID   ";
    }else{
      $delete_customer = "DELETE FROM moisture WHERE ID_moisture = $ID   ";
    }
    
    mysqli_query($conn, $delete_customer);

    $return['status'] = "success";
    $return['form'] = "delete_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  function add_item_rice($conn, $DATA)
  {
    $item_price_add         = $DATA["item_price_add"];
    $item_type_add_rice           = $DATA["item_type_add_rice"];

    // ===================
    $addcustomer = "INSERT INTO grade_price_rice(
                                                  item_code,
                                                  Grade
                                                )
                                                VALUES
                                                (
                                                  '$item_type_add_rice',
                                                  $item_price_add
                                                )
            ";

    mysqli_query($conn, $addcustomer);
    $return['status'] = "success";
    $return['form'] = "add_item_rice";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  function Get_item_rice($conn, $DATA)
  {
        $count = 0;

        $Showitem = "SELECT
                        item.item_name,
                        item.item_code
                    FROM
                        item
                    WHERE item.item_type =1
                      ";

        $meQuery = mysqli_query($conn, $Showitem);
        while ($Result = mysqli_fetch_assoc($meQuery)) {
          $return[$count]['item_code']          = $Result['item_code'];
          $return[$count]['item_name']          = $Result['item_name'];
          $count++;
        }
        $return['count']  = $count;
      if($count>0){
        $return['status'] = "success";
        $return['form'] = "Get_item_rice";
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





  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'Showitem'){
        Showitem($conn, $DATA);
      }elseif ($DATA['STATUS'] == 'show_detail_item') {
        show_detail_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'edit_item') {
        edit_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'edit_moisture') {
        edit_moisture($conn, $DATA);
      }elseif ($DATA['STATUS'] == 'delete_item') {
        delete_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'add_item_rice') {
        add_item_rice($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'Showitem_rice') {
        Showitem_rice($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'Get_item_rice') {
        Get_item_rice($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'show_detail_moisture') {
        show_detail_moisture($conn, $DATA);  
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