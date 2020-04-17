<?php
  session_start();
  require '../connect/connect.php';
  date_default_timezone_set("Asia/Bangkok");


  $Sign   = $_POST['SignSVG'];
  $Table  = $_POST['Table']; 
  $Column = $_POST['Column'];
  $DocNo  = $_POST['DocNo'];

  if($Column == 'signStart')
  {
    $Update = "UPDATE $Table SET $Column = '$Sign', DvStartTime = NOW() , IsStatus = 2 WHERE DocNo = '$DocNo'";
    mysqli_query($conn, $Update);
  }
  else
  {
    $Update = "UPDATE $Table SET $Column = '$Sign', DvStartTime = NOW()  WHERE DocNo = '$DocNo'";
    mysqli_query($conn, $Update);
  }




