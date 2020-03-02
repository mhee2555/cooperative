<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");
$PmID = $_SESSION['PmID'];
$Userid = $_SESSION['ID'];
$FName = $_SESSION['FName'];
$Permission = $_SESSION['Permission'];

// session_destroy();
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/img/basic/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Krub&display=swap" rel="stylesheet">
    <!-- <link href="../dist/css/sweetalert2.css" rel="stylesheet"> -->
    <script src="../dist/js/sweetalert2.min.js"></script>
    <script src="../dist/js/jquery-3.3.1.min.js"></script>
    <script src="../datepicker/dist/js/datepicker-en.js"></script>
    <link href="../datepicker/dist/css/datepicker.min.css" rel="stylesheet" type="text/css">
    <script src="../datepicker/dist/js/datepicker.th.js"></script>
    <script src="../datepicker/dist/js/i18n/datepicker.en.js"></script>
    <title>Paper</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
        $(document).ready(function(e)
        {
            showchartbuy();
        });

        function showchartbuy()
        {
            var data = 
                {
                    'STATUS'      : 'showchartbuy'
                };
                senddata(JSON.stringify(data));
        }


        // END FUNCTION

        function senddata(data)
        {
            var form_data = new FormData();
            form_data.append("DATA",data);
            var URL = '../process/main.php';
            $.ajax
            ({
                url: URL,
                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (result) 
                {
                    try 
                    {
                        var temp = $.parseJSON(result);
                    } 
                    catch (e) 
                    {
                        console.log('Error#542-decode error');
                    }
                    swal.close();
                    if(temp["status"]=='success')
                    {
                        if(temp["form"]=='showchartbuy')
                        {

                            var showdatebuy = "ยอดซื้อลำไยประจำวัน ( "+temp[0]['DocDate']+" ) "
                            $("#datebuy").text(showdatebuy);


                            
                            var name = [];
                            var marks = [];
                            for (var i = 0; i < temp['Row']; i++) 
                            {
                                name.push(temp[i]['item_name']);
                                marks.push(temp[i]['kilo']);
                            }
                                         var chartdata = {
                                            labels: name,
                                            datasets: [
                                                {
                                                    label: 'ยอดซื้อลำไยเข้า (กก)',
                                                    backgroundColor: '#49e2ff',
                                                    borderColor: '#46d5f1',
                                                    hoverBackgroundColor: '#CCCCCC',
                                                    hoverBorderColor: '#666666',
                                                    data: marks
                                                }
                                            ]
                                        };

                                        var graphTarget = $("#graphCanvas");
                                        var barGraph = new Chart(graphTarget, {
                                            type: 'bar',
                                            data: chartdata
                                        });
                        }

                    }
                    else if (temp['status']=="failed") 
                    {
                        switch (temp['msg']) 
                        {
                        case "searchfailed":
                                    temp['msg'] = "ไม่พบเอกสารของวันที่ "+temp['date']+" ";
                            break;
                        case "Detailfail":
                                    $( "#TableDetail tbody" ).empty();
                                    temp['msg'] = "เอกสาร "+temp['DocNo']+" ไม่มีรายละเอียด ";
                            break;
                        case "showcharterror":
                                    temp['msg'] = "ไม่มีการซื้อของวันที่ "+temp['date']+" ";
                            break;
                        case "notfound":
                                    temp['msg'] = "<?php echo $array['notfoundmsg'][$language]; ?>";
                            break;
                        case "addsuccess":
                            temp['msg'] = "<?php echo $array['addsuccessmsg'][$language]; ?>";
                            break;
                        case "addfailed":
                                    temp['msg'] = "<?php echo $array['addfailedmsg'][$language]; ?>";
                            break;
                        case "editsuccess":
                                    temp['msg'] = "<?php echo $array['editsuccessmsg'][$language]; ?>";
                            break;
                        case "editfailed":
                                    temp['msg'] = "<?php echo $array['editfailedmsg'][$language]; ?>";
                            break;
                        case "cancelsuccess":
                                    temp['msg'] = "<?php echo $array['cancelsuccessmsg'][$language]; ?>";
                            break;
                        case "cancelfailed":
                                    temp['msg'] = "<?php echo $array['cancelfailed'][$language]; ?>";
                            break;
                        case "nodetail": 
                                    temp['msg'] = "<?php echo $array['nodetail'][$language]; ?>";
                            break;
                            case "adduserfacfailed":
                                    temp['msg'] = "<?php echo $array['adduserfacfailed'][$language]; ?>";
                            break;
                        }
                            swal({
                            title: '',
                            text: temp['msg'],
                            type: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            showConfirmButton: false,
                            timer: 2000,
                            confirmButtonText: 'Ok'
                            })
                    }
                },
            });
        }
    </script>


    <style>
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #F5F8FA;
            z-index: 9998;
            text-align: center;
        }

        .plane-container {
            position: absolute;
            top: 50%;
            left: 50%;
        }
    </style>
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
</head>


<body class="light">
<!-- Pre loader -->
<div id="loader" class="loader">
    <div class="plane-container">
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div>
    </div>
</div>
<div id="app">

<?php include 'menubar.php';?>

<div class="page has-sidebar-left">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-bubble_chart"></i>
                        หน้าหลัก
                    </h4>
                </div>
            </div>
            <div class="row">
                    <ul class="nav responsive-tab nav-material nav-material-white">
                            <li>
                                <a class="nav-link active" href="panel-element-morris.html">
                                    <i class="icon icon-bubble_chart"></i>หน้าหลัก</a>
                            </li>
                        </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row my-3">
            <!-- bar chart -->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="card " id="chartbuy">
                    <div class='card-header white'> 
                        <strong id="datebuy"> </strong>
                    </div>
                    <div class="card-body p-0">
                        <div style="height: 450px">
                            <canvas  id="graphCanvas"></canvas>
                        </div>                    
                    </div>
                </div>
            </div>





            <!-- /line graph -->
        </div>
    </div>
</div>



<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg shadow white fixed"></div>
</div>
<!--/#app -->
<script src="assets/js/app.js"></script>




<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>