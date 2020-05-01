<?php
  session_start();
  require '../connect/connect.php';

  $count = 0;
  $ID = $_POST['ID'];
  $FName_edit = $_POST['FName_edit'];
  $UserName_edit = $_POST['UserName_edit'];
  $address_edit = $_POST['address_edit'];
  $email_edit = $_POST['email_edit'];
  $Tel_edit = $_POST['Tel_edit'];
  $PmID_edit = $_POST['PmID_edit'];
  $Password_edit = $_POST['Password_edit'];
  $boolean = false ;

  $newname = $UserName_edit;
  $lastname = explode('.',$_FILES['file']['name']);
  $filename = $newname.'.'.$lastname[1];


  if($_FILES['file']!="")
  {
    copy($_FILES['file']['tmp_name'], '../profile/img/' . $filename);
    $Sql = "UPDATE employee 
              SET employee.FName = '$FName_edit',
              employee.UserName = '$UserName_edit',
              employee.email = '$email_edit',
              employee.Tel = '$Tel_edit',
              employee.address = '$address_edit',
              employee.PmID = '$PmID_edit',
              employee.PASSWORD = '$Password_edit',
              employee.pic='$filename'
            WHERE
              employee.ID = '$ID'";
  }
  else
  {
    $Sql = "UPDATE employee 
              SET employee.FName = '$FName_edit',
              employee.UserName = '$UserName_edit',
              employee.email = '$email_edit',
              employee.Tel = '$Tel_edit',
              employee.address = '$address_edit',
              employee.PmID = '$PmID_edit',
              employee.PASSWORD = '$Password_edit'
            WHERE
              employee.ID = '$ID'";
  }
  $_SESSION['pic']  = $filename==null?'default_img.png':$filename;
  mysqli_query($conn, $Sql);
  echo json_encode($result);
  mysqli_close($conn);
?>