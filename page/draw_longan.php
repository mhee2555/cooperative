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
    <title>บันทึกกาขอเบิกลำใย</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
    $(document).ready(function(e)
    {

        $("#P").attr('disabled' , true );
      // ===========DATE ITEM =======
      var d = new Date();
      var month = d.getMonth()+1;
      var day = d.getDate();
      var output = d.getFullYear() + '-' +
          ((''+month).length<2 ? '0' : '') + month + '-' +
          ((''+day).length<2 ? '0' : '') + day;
      $("#datestock").val(output);
        // ========
        Showuser();
        ShowSearch();
        // ========
        // ค้นหา
        $("#Search").on("keyup", function() 
        {
            var value = $(this).val().toLowerCase();
            $("#TableSearch tbody tr").filter(function() 
            {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
    // 
    
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
            if (result.value) 
            {
                var data = 
                {
                    'STATUS'    : 'CreateDocument',
                    'userid'	: userid
                };
                senddata(JSON.stringify(data));
                // clear detail
                $( "#TableDetail tbody" ).empty();
            }
            else if (result.dismiss === 'cancel')
            {
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
            ShowItem(1);
        }
    }
    function ShowItem(chk)
    {
        var datestock = $("#datestock").val();
        var data = 
        {
            'STATUS'  : 'ShowItem',
            'datestock'	: datestock,
            'chk'	: chk
        };
        senddata(JSON.stringify(data));
    }   
    function Sumitem(ccqty ,rowid)
    {
        // ค่าเบิิก
        var draw =  parseFloat($("#draw_"+rowid).val());

        // เก็บคงเหลือ
        var fix =  parseFloat($("#fix_"+rowid).val());

        //หักลบ
        var total = parseFloat(ccqty - draw);

        if(total <= 0)
        {
          $("#draw_"+rowid).val(ccqty); 
          $("#ccqty_total_"+rowid).val(0); 
        }
        else
        {
          if(isNaN(total))
          {
            total = fix;
          }
          $("#ccqty_total_"+rowid).val(total); 
        }

    }
    function Importdata()
    {
        var DocNo = $("#DocNo").val();
        var iArray = [];
        var kiloArray = [];
        var stock_codeArray = [];
        var item_codeArray = [];
        var unitArray = [];

        $(".checkitem:checked").each(function() 
        {
            iArray.push($(this).val());
        });
        // =======================================================
        for(var j=0;j<iArray.length; j++)
        {
            item_codeArray.push( $("#item_code_"+iArray[j]).val() );
            kiloArray.push( $("#draw_"+iArray[j]).val() );
            stock_codeArray.push( $("#stock_code_"+iArray[j]).val() );
            unitArray.push( $("#iUnit_"+iArray[j]).val() );
        }
        // =======================================================
        var item_code = item_codeArray.join(',') ;
        var kilo = kiloArray.join(',') ;
        var stock_code = stock_codeArray.join(',') ;
        var xunit = unitArray.join(',') ;
        // =======================================================

        $( "#TableDetail tbody" ).empty();

        var data = 
        {
          'STATUS'  	: 'Importdata',
          'item_code'   : item_code,
          'kilo'		: kilo,
          'DocNo'		: DocNo,
          'stock_code'  : stock_code,
          'xunit'		: xunit
        };
        $('#Additem').modal('toggle');
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
    function Savebill()
    {
        var chkrow  =   $( ".chkrow" ).length;
        if(chkrow > 0)
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
                        'STATUS'      : 'Savebill',
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
    function Cancelbill()
    {
        var DocNo = $("#DocNo").val();
        if(DocNo == '')
        {
            swal({
                title: '',
                text: 'กรุณาสร้างเอกสารก่อนยกเลิกเอกสาร',
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
          text: "ยืนยันการยกเลิกเอกสาร "+DocNo+" ",
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
    function showdetailsub(itemcode)
    {
        $('#DetailSub').modal('show');
        var DocNo = $("#DocNo").val();
        var data = 
                {
                    'STATUS'    : 'ShowdetailSub',
                    'DocNo'	: DocNo,
                    'itemcode'	: itemcode
                };
                senddata(JSON.stringify(data));
    }
    function Deleteitem()
    {   
        var chkrow  =   $( ".chkrow" ).length;
        if(chkrow < 1)
        {
            swal({
                title: '',
                text: 'กรุณาเพิ่มรายการก่อนลบรายการ',
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
        
        
    }

    function report_Lg(){
   
        var DocNo = $("#DocNo").val();
        var Employee = $("#Employee").val();
        var docdate = $("#docdate").val();


            url = "../tcreport/Report_draw_longan.php?eDate=" + docdate +"&DocNo=" + DocNo+"&Employee=" + Employee;
            window.open(url);
   
       
    
    }
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/draw_longan.php';
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
                                var chkunit ="<select  class='form-control' style='width: 120px;'  id='iUnit_"+i+"'>";
                                    $.each(temp['Unit'], function(key, val)
                                    {
                                        if(temp[i]['UnitCode']==val.UnitCode)
                                        {
                                            chkunit += '<option  selected value=" '+val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                        else
                                        {
                                            chkunit += '<option value="' +val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                    });
                                    chkunit += "</select>";



                                  var chkinput = "<div class='custom-control custom-checkbox'><input type='checkbox' class='custom-control-input checkSingle checkitem'  value='"+i+"'  id= ' item_id_"+i+" ' required><label class='custom-control-label ' for=' item_id_"+i+" ' style='margin-top: 15px;'></label></div> <input type='hidden' id='item_code_"+i+"' value='"+temp[i]['item_code']+"'><input type='hidden' id='stock_code_"+i+"' value='"+temp[i]['stock_code']+"'>";
                                  var draw = "<input type='text' id='draw_"+i+"' class='form-control numonly' autocomplete='off' style='text-align:right;width: 100px;'  placeholder='0.00' onkeyup='Sumitem(\""+temp[i]['item_ccqty']+"\" , \""+i+"\" ) '>  ";
                                  var qty_total = "<input type='text' id='qty_total_"+i+"' class='form-control ' autocomplete='off' style='text-align:right'   placeholder='0.00' value='"+temp[i]['item_qty']+"'  disabled>  ";
                                  var qty_cc = "<input type='text' id='ccqty_total_"+i+"' class='form-control ' autocomplete='off' style='text-align:right'  placeholder='0.00' value='"+temp[i]['item_ccqty']+"'  disabled>  ";
                                  // hidden
                                  var fix = "<input type='hidden' id='fix_"+i+"' value='"+temp[i]['item_ccqty']+"'  >  ";

                                 StrTR = "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td style='width: 15%;'>"+temp[i]['item_name']+"</td>"+
                                                "<td >"+qty_total+"</td>"+
                                                "<td >"+qty_cc+"</td>"+
                                                "<td style='width: 13%;'>"+temp[i]['DocNo']+"</td>"+
                                                "<td style='width: 10%;'>"+temp[i]['date_exp']+"</td>"+
                                                "<td >"+draw+"</td>"+
                                                "<td >"+chkunit+"</td>"+
                                                "<td hidden>"+fix+"</td>"+
                                                "</tr>";
   
                                   $('#Tableitem tbody').append( StrTR );
                              }
                              $('.numonly').on('input', function()
                              {
                                this.value = this.value.replace(/[^0-9.]/g, ''); //<-- replace all other than given set of values
                              });
                    }
                    else if(temp["form"]=='ShowdetailSub')
                    {

                        $( "#TableDetailSub tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {

                                var chkunit ="<select  class='form-control'  disabled>";
                                    $.each(temp['Unit'], function(key, val)
                                    {
                                        if(temp[i]['UnitCode']==val.UnitCode)
                                        {
                                            chkunit += '<option  selected value=" '+val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                        else
                                        {
                                            chkunit += '<option value="' +val.UnitCode+' ">'+val.UnitName+'</option>';
                                        }
                                    });
                                    chkunit += "</select>";




                                 StrTR = "<tr>"+
                                                "<td>"+temp[i]['item_name']+"</td>"+
                                                "<td >"+temp[i]['DocNo']+"</td>"+
                                                "<td >"+temp[i]['kilo']+"</td>"+
                                                "<td >"+chkunit+"</td>"+
                                                "</tr>";
   
                                   $('#TableDetailSub tbody').append( StrTR );
                              }
                    }
                    else if(temp["form"]=='ShowDetail')
                    {
                        $( "#TableDetail tbody" ).empty();
                        // total
                        $("#Total").val(temp['Total']);
                        // 
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                var chkunit ="<select  class='form-control'  id='detailUnit_"+i+"' disabled style='width: 50%;'>";
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
                                  var Kilo = "<input type='text' id='Detail_Kilo_"+i+"' class='form-control ' autocomplete='off'  name='KiloArray'  placeholder='0.00' value='"+temp[i]['kilo']+"' style='text-align: right;'>  ";
                                  var ShowEdit = "<a href='javascript:void(0)'  onclick='showdetailsub("+temp[i]['item_code']+");'><i style='font-size : 30px; ' class='icon-search3'></i></a> ";

                                   StrTR =   "<tr class='chkrow'>"+
                                                "<td style='width:10%'>"+chkinput+"</td>"+
                                                "<td style='width:40%'>"+temp[i]['item_name']+"</td>"+
                                                "<td style='width:10%'>"+Kilo+"</td>"+
                                                "<td style='width:20%'>"+chkunit+"</td>"+
                                                "<td >"+ShowEdit+"</td>"+
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
                                        Status = "อนุมัติเรียบร้อย";
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

                                    StrTR =   "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td>"+temp[i]['DocNo']+"</td>"+
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
                        $("#Employee").val(temp[0]['employee']);

                        // DISABLED INPUT
                        $("#ModifyDate").attr('disabled' , true );
                        $("#docdate").attr('disabled' , true );
                        $("#Employee").attr('disabled' , true );
                        $("#DocNo").attr('disabled' , true );
                        // 
                  
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
                        }else if(temp[0]['IsStatus'] == 2)
                        {
                            // disabled
                            $("#C").attr('disabled' , false );
                            $("#A").attr('disabled' , false );
                            $("#S").attr('disabled' , false );
                            $("#CB").attr('disabled' , false );
                            $("#P").attr('disabled' , false );
                            $("#D").attr('disabled' , false );

                            // addclass
                            $("#HC").addClass('boxshadowx');
                            $("#HA").addClass('boxshadowx');
                            $("#HS").addClass('boxshadowx');
                            $("#HCB").addClass('boxshadowx');
                            $("#HP").addClass('boxshadowx');
                            $("#HD").addClass('boxshadowx');
                        }
                        else{
                            // disabled
                            $("#C").attr('disabled' , false );
                            $("#A").attr('disabled' , false );
                            $("#S").attr('disabled' , false );
                            $("#CB").attr('disabled' , false );
                            $("#P").attr('disabled' , true );
                            $("#D").attr('disabled' , false );

                            // addclass
                            $("#HC").addClass('boxshadowx');
                            $("#HA").addClass('boxshadowx');
                            $("#HS").addClass('boxshadowx');
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
                        บันทึกการขอเบิกลำใย
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" 
                        aria-controls="v-pills-all"><i class="icon icon-home2"></i>การขอเบิก</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab"
                           aria-controls="v-pills-buyers"><i class="icon-search3"></i> ค้นหา </a>
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
            <div class="row box  col-md-12 my-3 d-flex justify-content-end">

                            <div class=" ml-5 boxshadowx " id="HC">
                            <button type="button" class="btn "   onclick="Createdocument();" id="C">
                                    <i class="icon-document-add2 blue accent-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">สร้างเอกสาร</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HA">
                            <button type="button" class="btn "  onclick="Additem();" id="A">
                                    <i class="icon-add_circle blue lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">เพิ่มรายการ</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HD">
                            <button type="button" class="btn " onclick="Deleteitem()" id="D">
                                    <i class="icon-delete  red lighten-2 avatar-md circle avatar-letter"></i>
                                    <div class="pt-1">ลบรายการ</div>
                            </button>
                            </div>

                            <div class=" ml-5 boxshadowx" id="HS">
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

                            <div class=" ml-5 boxshadowx" id="HP">
                            <button type="button" class="btn "  id="P" onclick="report_Lg()">
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
                                            <th >NO.</th>
                                            <th >ชื่อรายการ</th>
                                            <th>ขอเบิก</th>
                                            <th >หน่วยนับ</th>
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

<!--------------------------------------- Modal add_customer  ------------------------------------------>
<div class="modal fade" id="Additem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style='width : 140%;'>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">เพิ่ม รายการ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row ">
          <div class="col-md-4 mt-2 ">
            <input type="text" autocomplete="off" class ="form-control datepicker-here" id="datestock" data-language='en' data-date-format='yyyy-mm-dd' placeholder="ค้นหาจากวันที่">
        </div>
          <div class="col-md-4  mt-2 ">
            <button type="button" class="btn btn-primary btn-lg" onclick="ShowItem(2)">
            <i class="icon-search3"></i> ค้นหา </button>
          </div>
      </div>
      <table class="table table-striped table-hover r-0" id="Tableitem">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th></th>
                                            <th>ชื่อรายการ</th>
                                            <th>จำนวนทั้งหมด(กก)</th>
                                            <th>คงเหลือ(กก)</th>
                                            <th>รหัสคลังสินค้า</th>
                                            <th>หมดอายุ</th>
                                            <th>ขอเบิก(กก)</th>
                                            <th>หน่วยนับ</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbody">
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
<div class="modal fade" id="DetailSub" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel" style="color:#000000;">รายละเอียด</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table table-striped table-hover r-0" id="TableDetailSub">
                                        <thead id="theadsum" >
                                        <tr class="no-b">
                                            <th>ชื่อรายการ</th>
                                            <th>ลอต</th>
                                            <th>ขอเบิก(กก)</th>
                                            <th>หน่วยนับ</th>
                                        </tr>
                                        </thead>
                                        <tbody  id="tbodydetail">
                                        </tbody>
                                    </table>
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