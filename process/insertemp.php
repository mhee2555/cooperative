<?php
  session_start();
  require '../connect/connect.php';

  $count = 0;
  $FName_add = $_POST['FName_add'];
  $UserName_add = $_POST['UserName_add'];
  $address_add = $_POST['address_add'];
  $email_add = $_POST['email_add'];
  $Tel_add = $_POST['Tel_add'];
  $PmID_add = $_POST['PmID_add'];
  $Password_add = $_POST['Password_add'];
  $boolean = false ;

  $newname = $UserName_add;
  $lastname = explode('.',$_FILES['file']['name']);
  $filename = $newname.'.'.$lastname[1];

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
  if($_FILES['file']!="")
  {
    copy($_FILES['file']['tmp_name'], '../profile/img/' . $filename);
    $Sql = "INSERT INTO employee (
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
                          address,
                          pic
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
                            '$address_add',
                            '$filename' ) ";
  }
  else
  {
    $Sql = "INSERT INTO employee (
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
                          '$address_add')";
  }
  mysqli_query($conn, $Sql);
  echo json_encode($Sql);
  mysqli_close($conn);
?>