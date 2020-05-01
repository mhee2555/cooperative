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
    
            (function ($) {
            $(document).ready(function () {


                $("#DateBuy_Start").datepicker({
                    onSelect: function (date, el) {
                        showchartbuy();
                    }
                });

                $("#DateBuy_End").datepicker({
                    onSelect: function (date, el) {
                        showchartbuy();
                    }
                });

                $("#DateSale_Start").datepicker({
                    onSelect: function (date, el) {
                        showchartsale();
                    }
                });

                $("#DateSale_End").datepicker({
                    onSelect: function (date, el) {
                        showchartsale();
                    }
                });

                $("#Date_Pro").datepicker({
                    onSelect: function (date, el) {
                        showpro();
                    }
                });

                $("#Date_Pack").datepicker({
                    onSelect: function (date, el) {
                        showpack();
                    }
                });
            });
        })(jQuery);



        $(document).ready(function(e)
        {

                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();
                var output = d.getFullYear() + '-' +
                    ((''+month).length<2 ? '0' : '') + month + '-' +
                    ((''+day).length<2 ? '0' : '') + day;
                $("#Date_Pro").val(output);
                $("#Date_Pack").val(output);


            showpro();
            showpack();
        });

        function showchartbuy()
        {
            var type_buy_chart = $("#type_buy_chart").val();
            var type_buy_chart_select = $("#type_buy_chart_select").val();
            var DateBuy_Start = $("#DateBuy_Start").val();
            var DateBuy_End = $("#DateBuy_End").val();
            
            var data = 
                {
                    'STATUS'         : 'showchartbuy',
                    'type_buy_chart' : type_buy_chart,
                    'DateBuy_Start'  : DateBuy_Start,
                    'DateBuy_End'    : DateBuy_End,
                    'type_buy_chart_select'    : type_buy_chart_select
                };
                senddata(JSON.stringify(data));
        }

        function showchartsale()
        {
            var type_sale_chart = $("#type_sale_chart").val();
            var type_sale_chart_select = $("#type_sale_chart_select").val();
            var DateSale_Start = $("#DateSale_Start").val();
            var DateSale_End = $("#DateSale_End").val();
            
            var data = 
                {
                    'STATUS'         : 'showchartsale',
                    'type_sale_chart' : type_sale_chart,
                    'DateSale_Start'  : DateSale_Start,
                    'DateSale_End'    : DateSale_End,
                    'type_sale_chart_select'    : type_sale_chart_select
                };
                senddata(JSON.stringify(data));
        }

        function showpro()
        {
            var type_Pro = $("#type_Pro").val();
            var Date_Pro = $("#Date_Pro").val();

            var data = 
                {
                    'STATUS'   : 'showpro',
                    'type_Pro' : type_Pro,
                    'Date_Pro' : Date_Pro
                };
                senddata(JSON.stringify(data));
        }

        function showpack()
        {
            var type_Pack = $("#type_Pack").val();
            var Date_Pack = $("#Date_Pack").val();

            var data = 
                {
                    'STATUS'   : 'showpack',
                    'type_Pack' : type_Pack,
                    'Date_Pack' : Date_Pack
                };
                senddata(JSON.stringify(data));
        }

        
        function ShowReport(type)
        {
          $('#namereport').val(type);
          
          // divsdate = วันเรึ่มต้น
          // divedate = วันสิ้นสุด
          // divxmonth = เดือน 
          // divyear = ปี
          // divbtn = ปุ่ม

          if(type == 'Report_buy_lg_between.php' || type == 'Report_buy_rc_between.php' || type == 'Report_sale_lg_bt.php' || type == 'Report_sale_rc_bt.php' || type == 'Report_draw_daily_lg.php' || type == 'Report_draw_daily_rc.php' || type == 'Report_process_lg.php' || type == 'Report_process_rc.php' || type == 'Report_pk_lg_bt.php' || type == 'Report_pk_rc_bt.php' || type == 'Report_receive_stock_unprocess.php' || type == 'Report_receive_st_process.php')
          {
            $('#divsdate').attr('hidden' , false); 
            $('#divedate').attr('hidden' , false);
            $('#divbtn').attr('hidden' , false);


            $('#divxmonth').attr('hidden' , true);
            $('#divxyear').attr('hidden' , true);
          }
          else if(type == 'Report_buylg_Y.php' || type == 'Report_buyrc_Y.php' || type == 'Report_sale_lg_Y.php' || type == 'Report_sale_rc_Y.php')
          {
            $('#divxyear').attr('hidden' , false);
            $('#divbtn').attr('hidden' , false);

            $('#divxmonth').attr('hidden' , true);
            $('#divsdate').attr('hidden' , true);
            $('#divedate').attr('hidden' , true);
          }
          else if(type == 'Report_receive_stock_unprocess_M.php'  || type == 'Report_receive_st_processM.php')
          {
            $('#divxyear').attr('hidden' , false);
            $('#divxmonth').attr('hidden' , false);
            $('#divbtn').attr('hidden' , false);

            $('#divsdate').attr('hidden' , true);
            $('#divedate').attr('hidden' , true);
          }



        }

        function gotoreport()
        {
          var type =  $('#namereport').val();
          var sDate =  $('#sDate').val();
          var eDate =  $('#eDate').val();
          var xYear =  $('#xYear').val();
          var xMonth =  $('#xMonth').val();
          var option = "";

          if(type == 'Report_buy_lg_between.php' || type == 'Report_buy_rc_between.php' || type == 'Report_sale_lg_bt.php' || type == 'Report_sale_rc_bt.php' || type == 'Report_draw_daily_lg.php' || type == 'Report_draw_daily_rc.php' || type == 'Report_process_lg.php' || type == 'Report_process_rc.php' || type == 'Report_pk_lg_bt.php' || type == 'Report_pk_rc_bt.php' || type == 'Report_receive_stock_unprocess.php' || type == 'Report_receive_st_process.php')
          {
            option = "?sDate="+sDate+"&eDate="+eDate;
          }
          else if(type == 'Report_buylg_Y.php' || type == 'Report_buyrc_Y.php' || type == 'Report_sale_lg_Y.php' || type == 'Report_sale_rc_Y.php')
          {
            option = "?xYear="+xYear;
          }
          else if(type == 'Report_receive_stock_unprocess_M.php'  || type == 'Report_receive_st_processM.php')
          {
            option = "?xYear="+xYear+"&xMonth="+xMonth;
          }

          var url  = "../tcreport/"+type+option;
          window.open(url);

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
                            $('#chart_buy').empty();
                            $('#chart_buy').append( '<canvas  id="graphCanvas"></canvas>' );
                            var showdatebuy = "ยอดซื้อลำไยประจำวัน ( "+temp[0]['DocDate']+" ) "
                            $("#datebuy").text(showdatebuy);
                            
                            var name = [];
                            var marks = [];
                            for (var i = 0; i < temp['Row']; i++) 
                            {
                                name.push(temp[i]['DocDate']);
                                marks.push(temp[i]['Total']);
                            }


            


                                         var chartdata = {
                                            labels: name,
                                            datasets: [
                                                {
                                                    label: 'ยอดเงินซื้อลำไยเข้า',
                                                    backgroundColor:
                                                    [
                                                       
                                                        '#33FFFF',
                                                        '#33FFCC',	
                                                        '#33FF99',
                                                        '#33FF66',
                                                        '#33FF33',
                                                    	'#33FF00',
                                                        '#33CCFF',
                                                        '#33CCCC',
                                                        '#33CC99',
                                                        '#33CC66',
                                                        '#33CC33',
                                                        '#33CC00',
                                                        '#3399FF',	'#3399CC',	'#339999',	'#339966',	'#339933',	'#339900',
                                                        '#3366FF',	'#3366CC',	'#336699',	'#336666',	'#336633',	'#336600',
                                                        '#3333FF',	'#3333CC',	'#333399',	'#333366',	'#333333',	'#333300'  

                                                    ],
                                                    borderColor: '#333300',
                                                    hoverBackgroundColor: '#666666',
                                                    hoverBorderColor: '#666666',
                                                    data: marks
                                                }
                                            ]
                                        };

                                        var graphTarget = $("#graphCanvas");
                                        var barGraph = new Chart(graphTarget, {
                                            type: temp['type_chart'],
                                            data: chartdata
                                        });

                        }

                        else if(temp["form"]=='showchartsale')
                        {
                            $('#chart_Sale').empty();
                            $('#chart_Sale').append( '<canvas  id="graphCanvas_Sale"></canvas>' );
                            var showdatebuy = "ยอดซื้อลำไยประจำวัน ( "+temp[0]['DocDate']+" ) "
                            $("#datebuy").text(showdatebuy);
                            
                            var name = [];
                            var marks = [];
                            for (var i = 0; i < temp['Row']; i++) 
                            {
                                name.push(temp[i]['DocDate']);
                                marks.push(temp[i]['Total']);
                            }


            


                                         var chartdata = {
                                            labels: name,
                                            datasets: [
                                                {
                                                    label: 'ยอดเงินขายลำไยออก',
                                                    backgroundColor:
                                                    [
                                                       
                                                        '#33FFFF',
                                                        '#33FFCC',	
                                                        '#33FF99',
                                                        '#33FF66',
                                                        '#33FF33',
                                                    	'#33FF00',
                                                        '#33CCFF',
                                                        '#33CCCC',
                                                        '#33CC99',
                                                        '#33CC66',
                                                        '#33CC33',
                                                        '#33CC00',
                                                        '#3399FF',	'#3399CC',	'#339999',	'#339966',	'#339933',	'#339900',
                                                        '#3366FF',	'#3366CC',	'#336699',	'#336666',	'#336633',	'#336600',
                                                        '#3333FF',	'#3333CC',	'#333399',	'#333366',	'#333333',	'#333300'  

                                                    ],
                                                    borderColor: '#333300',
                                                    hoverBackgroundColor: '#666666',
                                                    hoverBorderColor: '#666666',
                                                    data: marks
                                                }
                                            ]
                                        };

                                        var graphTarget = $("#graphCanvas_Sale");
                                        var barGraph = new Chart(graphTarget, {
                                            type: temp['type_chart'],
                                            data: chartdata
                                        });

                        }

                        else if(temp["form"]=='showpro')
                        {
                            $( "#Tablepro tbody" ).empty();

                            if(temp['Row'] > 0)
                            {
                                for (var i = 0; i < temp['Row']; i++) 
                                {
                                    
                                    if(temp[i]['IsStatus']==0)
                                    {
                                        Status = "ยังไม่ได้บันทึก";
                                        Style  = "style='color: #3399ff;'";
                                    }
                                    else if(temp[i]['IsStatus']==1)
                                    {
                                        Status = "รออนุมัติ";
                                        Style  = "style='color: #FF6633;'";
                                    }
                                    else if(temp[i]['IsStatus']==2)
                                    {
                                        Status = "รอการแปรรูป";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==8)
                                    {
                                        Status = "ปฎิเสธการขอเบิก";
                                        Style  = "style='color: #990000;'";
                                    }
                                    else if(temp[i]['IsStatus']==9)
                                    {
                                        Status = "ยกเลิกเอกสาร";
                                        Style  = "style='color: #ff0000;'";
                                    }
                                    var ii = i+1;
                                    StrTR =   "<tr>"+
                                                "<td >"+ ii +"</td>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td " +Style+ ">"+Status+"</td>"+

                                                "</tr>";

                                    $('#Tablepro tbody').append( StrTR );
                                }
                            }
                            else
                            {
                                $('#Tablepro tbody').empty();
                                var Str = "<tr ><td  class='text-center'></td><td  class='text-center'>ไม่มีเอกสารขอแปรรูป</td><td  class='text-center'></td></tr>";

                                    $('#Tablepro tbody').append( Str );
                            }
                   

                        }

                        else if(temp["form"]=='showpack')
                        {
                            $( "#Tablepack tbody" ).empty();

                            if(temp['Row'] > 0)
                            {
                                for (var i = 0; i < temp['Row']; i++) 
                                {
                                    
                                    if(temp[i]['IsStatus']==0)
                                    {
                                        Status = "ยังไม่ได้บันทึก";
                                        Style  = "style='color: #3399ff;'";
                                    }
                                    else if(temp[i]['IsStatus']==1)
                                    {
                                        Status = "รอการบรรจุภัณฑ์";
                                        Style  = "style='color: #FF6633;'";
                                    }
                                    else if(temp[i]['IsStatus']==2)
                                    {
                                        Status = "รอการแปรรูป";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==8)
                                    {
                                        Status = "ปฎิเสธการขอเบิก";
                                        Style  = "style='color: #990000;'";
                                    }
                                    else if(temp[i]['IsStatus']==9)
                                    {
                                        Status = "ยกเลิกเอกสาร";
                                        Style  = "style='color: #ff0000;'";
                                    }
                                    var ii = i+1;
                                    StrTR =   "<tr>"+
                                                "<td >"+ ii +"</td>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td " +Style+ ">"+Status+"</td>"+

                                                "</tr>";

                                    $('#Tablepack tbody').append( StrTR );
                                }
                            }
                            else
                            {
                                $('#Tablepack tbody').empty();
                                var Str = "<tr ><td  class='text-center'></td><td  class='text-center'>ไม่มีเอกสารขอแปรรูป</td><td  class='text-center'></td></tr>";

                                    $('#Tablepack tbody').append( Str );
                            }
                   

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
                                    // temp['msg'] = "ไม่มีการซื้อของวันที่ "+temp['date']+" ";
                                    temp['msg'] = "ไม่มีข้อมูลกราฟ ";

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
          body , .swal2-popup{
            font-family: 'Krub', sans-serif;
        }
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
                        รายงาน
                    </h4>
                </div>
            </div>
            <div class="row">
                    <ul class="nav responsive-tab nav-material nav-material-white">
                            <li>
                                <a class="nav-link active" href="panel-element-morris.html">
                                    <i class="icon icon-bubble_chart"></i>รายงาน</a>
                            </li>
                        </ul>
            </div>
        </div>
    </header>

    <div class="container-fluid">



      <div class="container">
          <div class="row" style="padding:20px;">

              <div class="col-md-5">
                <h5><b><u>รายชื่อรายงาน</u></b></h5>
              </div>

              <div class="col-md-2" id="">
                <div class="vl">
                </div>
              </div>

              <div class="col-md-5" id="divdetail">
                <h5><b><u>รายละเอียด</u></b></h5>
              </div>

          </div>

          <div class="row" >
            <div class="col-md-5" Style="height: 600px; overflow: auto;" id="rowbtn">
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_buy_lg_between.php')">รายงานการซื้อลำใยประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_buylg_Y.php')">รายงานการซื้อลำใยประจำปี</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_buy_rc_between.php')">รายงานการซื้อข้าวประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_buyrc_Y.php')">รายงานการซื้อข้าวประจำปี</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_sale_lg_bt.php')">รายงานการขายลำใยประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_sale_lg_Y.php')">รายงานการขายลำใยประจำปี</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_sale_rc_bt.php')">รายงานการขายข้าวประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_sale_rc_Y.php')">รายงานการซื้อข้าวประจำปี</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_draw_daily_lg.php')">รายงานการขอเบิกลำใยประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_draw_daily_rc.php')">รายงานการขอเบิกข้าวประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_process_lg.php')">รายงานการแปรรูปลำใยประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_process_rc.php')">รายงานการแปรรูปข้าวประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_pk_lg_bt.php')">รายงานการบรรจุภัณฑ์ลำใยประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_pk_rc_bt.php')">รายงานการบรรจุภัณฑ์ข้าวประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_receive_stock_unprocess.php')">รายงานรับเข้าสินค้ายังไม่ได้แปรรูปประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_receive_stock_unprocess_M.php')">รายงานรับเข้าสินค้ายังไม่ได้แปรรูปประจำเดือน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_receive_st_process.php')">รายงานรับเข้าสินค้าแปรรูปประจำวัน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('Report_receive_st_processM.php')">รายงานรับเข้าสินค้าแปรรูปประจำเดือน</button>
              <button class="margin-btn btn btn-success mt-3" style="width:95%" id="Report_Spore_Test" onclick="ShowReport('xxxx')">รายงานการซื้อขาย การเงิน</button>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-5" id="divdetail2">

                <div class="row" style="margin:10px;">
                  <div class="col-md-4" style="margin-bottom:5px;">
                    รายงาน
                  </div>
                  <div class="col-md-7">
                    <input type="text" class="form-control" name="namereport" id="namereport" value="" readonly style="width: 300px;">
                  </div>
                </div>

                <div class="row" style="margin:10px;" id="divsdate" hidden>
                  <div class="col-md-4">
                    วันที่(เริ่มต้น)
                  </div>
                  <div class="col-md-7">
                    <div class="input-group" style="width: 300px;">
                      <input type="text" class="form-control datepicker-here" id="sDate" data-language='en' data-date-format='dd/mm/yyyy' value="<?php echo date('d/m/Y'); ?>">
                      </div>
                  </div>
                </div>

                <div class="row" style="margin:10px;" id="divedate" hidden>
                  <div class="col-md-4">
                    วันที่(สิ้นสุด)
                  </div>
                  <div class="col-md-7">
                    <div class="input-group" style="width: 300px;">
                      <input type="text"  class="form-control datepicker-here" id="eDate" data-language='en' data-date-format='dd/mm/yyyy' value="<?php echo date('d/m/Y'); ?>">
                      </div>
                  </div>
                </div>


                <div class="row" style="margin:10px;" id="divxmonth" hidden>
                  <div class="col-md-4">
                    เดือน
                  </div>
                  <div class="col-md-7">
                    <select class="form-control" style="width: 300px;" name="xMonth" id="xMonth">
                      <option value='01'>มกราคม</option>
                      <option value='02'>กุมภาพันธ์</option>
                      <option value='03'>มีนาคม</option>
                      <option value='04'>เมษายน</option>
                      <option value='05'>พฤษภาคม</option>
                      <option value='06'>มิถุนายน</option>
                      <option value='07'>กรกฏาคม</option>
                      <option value='08'>สิงหาคม</option>
                      <option value='09'>กันยายน</option>
                      <option value='10'>ตุลาคม</option>
                      <option value='11'>พฤศจิกายน</option>
                      <option value='12'>ธันวาคม</option>
                    </select>
                  </div>
                </div>

                <div class="row" style="margin:10px;" id="divxyear" hidden>
                  <div class="col-md-4">
                    ปี
                  </div>
                  <div class="col-md-7">
                    <select class="form-control" style="width: 300px;" name="xYear" id="xYear">
                      <option value='2020'>2020</option>
                    </select>
                  </div>
                </div>


                <div class="row" style="margin:10px;" id="divbtn" hidden>
                  <div class="col-md-4"></div>
                  <!-- BUTTON -->
                  <div class="col-md-7" style="text-align:left;">
                    <button type="button" class="btn btn-warning" name="button" id="reportbtn" onclick="gotoreport();">ดูรายงาน</button>
                  </div>
                </div>

                </div>
            </div>





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