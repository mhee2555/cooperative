<?php 
require '../connect/connect.php';


    $count = 0;
    $Showitem = "SELECT
                    item.item_name,	
                    grade_price_rice.Grade,
                    DATE(NOW()) as DATE
                FROM
                  grade_price_rice
                INNER JOIN item ON  grade_price_rice.item_code = item.item_code";
                $meQuery = mysqli_query($conn, $Showitem);
                while ($Result = mysqli_fetch_assoc($meQuery))
                {
                    $item_name[$count]  = $Result['item_name'];
                    $Grade[$count]      = $Result['Grade'];
                    $DATE      = $Result['DATE'];

                    $mess .=   $item_name[$count] . "  ราคา " . $Grade[$count] . "  บาท" . "\n" ;          
                    $count ++;
                }
                $header = "อัพเดตราคาข้าว ประจำวันที่  ";
                $message = $header . $DATE .
                        "\n". $mess ;


    

            sendlinemesg();
            header('Content-Type: text/html; charset=utf8');
            $res = notify_message($message);
        
    

    function sendlinemesg() {
        define('LINE_API',"https://notify-api.line.me/api/notify");
        define('LINE_TOKEN',"EkjSyS4jHyn97YQcjLoC17ggHRgkkqdPK35ZXx0rd0Z");

        function notify_message($message) {
            $queryData = array('message' => $message);
            $queryData = http_build_query($queryData,'','&');
            $headerOptions = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                                ."Authorization: Bearer ".LINE_TOKEN."\r\n"
                                ."Content-Length: ".strlen($queryData)."\r\n",
                    'content' => $queryData
                )
            );
            $context = stream_context_create($headerOptions);
            $result = file_get_contents(LINE_API, FALSE, $context);
            $res = json_decode($result);
            return $res;
        }
    }


?>