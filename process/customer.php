<?php
session_start();
require '../connect/connect.php';

function getUser($conn, $DATA)
  {
    $count = 0;

    $Showuser = " SELECT
                                users.FName,
                                users.UserName,
                                users.`Password`,
                                users.email,
                                users.Tel
                            FROM   users ";
    $meQuery = mysqli_query($conn, $Showuser);
    while ($Result = mysqli_fetch_assoc($meQuery)) {
      $return[$count]['FName']          = $Result['FName'];
      $return[$count]['UserName']     = $Result['UserName'];
      $return[$count]['Password']       = $Result['Password'];
      $return[$count]['email']              = $Result['email']; 
      $return[$count]['Tel']                  = $Result['Tel']; 
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