<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");

$UserID = $_SESSION['Userid'];
$DocNo = $_POST['DocNo'];
$SignCode = $_POST['SignCode'];
$SignFnc = $_POST['SignFnc'];
$return['SignFnc'] = $SignFnc;



$count = "SELECT COUNT(DocNo) AS cnt FROM sale_longan WHERE DocNo = '$DocNo' ";
$meQuery = mysqli_query($conn, $count);
$Result = mysqli_fetch_assoc($meQuery);
$cnt = $Result['cnt'];

if($cnt > 0)
{
    if ($SignFnc == 'start_send')
    {
        $Sql = "UPDATE sale_longan SET DvStartTime = NOW(),signStart = '$SignCode' , IsStatus = 2 WHERE DocNo = '$DocNo'";
        mysqli_query($conn, $Sql);
    }
    else if ($SignFnc == 'end_send')
    {
        $Update = "UPDATE sale_longan SET signEnd = '$SignCode'  WHERE DocNo = '$DocNo'";
        mysqli_query($conn, $Update);
    }

}
else
{
    if ($SignFnc == 'start_send')
    {
        $Sql = "UPDATE sale_rice SET DvStartTime = NOW(),signStart = '$SignCode' , IsStatus = 2 WHERE DocNo = '$DocNo'";
        mysqli_query($conn, $Sql);
    }
    else if ($SignFnc == 'end_send')
    {
        $Update = "UPDATE sale_rice SET signEnd = '$SignCode'  WHERE DocNo = '$DocNo'";
        mysqli_query($conn, $Update);
    }
}



