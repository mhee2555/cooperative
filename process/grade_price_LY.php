<?php
session_start();
require '../connect/connect.php';

function Showitem($conn, $DATA)
  {
    $item_type = $DATA["item_type"];
    $count = 0;

        $Showitem = "SELECT
                                  item.item_name,
                                  item.item_code
                                FROM
                                item";

        // ค้นหาจาก item_type
        if($item_type > 0) {$Showitem .=" WHERE item.item_type = $item_type";}
        // 

      $Showitem.=" ORDER BY item.item_code DESC";

      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['item_name']          = $Result['item_name'];
        $return[$count]['item_code']          = $Result['item_code'];
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
  function add_item($conn, $DATA)
  {
    $item_name_add         = $DATA["item_name_add"];
    $item_type_add           = $DATA["item_type_add"];

    // ===================
    $Sql_ID = "SELECT
                          (item_code) + 1 AS ID
                        FROM
                          item
                        ORDER BY
                          item_code DESC
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
    
    $addcustomer = "INSERT INTO item (
                                    item_code,
                                    item_name,
                                    item_type
                                  )
                                  VALUES
                                    (
                                      $ID,
                                      '$item_name_add',
                                      $item_type_add
                                    )
            ";

    mysqli_query($conn, $addcustomer);



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