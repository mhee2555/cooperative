<?php
session_start();
require '../connect/connect.php';

function getUser($conn, $DATA)
  {
    $count = 0;

    $Showuser = " SELECT
                                users.FName,
                                users.email,
                                users.Tel,
                                users.ID
                            FROM   users ";
    $meQuery = mysqli_query($conn, $Showuser);
    while ($Result = mysqli_fetch_assoc($meQuery)) {
      $return[$count]['FName']    = $Result['FName'];
      $return[$count]['email']    = $Result['email']; 
      $return[$count]['Tel']      = $Result['Tel']; 
      $return[$count]['ID']      = $Result['ID']; 
      $count++;
    }
      $return['count']  = $count;
    if($count>0){
      $return['status'] = "success";
      $return['form'] = "getUser";
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
  function show_detail_customer($conn, $DATA)
  {
    $ID = $DATA["ID"];
    $sel = $DATA["sel"];
    $Showcustomer = " SELECT
                          users.FName,
                          users.email,
                          users.Tel,
                          users.ID,
                          users.address,
                          users.type
                      FROM   users
                      WHERE users.ID='$ID' ";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['FName']    = $Result['FName'];
      $return['email']    = $Result['email']; 
      $return['Tel']      = $Result['Tel']; 
      $return['ID']       = $Result['ID'];
      $return['address']       = $Result['address']; 
      $return['type']       = $Result['type']; 
      $count=1;
      
    if($count>0){
      $return['status'] = "success";
      $return['sel'] = $sel;
      $return['form'] = "show_detail_customer";
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

  function edit_customer($conn, $DATA)
  {
    $ID = $DATA["ID"];
    $FName_edit = $DATA["FName_edit"];
    // $UserName_edit = $DATA["UserName_edit"];
    $address_edit = $DATA["address_edit"];
    $email_edit = $DATA["email_edit"];
    $Tel_edit = $DATA["Tel_edit"];
    $c_type_edit = $DATA["c_type_edit"];
    
    $editcustomer = " UPDATE users SET users.FName ='$FName_edit',users.email ='$email_edit',users.Tel ='$Tel_edit',users.address ='$address_edit', type ='$c_type_edit'
                      WHERE users.ID='$ID' ";
    mysqli_query($conn, $editcustomer);


    $return['status'] = "success";
    $return['form'] = "edit_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function add_customer($conn, $DATA)
  {

    $FName_add = $DATA["FName_add"];
    // $UserName_add = $DATA["UserName_add"];
    $address_add = $DATA["address_add"];
    $email_add = $DATA["email_add"];
    $Tel_add = $DATA["Tel_add"];
    $c_type = $DATA["c_type"];

    $Sql_ID = "SELECT (ID)+1 AS ID FROM users
               ORDER BY ID DESC LIMIT 1 ";
    $meQuery = mysqli_query($conn, $Sql_ID);
    $Result2 = mysqli_fetch_assoc($meQuery); 
    $ID =  $Result2['ID']; 
    if($ID==null){
      $ID=1;
    }else{
      $ID =  $Result2['ID']; 
    }
    $addcustomer = "INSERT INTO users
                    (ID,FName,email,Start_Date,Modify_User,Modify_Date,Tel,address,type)
                    VALUES
                    ($ID,'$FName_add','$email_add',NOW(),$ID,NOW(),'$Tel_add','$address_add','$c_type')    
            ";
            // $return['sql'] = $addcustomer;
    mysqli_query($conn, $addcustomer);


    $return['status'] = "success";
    $return['form'] = "add_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function delete_customer($conn, $DATA)
  {
    $ID = $DATA["ID"];

    $delete_customer = "DELETE FROM users WHERE ID = $ID  ";
    mysqli_query($conn, $delete_customer);

    $return['status'] = "success";
    $return['form'] = "delete_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }

  
  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'getUser'){
        getUser($conn, $DATA);
      } elseif ($DATA['STATUS'] == 'show_detail_customer') {
        show_detail_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'edit_customer') {
        edit_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'add_customer') {
        add_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'delete_customer') {
        delete_customer($conn, $DATA);  
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