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
        permission.Permission
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










  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'getUser') 
      {
        getUser($conn, $DATA);
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