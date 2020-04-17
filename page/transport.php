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
    <link href="../assets-sign/css/jquery.signature.css" rel="stylesheet">
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
          $("#v-pills-buyers").attr("hidden" , true);
          var d = new Date();
          var month = d.getMonth()+1;
          var day = d.getDate();
          var output = d.getFullYear() + '-' +
              ((''+month).length<2 ? '0' : '') + month + '-' +
              ((''+day).length<2 ? '0' : '') + day;
          $("#Search").val(output);
        });

        function showchartbuy()
        {
            var data = 
                {
                    'STATUS'      : 'showchartbuy'
                };
                senddata(JSON.stringify(data));
        }

        function ShowDoc()
        {
          var date = $('#Search').val();
          var type = $('#type').val();

          var data = 
                {
                    'STATUS': 'ShowDoc',
                    'date':date,
                    'type':type
                };
                senddata(JSON.stringify(data));


        }

        function show_process(DocNo ,Fname)
        {
          $("#v-pills-buyers").attr("hidden" , false);
          $('#v-pills-buyers-tab').tab('show');
          $("#S_End_btn").hide();
          $('#DocNo').text(DocNo);
          $('#Fname').text(Fname);
          // $("#S_Start").text("--:--:--");
					// $("#S_End").text("--:--:--");

          var data = 
                {
                    'STATUS': 'show_process',
                    'DocNo':DocNo
                };
                senddata(JSON.stringify(data));
        }

        function md_send(fnc)
        {
          SignFnc = fnc;
          if (SignFnc == 'start_send')
          {
              $("#ModalSign").modal('show');
          }
          else
          {
            $("#ModalSign_end").modal('show');
          }
        }

        function view_detail()
        {
          DocNo =  $('#DocNo').text();
          var data = {
            'DocNo': DocNo,
            'STATUS': 'view_detail'
          };
          senddata(JSON.stringify(data));
        }


        function end_send() {
          DocNo =  $('#DocNo').text();
        var data = {
          'DocNo': DocNo,
          'STATUS': 'end_send'
        };
        senddata(JSON.stringify(data));
		}
        // END FUNCTION

        function senddata(data)
        {
            var form_data = new FormData();
            form_data.append("DATA",data);
            var URL = '../process/transport.php';
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
                        if(temp["form"]=='ShowDoc')
                        {
                          $("#document").empty();
                          for (var i = 0; i < temp['count']; i++)
                          {
                            var status_class = "";
                            var status_text = "";

                            if (temp[i]['IsStatus'] == 0) {
                              status_class = "status2";
                              status_text = "หยุดชั่วขณะ";
                            } else if (temp[i]['IsStatus'] == 1 ) {
                              status_class = "status4";
                              status_text = "ไม่ทำงาน";
                            } else if (temp[i]['IsStatus'] == 2 ) {
                              status_class = "status1";
                              status_text = "กำลังส่ง";
                            } else if (temp[i]['IsStatus'] == 3) {
                              status_class = "status3";
                              status_text = "เสร็จสิ้น";
                            }

                            var Str = "<button onclick='show_process(\"" + temp[i]['DocNo'] + "\" , \"" + temp[i]['FName'] + "\" )' class='btn btn-light btn-block mt-3' ><div class='row'><div class='col-5 d-flex justify-content-end '>";
                            Str += "<div class='row'><div class='card " + status_class + "'>" + status_text + "</div>";
                            Str += "</div></div><div class='col-7 text-left'>";
                            Str += "<div class='text-truncate font-weight-bold'>" + temp[i]['DocNo'] + "</div><div class='font-weight-light'>" + temp[i]['FName'] + "</div></div></div></button>";

                            $("#document").append(Str);

                          }
                            
                        }
                        else if(temp["form"]=='show_process')
                        {
                          if ( (temp['IsStatus'] == 1 || temp['IsStatus'] == 2) && temp['DvEndTime'] == null)
                          { //-----กำลังขนส่ง
                            $("#S_Status").attr("src", "../img/Status_1.png");
                            $("#S_Status_text").text("Wait Process");
                            $("#S_Use_text").hide();
                            $("#S_Sum_btn").show();
                            $("#sign_zone_start").attr("hidden" , true);
                            $("#sign_zone").attr("hidden" , true);
                            
                            if (temp['DvStartTime'] == null)
                            { // ถ้ากดเริ่มครั้งแรก

                              $("#S_Status").attr("src", "../img/Status_4.png");
                              $("#S_Start_btn").show();
                              $("#S_Sum_btn").show();
                              $("#S_End_btn").hide();
                              $("#S_Start").text("--:--:--");
                              $("#S_End").text("--:--:--");
                            }
                            else if (temp['DvStartTime'] != null)
                            { // ถ้าเคยกดเริ่มแล้ว
                              $("#S_Start_btn").hide();
                              $("#S_End_btn").show();

                              var S_Start = new Date(temp['DvStartTime']);
                                $("#S_Start").text(S_Start.toLocaleTimeString());
                                $("#S_End").text("--:--:--");

                              var ck_start = temp['signStart'];
                                $("#show_sign_start").html(ck_start);
                                $("#sign_zone_start").removeAttr("hidden");
                            }
                          }

                          else if (temp['IsStatus'] == 3 && temp['DvEndTime'] != null)
                          { //-----เสร็จสิ้น
                            var ck_start = temp['signStart'];
                            var ck_end = temp['signEnd'];

                              if (temp['signEnd'] == null || temp['signEnd'] == "")
                              {
                                $("#show_sign_start").html(ck_start);
                                $("#sign_zone_start").removeAttr("hidden");
                                md_send('end_send');
                                
                              }
                              else
                              {
                                $("#show_sign_start").html(ck_start);
                                $("#sign_zone_start").removeAttr("hidden");

                                $("#show_sign").html(ck_end);
                                $("#sign_zone").removeAttr("hidden");
                              }
                            
                            
                         
                            $("#S_Sum_btn").hide();
                            $("#S_Status").attr("src", "../img/Status_3.png");
                            $("#S_Status_text").text("Success Process");
                            $("#S_Start_text").removeClass("col-lg-6");
                            $("#S_End_text").removeClass("col-lg-6");
                            $("#S_Start_text").addClass("col-lg-4");
                            $("#S_End_text").addClass("col-lg-4");
                            $("#S_Use_text").show();

                            var S_Start = new Date(temp['DvStartTime']);
                            var S_End = new Date(temp['DvEndTime']);
                            var S_Over = temp['DvUseTime'].substring(0, 1);

                            if (S_Over == '-')
                            {
                              $("#S_Head_use").text("เกินเวลา");
                              $("#S_Head_use").css("color", "red");
                              $("#S_Use").css("color", "red");
                              $("#S_Use").text(temp['DvUseTime'].substring(1) + " นาที");

                            }
                            else
                            {
                              $("#S_Head_use").text("ใช้เวลา");
                              $("#S_Use").text(temp['DvUseTime'] + " นาที");
                            }

                            $("#S_Start").text(S_Start.toLocaleTimeString());
                            $("#S_End").text(S_End.toLocaleTimeString());

                          }
                        }

                        else if (temp["form"] == 'end_send')
                        {
                          var DocNo = $('#DocNo').text();
                          var Fname = $('#Fname').text();
                          show_process(DocNo , Fname);
                        } 
                        else if (temp["form"] == 'view_detail')
                        {
                          $("#lg_body").empty();

                            var Str = "<table class='table table-bordered table-sm'>";
                            Str += "			<thead>";
                            Str += "				<tr>";
                            Str += "					<th>No.</th>";
                            Str += "					<th>รายการ</th>";
                            Str += "					<th>จำนวน</th>";
                            Str += "				</tr>";
                            Str += "			</thead>";
                            Str += "			<tbody>";

                            for (var i = 0; i < temp['cnt']; i++)
                            {
                              Str += "					<tr>";
                              Str += "						<th>" + Number(i+1) + "</th>";
                              Str += "						<td class='text-left pl-3'>" + temp[i]['item_name'] + "</td>";
                              Str += "						<td>" + temp[i]['kilo'] + "</td>";
                              Str += "					</tr>";
                            }

                            Str += "				</tbody>";
                            Str += "			</table>";

                            $("#lg_body").append(Str);
                            $("#md_lg").modal('show');
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
        .status1 { /* กำลังทำงาน (สีน้ำเงิน) */
          align-items: center!important;
          width: 115px!important;
          border: 1px solid #1E70AD!important;
          background-color: #1E70AD!important;
          color: #fff!important;
          padding: .5rem!important;
          margin-right: 8px!important
      }

      .status2 { /* หยุดชั่วขณะ (สีเหลือง) */
          align-items: center!important;
          width: 115px!important;
          border: 1px solid #F7EDC6!important;
          background-color: #F7EDC6!important;
          color: #000!important;
          padding: .5rem!important;
          margin-right: 8px!important
      }

      .status3 { /* เสร็จสิ้น (สีเขียว) */
          align-items: center!important;
          width: 115px!important;
          border: 1px solid #94CE93!important;
          background-color: #94CE93!important;
          color: #000!important;
          padding: .5rem!important;
          margin-right: 8px!important
      }

      .status4 { /* ไม่ทำงาน (สีเทา) */
          align-items: center!important;
          width: 115px!important;
          border: 1px solid #C1BFC1!important;
          background-color: #C1BFC1!important;
          color: #000!important;
          padding: .5rem!important;
          margin-right: 8px!important
      }
      .kbw-signature { width: 100%; height: 240px; }

       #ModalSign{
        top: 0% !important;
       }
       svg
       {
         width : 10cm !important;
         helght : 8cm !important;
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
                        ขนส่ง
                    </h4>
                </div>
            </div>
            <div class="row">
                    <ul class="nav responsive-tab nav-material nav-material-white">
                            <li>
                                <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all"
                              role="tab" aria-controls="v-pills-all"><i class="icon icon-home2"></i>ขนส่ง</a>
                            </li>
                            <li>
                                 <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                                  aria-controls="v-pills-buyers"><i class="icon icon-face"></i>รายละเอียด</a>
                            </li>
                        </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row">
                    <div class="col-md-6 mt-2 ">
                        <select class =  " custom-select  "  id="type" onchange="getUser()">
                            <option value="1">ลำไย</option>
                            <option value="2">ข้าว</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-2 ">
                      <input type="text" class ="form-control datepicker-here"  placeholder="ค้นหาจากวันที่" id="Search" data-language='en' data-date-format='yyyy-mm-dd'>
                    </div>   
                </div>

                <div class="d-flex justify-content-center mt-2">
                        <button type="button" class="btn btn-primary btn-lg" onclick="ShowDoc()" style="width: 250px;"><i class="icon-search3"></i>ค้นหา</button>
                </div>

                <div id="document">
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
          
              <div class="text-center text-truncate font-weight-bold my-4" id='DocNo' style="font-size:20px;"></div>
              <div class="text-center text-truncate font-weight-bold my-4" id='Fname' style="font-size:20px;"></div>

              <div id="process">
                  <div class="card alert alert-info mx-3 mt-4" style="padding:1rem;">

                    <div class="row">
                      <div class="col-4 align-self-center">
                        <div class="row">
                          <div class="col-md-6 col-sm-none"></div>
                          <div class="col-md-6 col-sm-12 text-center"><img src="../img/icon_3.png" height="90px" /></div>
                          <div class="col-md-6 col-sm-none"></div>
                          <div class="col-md-6 col-sm-12 text-center">ขนส่ง</div>
                        </div>
                      </div>

                      <div class="col-4 text-left align-self-center text-center">
                        <div class="row">
                          <div id="S_Start_text" class="col-lg-6 col-md-12 col-sm-12">
                            <div class="head_text">เวลาที่เริ่ม</div>
                            <label id="S_Start" class='font-weight-light'></label>
                          </div>
                          <div id="S_End_text" class="col-lg-6 col-md-12 col-sm-12">
                            <div class="head_text">เวลาสิ้นสุด</div>
                            <label id="S_End" class='font-weight-light'></label>
                          </div>
                          <div id="S_Use_text" class="col-lg-4 col-md-12 col-sm-12">
                            <div id="S_Head_use" class="head_text"></div>
                            <label id="S_Use" class='font-weight-light'></label>
                          </div>
                        </div>
                      </div>

                      <div class="col-4 align-self-center">
                        <div class="row">
                          <div class="col-md-6 col-sm-12 text-center"><img id="S_Status" height="40px" /></div>
                          <div class="col-md-6 col-sm-none"></div>
                          <div id="S_Status_text" class="col-md-6 col-sm-12 text-center"></div>
                          <div class="col-md-6 col-sm-none"></div>
                        </div>
                      </div>

                    </div>

                    <div id="S_Sum_btn" class="row mt-4">
                      <div class="col-md-2 col-sm-none"></div>
                      <div class="col-md-8 col-sm-12" id="S_Start_btn"><button onclick="md_send('start_send')" type="button" class="btn btn-lg btn-primary btn-block">เริ่มขนส่ง</button></div>
                      <div class="col-md-8 col-sm-12" id="S_End_btn"><button onclick="end_send()" type="button" class="btn btn-lg btn-success btn-block">เสร็จสิ้น</button></div>
                      <div class="col-md-2 col-sm-none"></div>
                    </div>

                  </div>

                  <div class="mx-3 mb-3 text-center">
                    <div class="col-md-8 col-sm-12 mx-auto my-4">
                      <button onclick="view_detail()" class="btn btn-block btn-info">รายละเอียด</button>
                    </div>

                    <div id="sign_zone_start" class="text-center" hidden>
                      <div><b>ลายเซนต์ผู้ส่ง</b></div>
                      <div class="row justify-content-center">
                        <div class="card mb-2 p-2">
                          <div id="show_sign_start" style="height : 300px"></div>
                        </div>
                      </div>
                    </div>
                  </div>

                <div id="sign_zone" class="mx-3 mb-3" hidden>
                  <div class="text-center">
                    <div><b>ลายเซนต์ผู้รับ</b></div>
                    <div class="row justify-content-center">
                      <div class="card mb-2 p-2">
                        <div id="show_sign" style="height : 300px"></div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>



            </div>
        </div>

      </div>



            <!-- /line graph -->
        </div>
    </div>

  
		<div class="modal fade" id="ModalSign" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background-color: rgba(64, 64, 64, 0.75)!important;">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="margin-top: 50px;background-color:#fff;">
      <div class="modal-header">
        <h4 class="modal-title">ลายเซ็นผู้ส่ง</h4>
      </div>
      <div class="modal-body">
        <div id="sig" class="kbw-signature">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" style="width:20%;" class="btn btn-success" id="svg">ยืนยัน</button>
        <button type="button" style="width:20%;" class="btn btn-danger" id="clear">ล้าง</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ModalSign_end" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background-color: rgba(64, 64, 64, 0.75)!important;">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="margin-top: 50px;background-color:#fff;">
      <div class="modal-header">
        <h4 class="modal-title">ลายเซ็นผู้รับ</h4>
      </div>
      <div class="modal-body">
        <div id="sig_end" class="kbw-signature">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" style="width:20%;" class="btn btn-success" id="svg_end">ยืนยัน</button>
        <button type="button" style="width:20%;" class="btn btn-danger" id="clear_end">ล้าง</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="md_lg" tabindex="-1" role="dialog" aria-hidden='false'>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div id="lg_body" class="modal-body text-center" style="max-height: calc(100vh - 210px);overflow-y: auto;">
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
<div class="control-sidebar-bg shadow white fixed"></div>
</div>
<!--/#app -->
<script src="assets/js/app.js"></script>
<script src="../assets-sign/js/jquery-ui.min.js"></script>
<script src="../assets-sign/js/jquery.signature.js"></script>



<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
<script>
  $(function() {
    var sig = $('#sig').signature();
    $('#clear').click(function() {
      sig.signature('clear');
    });
    $('#svg').click(function() {
      var SignSVG = sig.signature('toSVG');
      var DocNo = $('#DocNo').text();
      var Fname = $('#Fname').text();


      

      $('#chk_sign').val(0);
      $.ajax({
        url: '../process/UpdateSign.php',
        dataType: 'text',
        cache: false,
        data: {
          SignSVG:SignSVG,
          DocNo:DocNo,
          Table:"sale_longan",
          Column:"signStart"
        },
        type: 'post',
        success: function (data) {
          swal({
            title: '',
            text: 'บันทึกสำเร็จ',
            type: 'success',
            showCancelButton: false,
            showConfirmButton: false,
            timer: 1500,
          }).then((result) => {
            $('#sig').signature('clear'); ;
            $('#ModalSign').modal('toggle');
            show_process(DocNo , Fname);
            ShowDoc();
        });
      
        }
      });
    });
  });
  
</script>
<script>
  $(function() {
    var sig = $('#sig_end').signature();
    $('#clear_end').click(function() {
      sig.signature('clear');
    });
    $('#svg_end').click(function() {
      var SignSVG = sig.signature('toSVG');
      var DocNo = $('#DocNo').text();
      var Fname = $('#Fname').text();


      

      $('#chk_sign').val(0);
      $.ajax({
        url: '../process/UpdateSign.php',
        dataType: 'text',
        cache: false,
        data: {
          SignSVG:SignSVG,
          DocNo:DocNo,
          Table:"sale_longan",
          Column:"signEnd"
        },
        type: 'post',
        success: function (data) {
          swal({
            title: '',
            text: 'บันทึกสำเร็จ',
            type: 'success',
            showCancelButton: false,
            showConfirmButton: false,
            timer: 1500,
          }).then((result) => {
            $('#sig_end').signature('clear'); ;
            $('#ModalSign_end').modal('toggle');
            show_process(DocNo , Fname);
            ShowDoc();
        });
      
        }
      });
    });
  });
  
</script>
</body>
</html>