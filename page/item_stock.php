<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");
$FName = $_SESSION['FName'];
$PmID = $_SESSION['PmID'];
$Permission = $_SESSION['Permission'];
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

    <title>คลังสินค้า</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
        // ---------------------------------------------------
        (function ($) {
            $(document).ready(function () {
                $("#Search_date").datepicker({
                    onSelect: function (date, el) {
                        Showitem();
                    }
                });
            });
        })(jQuery);
        // ---------------------------------------------------
    $(document).ready(function(e)
    {
       // ===========DATE ITEM =======
      var d = new Date();
      var month = d.getMonth()+1;
      var day = d.getDate();
      var output = d.getFullYear() + '-' +
          ((''+month).length<2 ? '0' : '') + month + '-' +
          ((''+day).length<2 ? '0' : '') + day;
      $("#Search_date").val(output);
      $("#datepicker").val(output);
      $("#datepicker2").val(output);
        // ========

        Showtype();
        setTimeout(() => {
            Showitem();
        }, 200);
        // ค้นหา
        $("#Search_name").on("keyup", function() 
        {
            var value = $(this).val().toLowerCase();
            $("#Tableitem tbody tr").filter(function() 
            {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        // 
    });

    function Showitem()
    {
        var Search_type = $('#Search_type').val();
        var Search_date = $('#Search_date').val();
        var Search_unit = $('#Search_unit').val();
        if(Search_type == 'packing')
        {
            $('#Search_unit_row').attr('hidden' , false);
        }
        else
        {
            $('#Search_unit_row').attr('hidden' , true);
        }
        var data = 
        {
            'STATUS': 'Showitem',
            'Search_type':Search_type,
            'Search_date':Search_date,
            'Search_unit':Search_unit
        };
        senddata(JSON.stringify(data));
    }
    function Showtype()
    {
        var data = 
        {
            'STATUS': 'Showtype'
        };
        senddata(JSON.stringify(data));
    }
    function ShowSearch()
    {
        var datepicker = $("#datepicker").val();
        var data = 
        {
          'STATUS'  	: 'ShowSearch',
          'datepicker' : datepicker

        };
        senddata(JSON.stringify(data));
    }
    function ShowSearch_rice()
    {
        var datepicker = $("#datepicker2").val();
        var data = 
        {
          'STATUS'  	: 'ShowSearch_rice',
          'datepicker' : datepicker

        };
        senddata(JSON.stringify(data));
    }
    function showdetaildraw(DocNo)
    {
        var data = 
        {
            'STATUS'  : 'showdetaildraw',
            'DocNo'	  : DocNo
        };
        senddata(JSON.stringify(data));     
    }
    function showdetaildraw_rice(DocNo)
    {
        var data = 
        {
            'STATUS'  : 'showdetaildraw_rice',
            'DocNo'	  : DocNo
        };
        senddata(JSON.stringify(data));     
    }
    function approve(status)
    {
        var stock_code_Array = [];
        var Detail_give_Array = [];
        var DocNo = "";
        $('input[name="stock_code_Array"]').each(function() 
        {
            stock_code_Array.push($(this).val());
        });

        $('input[name="giveArray"]').each(function() 
        {
            Detail_give_Array.push($(this).val());
        });

        $('input[name="docno_detail"]').each(function() 
        {
            DocNo = $(this).val();
        });

        var give = Detail_give_Array.join(',') ;
        var stock_code = stock_code_Array.join(',') ;

        var data = 
        {
            'STATUS'  : 'approve',
            'stock_code': stock_code ,
            'give'	  : give ,
            'DocNo'	  : DocNo,
            'status'	  : status
        };
        $('#showdetaildraw').modal('toggle');
        senddata(JSON.stringify(data));    
    }
    function approve_rice(status)
    {
        var stock_code_Array = [];
        var Detail_give_Array = [];
        var DocNo = "";
        $('input[name="stock_code_Array2"]').each(function() 
        {
            stock_code_Array.push($(this).val());
        });

        $('input[name="giveArray2"]').each(function() 
        {
            Detail_give_Array.push($(this).val());
        });

        $('input[name="docno_detail2"]').each(function() 
        {
            DocNo = $(this).val();
        });

        var give = Detail_give_Array.join(',') ;
        var stock_code = stock_code_Array.join(',') ;

        var data = 
        {
            'STATUS'  : 'approve_rice',
            'stock_code': stock_code ,
            'give'	  : give ,
            'DocNo'	  : DocNo,
            'status'	  : status
        };
        $('#showdetaildraw_rice').modal('toggle');
        senddata(JSON.stringify(data));    
    }
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/item_stock.php';
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
                    if( (temp["form"]=='Showitem') )
                    {
                              $( "#Tableitem tbody" ).empty();

                              for (var i = 0; i < temp['count']; i++) 
                              {
                                 StrTR = "<tr ondblclick='showmodal("+temp[i]['item_code']+","+'1'+");'>"+
                                                "<td >"+(i+1)+"</td>"+
                                                "<td >"+temp[i]['item_name']+ ' ' +temp[i]['UnitName'] +"</td>"+
                                                "<td >"+temp[i]['item_qty']+"</td>"+
                                                "<td >"+temp[i]['item_ccqty']+"</td>"+
                                                "<td >"+temp[i]['Date_start']+"</td>"+
                                                "<td >"+temp[i]['Date_exp']+"</td>"+

                                                "</tr>";
                                   $('#Tableitem tbody').append( StrTR );
                              }

                    }
                    else if( (temp["form"]=='Showtype') )
                    {
                        $("#type").empty();
                        for (var i = 0; i < temp['count']; i++) 
                        {
                            var StrTr = "<option value = '"+temp[i]['id']+"'> " + temp[i]['type_name'] + " </option>"
                            $("#type").append(StrTr);
                        }

                        $("#Search_unit").empty();
                        for (var i = 0; i < temp['count_unit']; i++) 
                        {
                            var StrTr = "<option value = '"+temp[i]['UnitCode']+"'> " + temp[i]['UnitName'] + " </option>"
                            $("#Search_unit").append(StrTr);
                        }
                    }
                    else if(temp["form"]=='ShowSearch')
                    {
                        $( "#TableSearch tbody" ).empty();
                                for (var i = 0; i < temp['Row']; i++) 
                                {                                    
                                    if(temp[i]['IsStatus']==1)
                                    {
                                        Status = "ยังไม่ได้อนุมัติ";
                                        Style  = "style='color: #FF6633;'";
                                    }
                                    else if(temp[i]['IsStatus']==2)
                                    {
                                        Status = "อนุมัติเรียบร้อย";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==8)
                                    {
                                        Status = "ปฎิเสธการขอเบิก";
                                        Style  = "style='color: #990000;'";
                                    }
                                    StrTR =   "<tr ondblclick='showdetaildraw(\""+temp[i]['DocNo']+"\");'>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td>"+temp[i]['DocDate']+"</td>"+
                                                "<td>"+temp[i]['Modify_Date']+"</td>"+
                                                "<td>"+temp[i]['employee']+"</td>"+
                                                "<td " +Style+ ">"+Status+"</td>"+

                                                "</tr>";

                                    $('#TableSearch tbody').append( StrTR );
                                }
                    }
                    else if(temp["form"]=='ShowSearch_rice')
                    {
                        $( "#TableSearch_rice tbody" ).empty();
                                for (var i = 0; i < temp['Row']; i++) 
                                {                                    
                                    if(temp[i]['IsStatus']==1)
                                    {
                                        Status = "ยังไม่ได้อนุมัติ";
                                        Style  = "style='color: #FF6633;'";
                                    }
                                    else if(temp[i]['IsStatus']==2)
                                    {
                                        Status = "อนุมัติเรียบร้อย";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==8)
                                    {
                                        Status = "ปฎิเสธการขอเบิก";
                                        Style  = "style='color: #990000;'";
                                    }
                                    StrTR =   "<tr ondblclick='showdetaildraw_rice(\""+temp[i]['DocNo']+"\");'>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td>"+temp[i]['DocDate']+"</td>"+
                                                "<td>"+temp[i]['Modify_Date']+"</td>"+
                                                "<td>"+temp[i]['employee']+"</td>"+
                                                "<td " +Style+ ">"+Status+"</td>"+

                                                "</tr>";

                                    $('#TableSearch_rice tbody').append( StrTR );
                                }
                    }
                    else if(temp["form"]=='showdetaildraw')
                    {
                        $( "#Tabledetail tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                var chkunit ="<select  class='form-control'  id='detailUnit_"+i+"' disabled>";
                                    $.each(temp['Unit'], function(key, val)
                                    {
                                        if(temp[i]['UnitCode']==val.UnitCode)
                                        {
                                            chkunit += '<option selected value=" '+val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                        else
                                        {
                                            chkunit += '<option value="' +val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                    });
                                    chkunit += "</select>";

                                var cc = parseFloat(temp[i]['item_ccqty']);
                                var kilo = parseFloat(temp[i]['kilo']);
                                if(cc >= kilo)
                                {
                                    var sum = kilo;
                                }
                                else if (kilo > cc)
                                {
                                    var sum = cc;
                                }
                                var give = "<input type='text' id='Detail_give_"+i+"' class='form-control ' autocomplete='off'  name='giveArray'  placeholder='0.00' value="+sum+" disabled style='text-align: right;'><input type='hidden' name='stock_code_Array'  id='stock_code_"+i+"' value='"+temp[i]['stock_code']+"'><input type='hidden' name='docno_detail'   value='"+temp[i]['draw_DocNo']+"'>  ";
                                var datetime = "<div><strong>"+temp[i]['date']+"<strong></div><small>"+temp[i]['time']+"</small>";
                                
                                 StrTR = "<tr>"+
                                                "<td style='width: 20%;'>"+temp[i]['item_name']+"</td>"+
                                                "<td style='width: 20%;'>"+datetime+"</td>"+
                                                "<td style='width: 10%;'>"+temp[i]['item_ccqty']+"</td>"+
                                                "<td style='width: 10%;'>"+temp[i]['kilo']+"</td>"+
                                                "<td style='width: 20%;'>"+give+"</td>"+
                                                "<td style='width: 20%;'>"+chkunit+"</td>"+
                                                "</tr>";
   
                                   $('#Tabledetail tbody').append( StrTR );
                              }
                                      $('#showdetaildraw').modal('show');

                    }
                    else if(temp["form"]=='showdetaildraw_rice')
                    {
                        $( "#Tabledetail_rice tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                var cc = parseFloat(temp[i]['item_ccqty']);
                                var kilo = parseFloat(temp[i]['kilo']);
                                if(cc >= kilo)
                                {
                                    var sum = kilo;
                                }
                                else if (kilo > cc)
                                {
                                    var sum = cc;
                                }
                                var give = "<input type='text' id='Detail_give_"+i+"' class='form-control ' autocomplete='off'  name='giveArray2'  placeholder='0.00' value="+sum+" disabled><input type='hidden' name='stock_code_Array2'  id='stock_code_"+i+"' value='"+temp[i]['stock_code']+"'><input type='hidden' name='docno_detail2'   value='"+temp[i]['draw_DocNo']+"'>  ";
                                var datetime = "<div><strong>"+temp[i]['date']+"<strong></div><small>"+temp[i]['time']+"</small>";
                                
                                 StrTR = "<tr>"+
                                                "<td style='width: 20%;'>"+temp[i]['item_name']+"</td>"+
                                                "<td style='width: 20%;'>"+datetime+"</td>"+
                                                "<td style='width: 20%;'>"+temp[i]['item_ccqty']+"</td>"+
                                                "<td style='width: 20%;'>"+temp[i]['kilo']+"</td>"+
                                                "<td style='width: 20%;'>"+give+"</td>"+
                                                "</tr>";
   
                                   $('#Tabledetail_rice tbody').append( StrTR );
                              }
                                      $('#showdetaildraw_rice').modal('show');

                    }
                    //------------------------------------------------------------------------------
                }
                else if (temp['status']=="failed") 
                {
                    switch (temp['msg']) 
                    {
                        case "searchfailed":
                                temp['msg'] = "ไม่พบเอกสารของวันที่ "+temp['date']+" ";
                        break;
                        case "cantcreate":
                                temp['msg'] = "<?php echo $array['cantcreatemsg'][$language]; ?>";
                        break;
                        case "noinput":
                                temp['msg'] = "<?php echo $array['noinputmsg'][$language]; ?>";
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
       body, .swal2-popup{
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
    <!-- Js -->
    <!--
    --- Head Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->
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
<!-- ===== -->
<div class="page  has-sidebar-left height-full">
    <header class="blue accent-3 relative">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-package"></i>
                        คลังสินค้า
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all"
                           role="tab" aria-controls="v-pills-all"><i class="icon icon-home2"></i>คลังสินค้า</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                           aria-controls="v-pills-buyers"><i class="icon icon-face"></i>การขอเบิกลำใย</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab2" data-toggle="pill" href="#v-pills-buyers2" role="tab"
                           aria-controls="v-pills-buyers"><i class="icon icon-face"></i>การขอเบิกข้าว</a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
            <div class="row">
                <div class="col-md-3 mt-2 ">
                    <select class ="custom-select" id="Search_type"  onchange="Showitem()">
                            <option value="unprocess">สินค้ายังไม่ได้แปรรูป</option>
                            <option value="process">สินค้าแปรรูป</option>
                            <option value="packing">สินค้าบรรจุภัณฑ์</option>
                    </select>
                </div>
                <div class="col-md-3 mt-2 " >
                    <input type="text" class =  "form-control " placeholder="ค้นหาจากชื่อรายการ" id="Search_name">
                </div>
                <div class="col-md-3 mt-2 " >
                    <input type="text" class =  "form-control  datepicker-here " placeholder="ค้นหาจากวันที่" id="Search_date" data-language='en' data-date-format='yyyy-mm-dd'>
                </div>
                <div class="col-md-3 mt-2 " id= 'Search_unit_row' hidden>
                    <select  type="text" class =  "form-control " placeholder="หน่วยนับ" id="Search_unit" onchange="Showitem()"> </select>
                </div>
                <!-- <div class=" mt-2 ml-3" >
                <button type="button" class="btn btn-primary btn-lg" onclick="Showitem()"><i class="icon-search3"></i>ค้นหา</button>
                </div> -->
            </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <!-- SHOW item -->
                                    <table class="table table-striped table-hover r-0" id="Tableitem">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>NO.</th>
                                            <th>ชื่อรายการ</th>
                                            <th>จำนวนทั้งหมด</th>
                                            <th>จำนวนที่เหลือ</th>
                                            <th>เวลารับเข้า</th>
                                            <th>เวลาหมดอายุ</th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >                                    
                                        </tbody>
                                    </table>
                                    <!-- =============== -->
                                    <!-- ====== Tableitem_Longan ========= -->
                                    <table class="table table-striped table-hover r-0" id="Tableitem_Longan" hidden>
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>NO.</th>
                                            <th>NAME</th>
                                            <th>TEPY</th>
                                            <th>PIRCE UNIT</th>
                                            <th>QTY</th>
                                            <th>GRADE</th>
                                            <th>UNIT</th>
                                            <th hidden>ROLE</th>
                                            <th></th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >

                                        <tr hidden>
                                            <td >
                                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkSingle" id="user_id_1" required><label class="custom-control-label" for="user_id_1"></label></div>
                                            </td>

                                            <td>
                                                <div>
                                                    <div>
                                                        <strong>Alexander Pierce</strong>
                                                    </div>
                                                    <small> alexander@paper.com</small>
                                                </div>
                                            </td>

                                            <td>2</td>
                                            <td>256</td>

                                            <td>+92 333 123 136</td>
                                            <td hidden><span class="icon icon-circle s-12  mr-2 text-warning"></span> Inactive</td>
                                            <td hidden><span class="r-3 badge badge-success ">Administrator</span></td>


                                            <td>
                                                <a href="panel-page-profile.html"><i class="icon-eye mr-3"></i></a>
                                                <a href="panel-page-profile.html"><i class="icon-pencil"></i></a>
                                            </td>
                                        </tr>

                                        <tr hidden>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input checkSingle"
                                                           id="user_id_5" required><label
                                                        class="custom-control-label" for="user_id_5"></label>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="avatar avatar-md mr-3 mt-1 float-left">
                                                    <img  src="assets/img/dummy/u5.png" alt="">
                                                </div>
                                                <div>
                                                    <div>
                                                        <strong>Alexander Pierce</strong>
                                                    </div>
                                                    <small> alexander@paper.com</small>
                                                </div>
                                            </td>
                                            <td>2</td>
                                            <td>6,000</td>

                                            <td>+92 333 123 136</td>
                                            <td><span class="icon icon-circle s-12  mr-2 text-success"></span> Active</td>

                                            <td><span class="r-3 badge badge-warning">Seller</span></td>
                                            <td>
                                                <a href="panel-page-profile.html"><i class="icon-eye mr-3"></i></a>
                                                <a href="panel-page-profile.html"><i class="icon-pencil"></i></a>
                                            </td>
                                        </tr>
                                    
                                        </tbody>
                                    </table>
                                    <!-- =============== -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <nav class="my-3" aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#">Previous</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- SEARCH -->
            <div class="tab-pane animated fadeInUpShort" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
                <div class="row">
                    <div class="col-md-3 mt-2 ">
                    <input type="text" autocomplete="off" class ="form-control datepicker-here" id="datepicker" data-language='en' data-date-format='yyyy-mm-dd' placeholder="ค้นหาจากวันที่">
                    </div>
                    <div class="col-md-3 mt-2 ">
                        <input type="text" class =  "form-control " placeholder="ค้นหา" id="Search">
                    </div>
                    <div class="col-md-3  mt-2 ">
                    <button type="button" class="btn btn-primary btn-lg" onclick="ShowSearch()">
                    <i class="icon-search3"></i> ค้นหา </button>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <!-- SHOW USER -->
                                    <table class="table table-striped table-hover r-0" id="TableSearch">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>เลขที่เอกสาร</th>
                                            <th>วันที่เอกสาร</th>
                                            <th>วันที่บันทึก</th>
                                            <th>ผู้บันทึก</th>
                                            <th>สถานะ</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbody">                                    
                                        </tbody>
                                    </table>
                                    <!-- =============== -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END BUYERS -->

               <!-- SEARCH2 -->
               <div class="tab-pane animated fadeInUpShort" id="v-pills-buyers2" role="tabpanel" aria-labelledby="v-pills-buyers-tab2">
                <div class="row">
                    <div class="col-md-3 mt-2 ">
                    <input type="text" autocomplete="off" class ="form-control datepicker-here" id="datepicker2" data-language='en' data-date-format='yyyy-mm-dd' placeholder="ค้นหาจากวันที่">
                    </div>
                    <div class="col-md-3 mt-2 ">
                        <input type="text" class =  "form-control " placeholder="ค้นหา" id="Search_rice">
                    </div>
                    <div class="col-md-3  mt-2 ">
                    <button type="button" class="btn btn-primary btn-lg" onclick="ShowSearch_rice()">
                    <i class="icon-search3"></i> ค้นหา </button>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <!-- SHOW USER -->
                                    <table class="table table-striped table-hover r-0" id="TableSearch_rice">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>เลขที่เอกสาร</th>
                                            <th>วันที่เอกสาร</th>
                                            <th>วันที่บันทึก</th>
                                            <th>ผู้บันทึก</th>
                                            <th>สถานะ</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbody">                                    
                                        </tbody>
                                    </table>
                                    <!-- =============== -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- END BUYERS -->
        </div>
    </div>
    <!--Add New Message Fab Button-->
    <a hidden  href="panel-page-users-create.html" class="btn-fab btn-fab-md fab-right fab-right-bottom-fixed shadow btn-primary"><i
            class="icon-add"></i></a>
</div>
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg shadow white fixed"></div>
</div>



<div class="modal fade" id="showdetaildraw" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">รายละเอียดการขอเบิก</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table table-striped table-hover r-0" id="Tabledetail">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>ชื่อรายการ</th>
                                            <th>วันที่หมดอายุ</th>
                                            <th>จำนวนคงเหลือ(กก)</th>
                                            <th>จำนวนขอเบิก(กก)</th>
                                            <th>จำนวนที่ให้(กก)</th>
                                            <th>หน่วยนับ</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbody">
                                        </tbody>
                                    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="approve(8)">ไม่อนุมัติ</button>
        <button type="button"  class="btn btn-success" onclick="approve(2)">อนุมัติ</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="showdetaildraw_rice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">รายละเอียดการขอเบิก</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table table-striped table-hover r-0" id="Tabledetail_rice">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>ชื่อรายการ55</th>
                                            <th>วันที่หมดอายุ</th>
                                            <th>จำนวนคงเหลือ(กก)</th>
                                            <th>จำนวนขอเบิก(กก)</th>
                                            <th>จำนวนที่ให้(กก)</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbody">
                                        </tbody>
                                    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="approve_rice(8)">ไม่อนุมัติ</button>
        <button type="button"  class="btn btn-success" onclick="approve_rice(2)">อนุมัติ</button>
      </div>
    </div>
  </div>
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