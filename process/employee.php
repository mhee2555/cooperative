<?php
session_start();
require '../connect/connect.php';

function getUser($conn, $DATA)
  {
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
      $meQuery = mysqli_query($conn, $Showuser);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['FName']          = $Result['FName'];
        $return[$count]['UserName']     = $Result['UserName'];
        $return[$count]['Password']       = $Result['Password'];
        $return[$count]['email']              = $Result['email']; 
        $return[$count]['Tel']                  = $Result['Tel']; 
        $return[$count]['Permission']     = $Result['Permission'];
        $return[$count]['ID']     = $Result['ID'];  
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
                          employee.ID
                      FROM   employee
                      WHERE employee.ID='$ID' ";
    $meQuery = mysqli_query($conn, $Showcustomer);
    $Result = mysqli_fetch_assoc($meQuery); 
      $return['FName']    = $Result['FName'];
      $return['UserName'] = $Result['UserName'];
      $return['email']    = $Result['email']; 
      $return['Tel']      = $Result['Tel']; 
      $return['ID']       = $Result['ID'];
      $return['address']       = $Result['address']; 
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










  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'getUser'){
        getUser($conn, $DATA);
      }elseif ($DATA['STATUS'] == 'show_detail_customer') {
        show_detail_customer($conn, $DATA);  
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