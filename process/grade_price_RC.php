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
    $sel = $DATA["sel"];
    $Showcustomer = " SELECT
                                      item.item_name,
                                      item.item_code,
	                                    type_item.type_name,
                                      item.item_type
                                    FROM
                                      item 
                                    INNER JOIN type_item ON type_item.id = item.item_type
                                    WHERE item.item_code = '$ID'
                                    ORDER BY
                                      item.item_code DESC";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['item_name']    = $Result['item_name'];
      $return['item_code'] = $Result['item_code'];
      $return['type_name'] = $Result['type_name'];
      $return['item_type'] = $Result['item_type'];
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
    $ID = $DATA["ID"];
    $item_name_edit = $DATA["item_name_edit"];
    $item_type_edit = $DATA["item_type_edit"];


    $editcustomer = " UPDATE item SET item.item_name ='$item_name_edit',item.item_type ='$item_type_edit'
                      WHERE item.item_code='$ID' ";
    mysqli_query($conn, $editcustomer);


    $return['status'] = "success";
    $return['form'] = "edit_item";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function delete_item($conn, $DATA)
  {
    $ID = $DATA["ID"];

    $delete_customer = "DELETE FROM item WHERE item_code = $ID  ";
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
      }elseif ($DATA['STATUS'] == 'delete_item') {
        delete_item($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'add_item_rice') {
        add_item_rice($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'Showitem_rice') {
        Showitem_rice($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'Get_item_rice') {
        Get_item_rice($conn, $DATA);  
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