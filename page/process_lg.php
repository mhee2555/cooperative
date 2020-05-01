<?php
session_start();
require '../connect/connect.php';
date_default_timezone_set("Asia/Bangkok");
$PmID = $_SESSION['PmID'];
$Userid = $_SESSION['ID'];
$FName = $_SESSION['FName'];
$Permission = $_SESSION['Permission'];
$Profile = $_SESSION['pic']==null?'default_img.png':$_SESSION['pic'];

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
    <title>บันทึกการแปรรูปลำไย</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
    $(document).ready(function(e)
    {
         // ===========DATE ITEM =======
        var d = new Date();
        var month = d.getMonth()+1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
            ((''+month).length<2 ? '0' : '') + month + '-' +
            ((''+day).length<2 ? '0' : '') + day;
        $("#dateRefDocNo").val(output);

        // ค้นหา
        $("#Search").on("keyup", function() 
        {
            var value = $(this).val().toLowerCase();
            $("#TableSearch tbody tr").filter(function() 
            {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        // 





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
    function Startprocess()
    {
        var chkrow  =   $( ".chkrow" ).length;
        if(chkrow > 0)
        {
            var DocNo = $("#DocNo").val();
            var RefDocNo = $("#RefDocNo").val();
            if(RefDocNo == "")
            {
                swal({
                    title: '',
                    text: "กรุณาเลือกเอกสารขอเบิก",
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    showConfirmButton: false,
                    timer: 2000,
                    confirmButtonText: 'Ok'
                    })
            }
            else
            {
                swal({
                title: "",
                text: "ยืนยันการเรึ่มต้นแปรรูป",
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
                    if (result.value)
                    {
                        var data =
                        {
                            'STATUS'    : 'Startprocess',
                            'DocNo'     : DocNo
                        };
                        senddata(JSON.stringify(data));
                    }
                    else if (result.dismiss === 'cancel')
                    {
                        swal.close();
                    } 
                })
            }
        }
        else
        {
            swal({
                title: '',
                text: 'กรุณาเพิ่มรายการก่อนบันทึก',
                type: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                showConfirmButton: false,
                timer: 2000,
                confirmButtonText: 'Ok'
                })
        }

    }
    function Endprocess()
    {
        var DocNo = $("#DocNo").val();
        swal({
          title: "",
          text: "ยืนยันการสิ้นสุดแปรรูป",
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
            if (result.value)
            {
                var data =
                {
                    'STATUS'    : 'Endprocess',
                    'DocNo'     : DocNo
                };
                senddata(JSON.stringify(data));
            }
            else if (result.dismiss === 'cancel')
            {
                swal.close();
            } 
          })  
    }
    function Successprocess()
    {

        // =======================================================
        var ItemCodeArray = [];
        var KiloArray = [];
        var UnitArray = [];
        var stock_codeArray = [];
        
        $('input[name="detailrow"]').each(function() 
        {
          if($(this).val()!="")
          {
              ItemCodeArray.push($(this).val());
          }
        });
        var ItemCode = ItemCodeArray.join(',') ;
        // ========================================================
        $('input[name="KiloArray"]').each(function() 
        {
          if($(this).val()!="")
          {
            KiloArray.push($(this).val());
          }
        });
        var Kilo = KiloArray.join(',') ;
        // ========================================================
        $('select[name="UnitArray"]').each(function() 
        {
          if($(this).val()!="")
          {
            UnitArray.push($(this).val());
          }
        });
        var Unit = UnitArray.join(',') ;

        // ========================================================
        $('input[name="stock_code"]').each(function() 
        {
          if($(this).val()!="")
          {
            stock_codeArray.push($(this).val());
          }
        });
        var stock_code = stock_codeArray.join(',') ;

        // ========================================================

        var DocNo = $("#DocNo").val();
        swal({
          title: "",
          text: "ยืนยันการบันทึก",
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
            if (result.value)
            {
                var data =
                {
                    'STATUS'   : 'Successprocess',
                    'DocNo' : DocNo,
                    'ItemCode' : ItemCode,
                    'Kilo'     : Kilo,
                    'Unit'     : Unit,
                    'stock_code' : stock_code
                };
                senddata(JSON.stringify(data));
                $('#v-pills-buyers-tab').tab('show');
            }
            else if (result.dismiss === 'cancel')
            {
                swal.close();
            } 
          })    
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
    function ShowRefDocNo()
    {
        var dateRefDocNo = $("#dateRefDocNo").val();
        var chk_ref_status = $("#chk_ref_status").val();

        $('#ShowRefDocNo').modal('show');

      setTimeout(() =>
      {
          
        var data = 
                {
                    'STATUS'        : 'ShowRefDocNo' ,
                    'dateRefDocNo'  :  dateRefDocNo,
                    'chk_ref_status':  chk_ref_status
                };
                senddata(JSON.stringify(data));


      }, 500);

        
    }
    function SaveRefDocNo()
    {
      var RefDocNo = "";
      var DocNo = $("#DocNo").val();
      var chk_ref_status = $("#chk_ref_status").val();

        $('input[name="refrow"]:checked').each(function() 
        {
          RefDocNo = $(this).val();
        });
      var data = 
                {
                    'STATUS'      : 'SaveRefDocNo' ,
                    'RefDocNo'    : RefDocNo ,
                    'DocNo'       : DocNo ,
                    'chk_ref_status' : chk_ref_status

                };
                senddata(JSON.stringify(data));
                $('#ShowRefDocNo').modal('toggle');
        
    }
    function Cancelbill()
    {
        var DocNo = $("#DocNo").val();
        swal({
          title: "",
          text: "ยืนยันการบันทึกเอกสาร "+DocNo+" ",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "ใช่",
          cancelButtonText: "ไม่ใช่",
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          closeOnCancel: false,
          showCancelButton: true}).then(result => 
          {
              if (result.value) 
              {
                var data = 
                {
                    'STATUS'      : 'Cancelbill',
                    'DocNo'      : DocNo
                };
                senddata(JSON.stringify(data));
                $('#v-pills-buyers-tab').tab('show');
              }
              else if (result.dismiss === 'cancel') 
              {
                swal.close();
              }
          })
    }
    function ShowDocNo()
    {
        var DocNochk = "";
        $('input[name="searchrow"]:checked').each(function() 
        {
            DocNochk = $(this).val();
        });

        var data = 
        {
          'STATUS'  : 'ShowDocNo',
          'DocNochk'	: DocNochk
        };
        senddata(JSON.stringify(data));
        
    }
    function Deleteitem()
    {
        var DocNo = $("#DocNo").val();
        var itemcode = "";
        $('input[name="detailrow"]:checked').each(function() 
        {
            itemcode = $(this).val();
        });

        swal({
          title: "",
          text: "ยืนยันการลบรายการ",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "ใช่",
          cancelButtonText: "ไม่ใช่",
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          closeOnConfirm: false,
          closeOnCancel: false,
          showCancelButton: true}).then(result => 
          {
            if (result.value) 
            {
                var data = 
                {
                'STATUS'    : 'Deleteitem',
                'DocNo'	: DocNo,
                'itemcode'	: itemcode
                };
                senddata(JSON.stringify(data));
            } 
            else if (result.dismiss === 'cancel') 
            {
                swal.close();
            } 
          })
        
    }
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/process_lg.php';
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

                 

                        // 1 วิเรียกใช้ Function
                        setTimeout(() =>
                        {
                          ShowRefDocNo();
                        }, 500);
                    }
                    else if(temp["form"]=='ShowRefDocNo')
                    {
                        $( "#TableRefDocNo tbody" ).empty();

                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                  var chkinput = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input checkSingle checkRefDocNo' name='refrow'  value='"+temp[i]['DocNo']+"'  id= ' RefDocNo_id_"+i+" ' required><label class='custom-control-label ' for=' RefDocNo_id_"+i+" ' style='margin-top: 15px;'></label></div> ";

                                   StrTR =   "<tr>"+
                                                "<td>"+chkinput+"</td>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td>"+temp[i]['DocDate']+"</td>"+
                                                "</tr>";
   
                                   $('#TableRefDocNo tbody').append( StrTR );
                              }
       
                    }
                    else if(temp["form"]=='ShowDetail')
                    {
                        $( "#TableDetail tbody" ).empty();

                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                var chkunit ="<select  class='form-control'  id='detailUnit_"+i+"' disabled style='width: 50%;' name='UnitArray'>";
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

                                  var chkinput = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input checkSingle checkdetail' name='detailrow'  value='"+temp[i]['item_code']+"'  id= ' Detail_id_"+i+" ' required><label class='custom-control-label ' for=' Detail_id_"+i+" ' style='margin-top: 15px;'></label></div> ";
                                  var Kilo = "<input type='text' id='Detail_Kilo_"+i+"' class='form-control ' autocomplete='off'  name='KiloArray'  placeholder='0.00' value='"+temp[i]['kilo']+"' style='  text-align: right;' disabled>  ";
                                  var stock_code = "<input type='text'  name='stock_code'   value='"+temp[i]['stock_code']+"'  hidden>  ";

                                  StrTR =   "<tr class='chkrow'>"+
                                                "<td style='width:10%'>"+chkinput+"</td>"+
                                                "<td style='width:40%'>"+temp[i]['item_name']+"</td>"+
                                                "<td style='width:10%'>"+Kilo+"</td>"+
                                                "<td style='width:20%'>"+chkunit+"</td>"+
                                                "<td style='width:20%'>"+stock_code+"</td>"+
                                                "</tr>";
   
                                   $('#TableDetail tbody').append( StrTR );
                              }
                    }
                    else if(temp["form"]=='ShowSearch')
                    {
                    
                        $( "#TableSearch tbody" ).empty();
                                for (var i = 0; i < temp['Row']; i++) 
                                {
                                    var chkinput = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input checkSingle checksearch' name='searchrow'  value='"+temp[i]['DocNo']+"'  id='search_id_"+i+"' required><label class='custom-control-label ' for='search_id_"+i+"' style='margin-top: 15px;'></label></div> ";
                                    
                                    if(temp[i]['IsStatus']==0)
                                    {
                                        Status = "ยังไม่ได้แปรรูป";
                                        Style  = "style='color: #3399ff;'";
                                    }
                                    else if(temp[i]['IsStatus']==1)
                                    {
                                        Status = "เรึ่มต้นการแปรรูป";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==2)
                                    {
                                        Status = "แปรรูปเสร็จสิ้น";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==3)
                                    {
                                        Status = "เสร็จสิ้นกระบวนการ";
                                        Style  = "style='color: #20B80E;'";
                                    }
                                    else if(temp[i]['IsStatus']==3)
                                    {
                                        Status = "ยกเลิกเอกสาร";
                                        Style  = "style='color: #ff0000;'";
                                    }

                                    StrTR =   "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
                                                "<td>"+temp[i]['RefDocNo']+"</td>"+
                                                "<td>"+temp[i]['DocDate']+"</td>"+
                                                "<td>"+temp[i]['Modify_Date']+"</td>"+
                                                "<td>"+temp[i]['employee']+"</td>"+
                                                "<td " +Style+ ">"+Status+"</td>"+

                                                "</tr>";

                                    $('#TableSearch tbody').append( StrTR );
                                }
                    }
                    else if(temp["form"]=='ShowDocNo')
                    {
                        // CLEAR DETAIL
                        $( "#TableDetail tbody" ).empty();
                        // 

                        $("#DocNo").val(temp[0]['DocNo']);
                        $("#docdate").val(temp[0]['DocDate']);
                        $("#ModifyDate").val(temp[0]['Modify_Date']);
                        $("#RefDocNo").val(temp[0]['RefDocNo']);
                        $("#Employee").val(temp[0]['employee']);
                        $("#process").val(temp[0]['start_process']);
                        $("#endprocess").val(temp[0]['end_process']);

                        // DISABLED INPUT
                        $("#ModifyDate").attr('disabled' , true );
                        $("#docdate").attr('disabled' , true );
                        $("#Employee").attr('disabled' , true );
                        $("#RefDocNo").attr('disabled' , true );
                        $("#DocNo").attr('disabled' , true );
                        // 
                   
                        if(temp[0]['IsRef_Status'] == 1)
                        {
                            $("#RefDocNo_text").val("เอกสารสั่งแปรรูป");
                            $("#RefDocNo_text").attr('disabled' , true );
                        }
                        else if(temp[0]['IsRef_Status'] == 2)
                        {
                            $("#RefDocNo_text").val("เอกสารสั่งบรรจุภัณฑ์");
                            $("#RefDocNo_text").attr('disabled' , true );
                        }
                        else
                        {
                            $("#RefDocNo_text").val("");
                            $("#RefDocNo_text").attr('disabled' , false );
                        }

                        if(temp[0]['IsStatus'] == 9)
                        {
                            // disabled
                            $("#C").attr('disabled' , true );
                            $("#A").attr('disabled' , true );
                            $("#S").attr('disabled' , true );
                            $("#CB").attr('disabled' , true );
                            $("#P").attr('disabled' , true );
                            $("#D").attr('disabled' , true );

                            // removeclass

                            $("#HC").removeClass('boxshadowx');
                            $("#HA").removeClass('boxshadowx');
                            $("#HS").removeClass('boxshadowx');
                            $("#HCB").removeClass('boxshadowx');
                            $("#HP").removeClass('boxshadowx');
                            $("#HD").removeClass('boxshadowx');
                        }
                        else
                        {
                            // check process  HSP = start_pro , HEP = end_pro , HS = Save
                            if(temp[0]['IsStatus'] == 0)
                            {   
                                $("#HSP").attr('hidden' , false );
                                $("#HEP").attr('hidden' , true );
                                $("#HS").attr('hidden' , true );
                            }
                            else if (temp[0]['IsStatus'] == 1)
                            {
                                $("#HSP").attr('hidden' , true );
                                $("#HEP").attr('hidden' , false );
                                $("#HS").attr('hidden' , true );
                            }
                            else if (temp[0]['IsStatus'] == 2)
                            {
                                $("#HSP").attr('hidden' , true );
                                $("#HEP").attr('hidden' , true );
                                $("#HS").attr('hidden' , false );
                            }
                            else if (temp[0]['IsStatus'] == 3)
                            {
                                $("#HSP").attr('hidden' , true );
                                $("#HEP").attr('hidden' , true );
                                $("#HS").attr('hidden' , false );
                            }
                            // disabled
                            $("#C").attr('disabled' , false );
                            $("#CB").attr('disabled' , false );
                            $("#P").attr('disabled' , false );
                            $("#D").attr('disabled' , false );

                            // addclass
                            $("#HC").addClass('boxshadowx');
                            $("#HCB").addClass('boxshadowx');
                            $("#HP").addClass('boxshadowx');
                            $("#HD").addClass('boxshadowx');
                        }
                        // 
                        ShowDetail();
                        // 

                        // SHOW MAIN
                        $('#v-pills-all-tab').tab('show')
                        // 
                    }
                    else if(temp["form"]=='Startprocess')
                    {
                        $("#process").val(temp['start_process']);

                        $("#HSP").attr('hidden' , true );

                        $("#HEP").attr('hidden' , false );

                    }
                    else if(temp["form"]=='SaveRefDocNo')
                    {
                        $("#RefDocNo").val(temp['RefDocNo']);
                        $("#RefDocNo").attr('disabled' , true );
                        ShowDetail();

                        if(temp['chk_ref_status'] == 1)
                        {
                            $("#RefDocNo_text").val("เอกสารสั่งแปรรูป");
                        }
                        else
                        {
                            $("#RefDocNo_text").val("เอกสารสั่งบรรจุภัณฑ์");
                        }
                        $("#RefDocNo_text").attr('disabled' , true );
                    }
                    else if(temp["form"]=='Endprocess')
                    {
                        $("#endprocess").val(temp['end_process']);

                        $("#HSP").attr('hidden' , true );

                        $("#HEP").attr('hidden' , true );

                        $("#HS").attr('hidden' , false );


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
                    case "Reffail":
                                temp['msg'] = "ไม่พบ "+temp['chk_ref_str']+" ของวันที่ "+temp['dateRefDocNo']+"  ";
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
        .boxshadowx button{
            box-shadow: none  !important;
        }
        .boxshadowx button:hover{
            color: #bcaaa4 !important;
          }
          .datepicker{
          z-index:9999 !important
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
                        บันทึกการแปรรูปลำไย
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" 
                        aria-controls="v-pills-all"><i class="icon icon-home2"></i>การแปรรูปลำไย</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                           aria-controls="v-pills-buyers" onclick=" ShowSearch();" ><i class="icon-search3" ></i> ค้นหา </a>
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
                        <label class=" col-sm-4 form-label  h4" >เอกสารอ้างอิง</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="RefDocNo"   placeholder="เอกสารอ้างอิง" onclick="ShowRefDocNo();">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >สถานะ</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="RefDocNo_text"   placeholder="สถานะ" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >เรึ่มต้นแปรรูป</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="process"   placeholder="เรึ่มต้นแปรรูป" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class='form-group row  text-black'>
                        <label class=" col-sm-4 form-label  h4" >สิ้นสุดแปรรูป</label>
                        <input type="text" autocomplete="off"   class=" col-sm-7 form-control " id="endprocess"   placeholder="สิ้นสุดแปรรูป" disabled>
                    </div>
                </div>
            </div>

            <div class="row box  col-md-12 my-3 d-flex justify-content-end">

                            <div class=" ml-5 boxshadowx " id="HC">
                            <button type="button" class="btn "   onclick="Createdocument();" id="C">
                                    <i class="icon-document-add2 blue accent-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">สร้างเอกสาร</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HSP" >
                            <button type="button" class="btn "  onclick="Startprocess();" id="SP">
                                    <i class="icon-hourglass-1  lime darken-3 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">เรึ่มต้นการแปรรูป</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HEP" hidden>
                            <button type="button" class="btn "  onclick="Endprocess();" id="EP">
                                    <i class="icon-hourglass-end green darken-3 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">สิ้นสุดการแปรรูป</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HS" hidden>
                            <button type="button" class="btn " onclick="Successprocess();"  id="S">
                                    <i class="icon-save2 green lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">บันทึก</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HD" hidden>
                            <button type="button" class="btn " onclick="Deleteitem()" id="D">
                                    <i class="icon-delete  red lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">ลบรายการ</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HS" hidden>
                            <button type="button" class="btn " onclick="Savebill()" id="S">
                                    <i class="icon-save2 green lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">บันทึก</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HCB">
                            <button type="button" class="btn " onclick="Cancelbill()" id="CB">
                                    <i class="icon-document-cancel2 red lighten-1 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">ยกเลิกเอกสาร</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HP" hidden>
                            <button type="button" class="btn "  id="P" >
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
                                            <th style="width : 50%;">ชื่อรายการ</th>
                                            <th>ปริมาณ(กก)</th>
                                            <th>หน่วยนับ</th>
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
                    <button type="button" class="btn btn-success btn-lg" onclick="ShowDocNo()">
                    <i class="icon-documents4"></i> แสดงเอกสาร </button>
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
                                            <th>NO.</th>
                                            <th>เลขที่เอกสาร</th>
                                            <th>เอกสารขอเบิก</th>
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
    <a hidden  href="panel-page-users-create.html" class="btn-fab btn-fab-md fab-right fab-right-bottom-fixed shadow btn-primary"><i class="icon-add"></i></a>
</div>
<!-- /.right-sidebar -->
<!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg shadow white fixed"></div>
</div>


<!--------------------------------------- Modal RefDocNo  ------------------------------------------>
<div class="modal fade" id="ShowRefDocNo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">เอกสารการขอเบิก</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row ">
        <div class="col-md-4 mt-2 ">
                <select  autocomplete="off" class ="form-control "  id="chk_ref_status">
                    <option value="1">เอกสารสั่งแปรรูป</option> 
                    <option value="2">เอกสารสั่งบรรจุภัณฑ์</option> 
                </select>
            </div>
            <div class="col-md-4 mt-2 ">
                <input type="text" autocomplete="off" class ="form-control datepicker-here" id="dateRefDocNo" data-language='en' data-date-format='yyyy-mm-dd' placeholder="ค้นหาจากวันที่">
            </div>
            <div class="col-md-4  mt-2 ">
                <button type="button" class="btn btn-primary btn-lg" onclick="ShowRefDocNo()">
                <i class="icon-search3"></i> ค้นหา </button>
            </div>
        </div>
      <table class="table table-striped table-hover r-0" id="TableRefDocNo">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th></th>
                                            <th>เลขที่เอกสาร</th>
                                            <th>วันที่เอกสาร</th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >

                                        </tbody>
                                    </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
        <button type="button"  class="btn btn-success" onclick="SaveRefDocNo()">ยืนยัน</button>
      </div>
    </div>
  </div>
</div>
<!-------------------------- end RefDocNo Modal ----------------------------------------------->
<!--/#app -->
<script src="assets/js/app.js"></script>




<!--
--- Footer Part - Use Jquery anywhere at page.
--- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
-->
<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>