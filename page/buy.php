<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");
$PmID = $_SESSION['PmID'];
$Userid = $_SESSION['ID'];

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

    <title>บันทึกการซื้อ</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
      $(document).ready(function(e){

    });
    // Function 
    function Createdocument()
    {
        var userid = '<?php echo $Userid; ?>';
        swal({
          title: "",
          text: "ยืนยันการสร้างเอกสาร",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "ใช่",
          cancelButtonText: "ไม่ใช่",
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          closeOnCancel: false,
          showCancelButton: true}).then(result => {
            if (result.value) {
            var data = {
              'STATUS'    : 'CreateDocument',
              'userid'	: userid
            };
            senddata(JSON.stringify(data));
          } else if (result.dismiss === 'cancel') {
            swal.close();
          } 
          })

    }
    function Additem()
    {
        var DocNo = $("#DocNo").val();

        if(DocNo != "")
        {
            $('#Additem').modal('show');

            ShowItem();
        }

    }
    function ShowItem()
    {
        var data = 
        {
            'STATUS'  : 'ShowItem'
        };
        senddata(JSON.stringify(data));
    }
    function Sumitem(grade , rowid)
    {
        var Kilo =  parseFloat($("#Kilo_"+rowid).val());
        var SUM =parseFloat( Kilo * grade );
        if(isNaN(SUM) )
        {
            SUM = 0;
        }
        $("#Total_"+rowid).val(SUM);
    }
    function Importdata()
    {
        var DocNo = $("#DocNo").val();
        /* declare an checkbox array */
        var iArray = [];
        var kiloArray = [];
        var totalArray = [];
        var item_codeArray = [];
        
        $(".checkitem:checked").each(function() 
        {
            iArray.push($(this).val());
        });
        // =======================================================
        for(var j=0;j<iArray.length; j++)
        {
            item_codeArray.push( $("#item_code_"+iArray[j]).val() );
            kiloArray.push( $("#Kilo_"+iArray[j]).val() );
            totalArray.push( $("#Total_"+iArray[j]).val() );
        }
        // =======================================================
        var item_code = item_codeArray.join(',') ;
        var kilo = kiloArray.join(',') ;
        var total = totalArray.join(',') ;
        // =======================================================
        $( "#TableDetail tbody" ).empty();
        var data = 
        {
          'STATUS'  	: 'Importdata',
          'item_code'   : item_code,
          'kilo'		: kilo,
          'total'	  	: total,
          'DocNo'		: DocNo
        };
        senddata(JSON.stringify(data));

    }
    function ShowDetail() 
    {
        var DocNo = $("#DocNo").val();
        var data = {
            'STATUS'  : 'ShowDetail',
            'DocNo'   : DocNo
        };
        senddata(JSON.stringify(data));
    }
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/buy.php';
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
                    if(temp["form"]=='CreateDocument')
                    {
                        swal({
                            title: "สร้างเอกสาร",
                            text: temp[0]['DocNo'] + "สำเร็จ",
                            type: "success",
                            showCancelButton: false,
                            timer: 1000,
                            confirmButtonText: 'Ok',
                            showConfirmButton: false
                            });
                        $("#DocNo").val(temp[0]['DocNo']);
                        $("#docdate").val(temp[0]['DocDate']);
                        $("#Employee").val(temp[0]['Record']);
                        $("#ModifyDate").val(temp[0]['RecNow']);
                        
                        $("#ModifyDate").attr('disabled' , true );
                        $("#docdate").attr('disabled' , true );
                        $("#Employee").attr('disabled' , true );
                        $("#DocNo").attr('disabled' , true );


                    }
                    else if(temp["form"]=='ShowItem')
                    {
                        $( "#Tableitem tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                  var chkinput = "<div class='custom-control custom-checkbox'><input type='checkbox' class='custom-control-input checkSingle checkitem'  value='"+i+"'  id= ' item_id_"+i+" ' required><label class='custom-control-label ' for=' item_id_"+i+" ' style='margin-top: 15px;'></label></div> <input type='hidden' id='item_code_"+i+"' value='"+temp[i]['item_code']+"'>";
                                  var Kilo = "<input type='text' id='Kilo_"+i+"' class='form-control ' autocomplete='off'  placeholder='0.00' onkeyup='Sumitem(\""+temp[i]['Grade']+"\" , \""+i+"\" ) '>  ";
                                  var Total = "<input type='text' id='Total_"+i+"' class='form-control ' autocomplete='off'  value='0.00' disabled>  ";


                                 StrTR = "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td style=' width: 20%; '>"+temp[i]['item_name']+"</td>"+
                                                "<td style=' width: 25%; ' >"+temp[i]['Grade']+"</td>"+
                                                "<td >"+Kilo+"</td>"+
                                                "<td >"+Total+"</td>"+
                                                "</tr>";
   
                                   $('#Tableitem tbody').append( StrTR );
                              }
                    }
                    else if(temp["form"]=='ShowDetail')
                    {
                        $( "#TableDetail tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                  var chkinput = "<div class='custom-control custom-checkbox'><input type='checkbox' class='custom-control-input checkSingle checkdetail'  value='"+i+"'  id= ' Detail_id_"+i+" ' required><label class='custom-control-label ' for=' Detail_id_"+i+" ' style='margin-top: 15px;'></label></div> <input type='hidden' id='Detail_item_code_"+i+"' value='"+temp[i]['item_code']+"'>";
                                  var Kilo = "<input type='text' id='Detail_Kilo_"+i+"' class='form-control ' autocomplete='off'  placeholder='0.00' value='"+temp[i]['kilo']+"' style='width: 40%;'>  ";
                                  var Total = "<input type='text' id='Detail_Total_"+i+"' class='form-control ' autocomplete='off'  value='"+temp[i]['total']+"' disabled style='width: 40%;'>  ";

                                   StrTR =   "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td style=' width: 20%; '>"+temp[i]['item_name']+"</td>"+
                                                "<td style=' width: 25%; ' >"+temp[i]['Grade']+"</td>"+
                                                "<td >"+Kilo+"</td>"+
                                                "<td >"+Total+"</td>"+
                                                "</tr>";
   
                                   $('#TableDetail tbody').append( StrTR );
                              }
                    }
                }
                else if (temp['status']=="failed") 
                {
                    switch (temp['msg']) 
                    {
                    case "notchosen":
                                temp['msg'] = "<?php echo $array['choosemsg'][$language]; ?>";
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
        body{
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
        .boxshadowx button{
            box-shadow: none  !important;
        }
        .boxshadowx button:hover{
            color: #bcaaa4 !important;
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
                        <i class="icon icon-folder5"></i>
                        บันทึกการซื้อลำไย
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all"
                           role="tab" aria-controls="v-pills-all"><i class="icon icon-home2"></i>การซื้อลำไย</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                           aria-controls="v-pills-buyers"><i class="icon icon-face"></i> ค้นหา </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
          <div class="tab-pane animated fadeInUpShort show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
            <div class="row">
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >เลขที่เอกสาร</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="DocNo"  placeholder="เลขที่เอกสาร" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >วันที่เอกสาร</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="docdate"  placeholder="วันที่เอกสาร" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label h4" >วันที่บันทึก</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control "  id="ModifyDate"   placeholder="วันที่บันทึก" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >ผู้บันทึก</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="Employee"   placeholder="ผู้บันทึก" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >ลูกค้า</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="Customer"   placeholder="ลูกค้า" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label h4" >จำนวนเงินทั้งหมด</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="Total"  placeholder="จำนวนเงินทั้งหมด" >
                    </div>
                </div>
            </div>

            <div class="row box  col-md-12 my-3 d-flex justify-content-end">

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn "   onclick="Createdocument();">
                                    <i class="icon-document-add2 blue accent-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">สร้างเอกสาร</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn "  onclick="Additem();">
                                    <i class="icon-add_circle blue lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">เพิ่มรายการ</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn " >
                                    <i class="icon-delete  red lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">ลบรายการ</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn " >
                                    <i class="icon-save2 green lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">บันทึก</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn " >
                                    <i class="icon-document-cancel2 red lighten-1 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">ยกเลิกเอกสาร</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx">
                            <button type="button" class="btn " >
                                    <i class="icon-print orange lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">พิมพ์รายงาน</div>
                            </button>
                            </div>

                        </div>

                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <!-- SHOW DETAIL -->
                                    <table class="table table-striped table-hover r-0" id="TableDetail">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>NO.</th>
                                            <th>ชื่อรายการ</th>
                                            <th>ราคาต่อหน่วย</th>
                                            <th>กิโล</th>
                                            <th>ราคารวม</th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >                                    
                                        </tbody>
                                    </table>
                                    <!-- =============== -->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- START BUYERS -->
            <div class="tab-pane animated fadeInUpShort" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
                <div class="row">

                    <div class="col-md-3 my-3">
                        <div class="card no-b">
                            <div class="card-body text-center p-5">
                                <div class="avatar avatar-xl mb-3">
                                    <img  src="assets/img/dummy/u11.png" alt="User Image">
                                </div>
                                <div>
                                    <h6 class="p-t-10">Alexander Pierce</h6>
                                    alexander@paper.com
                                </div>
                                <a href="#" class="btn btn-success btn-sm mt-3">View Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 my-3">
                        <div class="card no-b">
                            <div class="card-body text-center p-5">
                                <div class="avatar avatar-xl mb-3">
                                    <img  src="assets/img/dummy/u12.png" alt="User Image">
                                </div>
                                <div>
                                    <h6 class="p-t-10">Alexander Pierce</h6>
                                    alexander@paper.com
                                </div>
                                <a href="#" class="btn btn-success btn-sm mt-3">View Profile</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- END BUYERS -->
        </div>
    </div>
    <!--Add New Message Fab Button-->
    <a hidden  href="panel-page-users-create.html" class="btn-fab btn-fab-md fab-right fab-right-bottom-fixed shadow btn-primary"><i class="icon-add"></i></a>
</div>
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg shadow white fixed"></div>
</div>

<!--------------------------------------- Modal add_customer  ------------------------------------------>
<div class="modal fade" id="Additem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">เพิ่ม รายการ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table table-striped table-hover r-0" id="Tableitem">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th></th>
                                            <th>ชื่อรายการ</th>
                                            <th>ราคาต่อหน่วย</th>
                                            <th>กิโล</th>
                                            <th>ราคารวม</th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >

                                        <tr hidden >
                                            <td >
                                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkSingle" id="user_id" required><label class="custom-control-label" for="user_id" style="margin-top: 25%;"></label></div>
                                            </td>
                                            <td>
                                                  <strong>ลำไย เกรด AA</strong>
                                            </td>
                                            <td>
                                                24
                                            </td>
                                            <td>
                                                256
                                            </td>
                                            <td>
                                                5000
                                            </td>
                                        </tr>


                                        <tr hidden >
                                            <td >
                                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkSingle" id="user_id" required><label class="custom-control-label" for="user_id" style="margin-top: 25%;"></label></div>
                                            </td>
                                            <td>
                                                  <strong>ลำไย เกรด A</strong>
                                            </td>
                                            <td>
                                                24
                                            </td>
                                            <td>
                                                256
                                            </td>
                                            <td>
                                                5000
                                            </td>
                                        </tr>


                                        <tr hidden >
                                            <td >
                                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkSingle" id="user_id" required><label class="custom-control-label" for="user_id" style="margin-top: 25%;"></label></div>
                                            </td>
                                            <td>
                                                  <strong>ลำไย เกรด B</strong>
                                            </td>
                                            <td>
                                                24
                                            </td>
                                            <td>
                                                256
                                            </td>
                                            <td>
                                                5000
                                            </td>
                                        </tr>


                                        <tr hidden>
                                            <td >
                                            <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkSingle" id="user_id" required><label class="custom-control-label" for="user_id" style="margin-top: 25%;"></label></div>
                                            </td>
                                            <td>
                                                  <strong>ลำไย เกรด C</strong>
                                            </td>
                                            <td>
                                                24
                                            </td>
                                            <td>
                                                256
                                            </td>
                                            <td>
                                                5000
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button"  class="btn btn-success" onclick="Importdata()">ยืนยัน</button>
      </div>
    </div>
  </div>
</div>
<!-------------------------- end add_customer Modal ----------------------------------------------->
<!--/#app -->
<script src="assets/js/app.js"></script>




<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>