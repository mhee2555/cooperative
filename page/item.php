<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");
$FName = $_SESSION['FName'];
$PmID = $_SESSION['PmID'];
$Permission = $_SESSION['Permission'];
$Profile = $_SESSION['pic']==null?'default_img.png':$_SESSION['pic'];

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

    <title>รายการ</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
      $(document).ready(function(e){
        Showitem();
        // ค้นหา
        $("#Search").on("keyup", function() 
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
        var item_type = $('#item_type').val();

        var data = 
        {
            'STATUS': 'Showitem',
            'item_type':item_type
        };
        senddata(JSON.stringify(data));
    }

    function showmodal(ID,sel)
    {
        var data = 
        {
            'STATUS': 'show_detail_item',
            'ID':ID,
            'sel':sel
        };
        senddata(JSON.stringify(data));
    } 
    
    function edit_item()
    {
        var ID = $('#ID_edit').val();
        var item_name_edit = $('#item_name_edit').val();
        var item_type_edit = $('#item_type_edit').val();
        
        if(item_name_edit=='' || item_type_edit=='' ){
                swal({
                          title: '',
                          text: 'กรุณากรอกข้อมูลให้ครบ',
                          type: 'info',
                          showCancelButton: false,
                          showConfirmButton: false,
                          timer: 1500,
                          confirmButtonText: 'Ok'
                    }); 
        }else{
                var data = 
                    {
                    'STATUS': 'edit_item',
                    'ID':ID,
                    'item_name_edit':item_name_edit,
                    'item_type_edit':item_type_edit
                    };
                senddata(JSON.stringify(data));

        }
       
    }
    function delete_item(ID)
    {
        swal({
          title: "",
          text: "ต้องการลบ รายการหรือไม่",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "ตกลง",
          cancelButtonText: "ยกเลิก",
          closeOnConfirm: false,
          closeOnCancel: false,
          showCancelButton: true
        }).then(result => {
          if (result.value) {
            var data = 
                    {
                    'STATUS': 'delete_item',
                    'ID':ID
                    };
                senddata(JSON.stringify(data));
          } else if (result.dismiss === 'cancel') {
            swal.close();
          }
        })
               
    }
    function show_add_modal()
    {            
            var item_name_add                = $('#item_name_add').val();
            var item_type_add                  = $('#item_type_add').val();

            if(item_name_add=='' || item_type_add=='' ){
                swal({
                          title: '',
                          text: 'กรุณากรอกข้อมูลให้ครบ',
                          type: 'info',
                          showCancelButton: false,
                          showConfirmButton: false,
                          timer: 1500,
                          confirmButtonText: 'Ok'
                    }); 
            }else{

                var data = 
                    {
                    'STATUS': 'add_item',
                    'item_name_add':item_name_add,
                    'item_type_add':item_type_add
                    };

                $('#add_item').modal('toggle');

                setTimeout(() => {
                    senddata(JSON.stringify(data));
                }, 1000);
            }
    }
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/item.php';
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
                                  var ShowEdit = "<a href='javascript:void(0)'  onclick='showmodal("+temp[i]['item_code']+","+'2'+");'><i class='icon-pencil'></i></a> <a href='javascript:void(0)' onclick='delete_item("+temp[i]['item_code']+");' style='margin-left:5%;'><i class='icon-delete_forever'></i></a>";

                                 StrTR = "<tr ondblclick='showmodal("+temp[i]['item_code']+","+'1'+");'>"+
                                                "<td >"+(i+1)+"</td>"+
                                                "<td >"+temp[i]['item_name']+"</td>"+
                                                "<td ></td>"+
                                                "<td ></td>"+
                                                "<td ></td>"+
                                                "<td ></td>"+
                                                "<td ></td>"+
                                                "<td >"+ShowEdit+"</td>"+
                                                "</tr>";
   
                                   $('#Tableitem tbody').append( StrTR );
                              }
                    }
                    else if( (temp["form"]=='show_detail_item') )
                    {
                        var sel = temp['sel'];
                        if(sel==1){
                            $('#show_item').modal('toggle');
                            $('#ID').val(temp['item_code']);
                            $('#item_name_show').val(temp['item_name']);
                            $('#item_type_show').val(temp['type_name']);
                        }else{
                            $('#show_item_edit').modal('toggle');
                            $('#ID_edit').val(temp['item_code']);
                            $('#item_name_edit').val(temp['item_name']);
                            $('#item_type_edit').val(temp['item_type']);
                        }
                        
                    }
                    else if( (temp["form"]=='edit_item') )
                    {
                        swal({
                            title: '',
                            text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                            type: 'success',
                            showCancelButton: false,
                            timer: 1000,
                            confirmButtonText: 'Ok',
                            showConfirmButton: false
                        });
                        $('#show_item_edit').modal('hide');

                        setTimeout(function() {
                            Showitem();
                        }, 1000);
                    }
                    else if( (temp["form"]=='delete_item') )
                    {
                        swal({
                            title: '',
                            text: 'ลบข้อมูล รายการเรียบร้อย',
                            type: 'success',
                            showCancelButton: false,
                            timer: 1000,
                            confirmButtonText: 'Ok',
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            Showitem();
                        }, 1000);
                        
                    }
                    else if( (temp["form"]=='add_item') )
                    {
                        swal({
                            title: '',
                            text: 'บันทึกข้อมูลเรียบร้อยแล้ว',
                            type: 'success',
                            showCancelButton: false,
                            timer: 1000,
                            confirmButtonText: 'Ok',
                            showConfirmButton: false
                        });
                        // CLEAR ALL
                        $('#item_name_add').val('');
                        $('#item_type_add').val('1');
                        // 

                        setTimeout(function() {
                            Showitem();
                        }, 1000);
                        
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
                        รายการ
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all"
                           role="tab" aria-controls="v-pills-all"><i class="icon icon-home2"></i>รายการ</a>
                    </li>
                    <!-- <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                           aria-controls="v-pills-buyers"><i class="icon icon-face"></i> Buyers</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-sellers-tab" data-toggle="pill" href="#v-pills-sellers" role="tab"
                           aria-controls="v-pills-sellers"><i class="icon  icon-local_shipping"></i> Sellers</a>
                    </li>
                    <li class="float-right">
                        <a class="nav-link"  href="panel-page-users-create.html" ><i class="icon icon-plus-circle"></i> Add New User</a>
                    </li> -->
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
            <div class="row">
                <div class="col-md-3 mt-2 ">
                    <select class =  " custom-select  "  id="item_type" onchange="Showitem()">
                        <option value="0">ค้นหาตามประเภท</option>
                        <option value="1">ข้าว</option>
                        <option value="2">ลำไย</option>
                        <option value="3">ข้าวแปรรูป</option>
                        <option value="4">ลำไยแปรรูป</option>
                    </select>
                </div>
                <div class="col-md-3 mt-2 ">
                    <input type="text" class =  "form-control " placeholder="ค้นหาจากชื่อรายการ" id="Search">
                </div>
                <div class="col-md-3  mt-2 ">
                <button type="button" class="btn btn-primary btn-lg" onclick="Showitem()"><i class="icon-search3"></i>ค้นหา</button>
                    <button type="button"  data-toggle="modal" data-target="#add_item"  class="btn btn-success btn-lg ml-3" ><i class="icon-person_add"></i>&nbsp;เพิ่ม&nbsp;</button>
                </div>
            </div>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="table-responsive">
                                <form>
                                    <!-- SHOW USER -->
                                    <table class="table table-striped table-hover r-0" id="Tableitem">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>NO.</th>
                                            <th>NAME</th>
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

                <!-- <nav class="my-3" aria-label="Page navigation">
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
                </nav> -->
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

            <div class="tab-pane animated fadeInUpShort" id="v-pills-sellers" role="tabpanel" aria-labelledby="v-pills-sellers-tab">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <div class="card no-b p-3">
                            <div>
                                <div class="image mr-3 avatar-lg float-left">
                                    <span class="avatar-letter avatar-letter-a avatar-lg  circle"></span>
                                </div>
                                <div class="mt-1">
                                    <div>
                                        <strong>Alexander Pierce</strong>
                                    </div>
                                    <small> alexander@paper.com</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card no-b p-3">
                            <div>
                                <div class="image mr-3 avatar-lg float-left">
                                    <span class="avatar-letter avatar-letter-c avatar-lg  circle"></span>
                                </div>
                                <div class="mt-1">
                                    <div>
                                        <strong>Clexander Pierce</strong>
                                    </div>
                                    <small>clexander@paper.com</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
<!--------------------------------------- Modal show_customer ------------------------------------------>
<div class="modal fade" id="show_item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">ข้อมูล รายการ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ID Code</label>
            <input type="text" id="ID" class="form-control " placeholder="ID">
            </div>
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">รายการ</label>
            <input type="text" id="item_name_show" class="form-control " placeholder="ชื่อรายการ">
            </div>
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ประเภท</label>
            <input type="text" id="item_type_show" class="form-control " placeholder="Type">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success " data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
<!-------------------------- end Modal ----------------------------------------------->
<!--------------------------------------- Modal show_customer edit ------------------------------------------>
<div class="modal fade" id="show_item_edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">แก้ไข รายการ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ID Code</label>
            <input type="text" id="ID_edit" class="form-control " placeholder="ID" disabled>
            </div>
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ชื่อรายการ *</label>
            <input type="text" id="item_name_edit" class="form-control " placeholder="ชื่อรายการ">
            </div>
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ประเภท</label>
                <select class =  " custom-select  " id="item_type_edit">
                            <option value="1">ข้าว</option>
                            <option value="2">ลำไย</option>
                            <option value="3">ข้าวแปรรูป</option>
                            <option value="4">ลำไยแปรรูป</option>
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick='edit_item();' class="btn btn-success">Save</button>
      </div>
    </div>
  </div>
</div>
<!-------------------------- end Modal ----------------------------------------------->
<!--------------------------------------- Modal add_customer  ------------------------------------------>
<div class="modal fade" id="add_item" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">เพิ่ม รายการ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ชื่อราการ *</label>
            <input type="text" id="item_name_add" class="form-control " placeholder="ชื่อราการ">
            </div>
            <div class="margin_input">
            <label class="form-label mr-1" style="color:#000000;">ประเภท</label>
                <select class =  " custom-select  " id="item_type_add">
                            <option value="1">ข้าว</option>
                            <option value="2">ลำไย</option>
                            <option value="3">ข้าวแปรรูป</option>
                            <option value="4">ลำไยแปรรูป</option>
                </select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button"onclick="show_add_modal()" class="btn btn-success">Save</button>
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