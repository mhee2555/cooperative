<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");

function checklogin($conn,$DATA)
{
    $username = $DATA['USERNAME'];
    $password = $DATA['PASSWORD'];

    $boolean = false;

    $Sql = "SELECT
                    employee.ID,
                    employee.FName,
                    employee.PmID,
										Permission.Permission
                FROM
                    employee 
                INNER JOIN permission ON permission.PmID = employee.PmID 
                WHERE 
                    employee.UserName = '$username' 
                    AND employee.`Password` = '$password' ";


    $meQuery = mysqli_query($conn,$Sql);
    while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $_SESSION['PmID']        = $Result['PmID'];
        $_SESSION['FName']     = $Result['FName'];
        $_SESSION['ID']             = $Result['ID'];
        $_SESSION['Permission']       = $Result['Permission'];
        $return['PmID']         = $Result['PmID'];

        $boolean = true;
      }
      if($boolean)
      {
        $return['status'] = "success";
        $return['form'] = "chk_login";
        $return['msg'] = "เข้าสู่ระบบสำเร็จ";  
        echo json_encode($return);
        mysqli_close($conn);
      }else 
      {
        $return['status'] = "failed";
        $return['msg'] = "Username or Password is Wrong!";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }
}









$data = $_POST['DATA'];
$DATA = json_decode(str_replace ('\"','"', $data), true);

    if ($DATA['STATUS'] == 'checklogin') 
    {
      checklogin($conn, $DATA);
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