<?php
session_start();
require '../connect/connect.php';

function getUser($conn, $DATA)
  {
    $Permission = $DATA["Permission"];
    $count = 0;

        $Showuser = " SELECT
        employee.FName,
        employee.UserName,
        employee.`Password`,
        employee.email,
        employee.Tel,
        permission.Permission,
        employee.ID
        FROM
        employee
        INNER JOIN permission ON employee.PmID = permission.PmID ";

        // ค้นหาจาก permission
        if($Permission > 0) {$Showuser .="WHERE employee.PmID = $Permission";}
        // 

      $Showuser.=" ORDER BY employee.ID DESC";

      $meQuery = mysqli_query($conn, $Showuser);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['FName']          = $Result['FName'];
        $return[$count]['UserName']     = $Result['UserName'];
        $return[$count]['Password']       = $Result['Password'];
        $return[$count]['email']              = $Result['email']; 
        $return[$count]['Tel']                  = $Result['Tel']; 
        $return[$count]['Permission']     = $Result['Permission'];
        $return[$count]['ID']                   = $Result['ID'];  
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
                          employee.FName,
                          employee.UserName,
                          employee.`Password`,
                          employee.email,
                          employee.Tel,
                          employee.ID,
                          employee.address,
                          permission.Permission,
                          employee.PmID,
                          employee.pic

                      FROM   employee
                      INNER JOIN permission ON employee.PmID = permission.PmID 
                      WHERE employee.ID='$ID' ";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['FName']    = $Result['FName'];
      $return['UserName'] = $Result['UserName'];
      $return['email']    = $Result['email']; 
      $return['Tel']      = $Result['Tel']; 
      $return['ID']       = $Result['ID'];
      $return['pic'] = $Result['pic']==null?'default_img.png':$Result['pic'];
      $return['address']       = $Result['address']; 
      $return['Password']       = $Result['Password']; 
      $return['Permission']       = $Result['Permission']; 
      $return['PmID']       = $Result['PmID']; 
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
    $UserName_edit = $DATA["UserName_edit"];
    $address_edit = $DATA["address_edit"];
    $email_edit = $DATA["email_edit"];
    $Tel_edit = $DATA["Tel_edit"];
    $PmID_edit = $DATA["PmID_edit"];
    $Password_edit = $DATA["Password_edit"];

    $editcustomer = " UPDATE employee SET employee.FName ='$FName_edit',employee.UserName ='$UserName_edit',employee.email ='$email_edit',employee.Tel ='$Tel_edit',employee.address ='$address_edit'
                      ,employee.PmID ='$PmID_edit',employee.Password ='$Password_edit'
                      WHERE employee.ID='$ID' ";
    mysqli_query($conn, $editcustomer);


    $return['status'] = "success";
    $return['form'] = "edit_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;

  }
  function delete_customer($conn, $DATA)
  {
    $ID = $DATA["ID"];

    $delete_customer = "DELETE FROM employee WHERE ID = $ID  ";
    mysqli_query($conn, $delete_customer);

    $return['status'] = "success";
    $return['form'] = "delete_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }
  function add_customer($conn, $DATA)
  {
    $FName_add           = $DATA["FName_add"];
    $UserName_add      = $DATA["UserName_add"];
    $address_add          = $DATA["address_add"];
    $email_add              = $DATA["email_add"];
    $Tel_add                  = $DATA["Tel_add"];
    $PmID_add              = $DATA["PmID_add"];
    $Password_add        = $DATA["Password_add"];

    // ===================
    $Sql_ID = "SELECT
                        (ID) + 1 AS ID
                      FROM
                        employee
                      ORDER BY
                        ID DESC
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
    
    $addcustomer = "INSERT INTO employee (
                                    ID,
                                    FName,
                                    PmID,
                                    UserName,
                                    `Password`,
                                    email,
                                    Start_Date,
                                    Modify_User,
                                    Modify_Date,
                                    Tel,
                                    address
                                  )
                                  VALUES
                                    (
                                      $ID,
                                      '$FName_add',
                                      $PmID_add,
                                      '$UserName_add',
                                      '$Password_add',
                                      '$email_add',
                                      NOW(),
                                      $ID,
                                      NOW(),
                                      '$Tel_add',
                                      '$address_add'
                                    )
            ";

    mysqli_query($conn, $addcustomer);



    $return['status'] = "success";
    $return['form'] = "add_customer";
    echo json_encode($return);
    mysqli_close($conn);
    die;
  }






  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'getUser'){
        getUser($conn, $DATA);
      }elseif ($DATA['STATUS'] == 'show_detail_customer') {
        show_detail_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'edit_customer') {
        edit_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'delete_customer') {
        delete_customer($conn, $DATA);  
      }elseif ($DATA['STATUS'] == 'add_customer') {
        add_customer($conn, $DATA);  
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