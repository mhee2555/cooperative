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
    <title>บันทึกการสั่งบรรจุภัณฑ์ข้าว</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">

    <script type="text/javascript">
    $(document).ready(function(e)
    {
        $("#P").attr('disabled' , true );
        // ========
        Showuser();
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
        // 

        $("#v-pills-all-tab").click(function()
        {
            $( "#TableDetail tbody" ).empty();
            $("#DocNo").val("");
            $("#docdate").val("");
            $("#ModifyDate").val("");
            $("#Employee").val("");
         });

         $("#v-pills-buyers-tab").click(function()
         {
            ShowSearch();
         });
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
    function Importdata()
    {
        var DocNo = $("#DocNo").val();
        var iArray = [];
        var kiloArray = [];
        var item_codeArray = [];
        var unitArray = [];
        var stock_codeArray = [];
        
        $(".checkitem:checked").each(function() 
        {
            iArray.push($(this).val());
        });
        // =======================================================
        for(var j=0;j<iArray.length; j++)
        {
            item_codeArray.push( $("#item_code_"+iArray[j]).val() );
            kiloArray.push( $("#Kilo_"+iArray[j]).val() );
            unitArray.push( $("#iUnit_"+iArray[j]).val() );
            stock_codeArray.push( $("#stock_code_"+iArray[j]).val() );
        }
        // =======================================================
        var item_code = item_codeArray.join(',') ;
        var kilo = kiloArray.join(',') ;
        var xunit = unitArray.join(',') ;
        var stock_code = stock_codeArray.join(',') ;
        // =======================================================
        $( "#TableDetail tbody" ).empty();




        var data = 
        {
          'STATUS'  	: 'Importdata',
          'item_code'   : item_code,
          'kilo'		: kilo,
          'DocNo'		: DocNo ,
          'xunit'		: xunit,
          'stock_code'		: stock_code
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
    function Showuser()
    {
        var data = {
            'STATUS'  : 'Showuser'
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
                    // =======================================================
            var ItemCodeArray = [];
            var KiloArray = [];

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
                        'DocNo'      : DocNo ,
                        'ItemCode'      : ItemCode ,
                        'Kilo'      : Kilo
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
    function check_unit(i)
    {
        var unit = $("#iUnit_"+i).val();

        var data = {
            'STATUS'  : 'check_unit',
                'i'	: i,
                'unit'	: unit
        };
        senddata(JSON.stringify(data));
        

    }
    function Sumitem(i)
    {
        // var qtyperunit = parseFloat($("#qtyperunit_"+i).val());
        // var qty = parseFloat($("#Kilo_"+i).val());
        // var total_stock_nan = parseFloat($("#total_stock_nan_"+i).val());

        // // ทุกครั้งที่พิมให้ reset
        // parseFloat($("#total_stock_"+i).val(total_stock_nan));
        // parseFloat($("#gram_"+i).val('0.00'));

        // var total_stock = parseFloat($("#total_stock_"+i).val());
        // var sumqty = parseFloat((qty * qtyperunit));
        // var sumqty_garm = parseFloat((sumqty / 1000));

  
        // if(isNaN(sumqty_garm) )
        // {
        //     sumqty_garm = '0.00';
        // }


        // var sumqty_total =  total_stock - sumqty_garm  ;


        // if(isNaN(sumqty_total) )
        // {
        //     sumqty_total = total_stock_nan;
        // }
        // // จำนวนก่อน ลบ
        // parseFloat($("#gram_"+i).val(sumqty_garm));
        
        // // จำนวนทั้งหมด ลบ กรัม
        // parseFloat($("#total_stock_"+i).val(sumqty_total));

        var qtyperunit = parseFloat($("#qtyperunit_"+i).val());
        var qty = parseFloat($("#Kilo_"+i).val());
        var total_stock_nan = parseFloat($("#total_stock_nan_"+i).val());

        // ทุกครั้งที่พิมให้ reset
        parseFloat($("#total_stock_"+i).val(total_stock_nan));
        parseFloat($("#gram_"+i).val('0.00'));

        var total_stock = parseFloat($("#total_stock_"+i).val());
        var sumqty = parseFloat((qty * qtyperunit));
        var sumqty_garm = parseFloat((sumqty / 1000));

  
        if(isNaN(sumqty_garm) )
        {
            sumqty_garm = '0.00';
        }


        var sumqty_total =  total_stock - sumqty_garm  ;

        if(sumqty_total < 0)
        {
            sumqty_total = 0;
        }

        if(sumqty_garm >= total_stock_nan)
        {
            sumqty_garm = total_stock_nan;

            var kilo_gram = parseFloat((total_stock_nan * 1000));

            var kilo_x = parseFloat((kilo_gram / qtyperunit));

            // จำนวนถ้ากรอกยอด ติดลบ
            parseFloat($("#Kilo_"+i).val(kilo_x));
        }

        if(isNaN(sumqty_total) )
        {
            sumqty_total = total_stock_nan;
        }

        // จำนวนก่อน ลบ
        parseFloat($("#gram_"+i).val(sumqty_garm));
        
        
    
        // จำนวนทั้งหมด ลบ กรัม
        parseFloat($("#total_stock_"+i).val(sumqty_total));

    }
    
function report_rc(){
   
   var DocNo = $("#DocNo").val();
   var Employee = $("#Employee").val();
   var docdate = $("#docdate").val();


       url = "../tcreport/Report_packing_rc.php?eDate=" + docdate +"&DocNo=" + DocNo+"&Employee=" + Employee;
       window.open(url);



}
//-----------------------------------------------------------------------------------------
    function senddata(data)
    {
         var form_data = new FormData();
         form_data.append("DATA",data);
         var URL = '../process/packing_rc.php';
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
                        $( "#TableDetail tbody" ).empty();

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
                                var chkunit ="<select  class='form-control' style='width: 120px;'  id='iUnit_"+i+"' onchange='check_unit("+i+")'>";
                                    $.each(temp['Unit'], function(key, val)
                                    {
                                        if(temp[i]['PackgeCode']==val.PackgeCode)
                                        {
                                            chkunit += '<option selected value=" '+val.PackgeCode+' " >'+val.PackgeName+'</option>';
                                        }
                                        else
                                        {
                                            chkunit += '<option value="' +val.PackgeCode+' ">'+val.PackgeName+'</option>';
                                        }
                                    });
                                    chkunit += "</select>";

                                  var chkinput = "<div class='custom-control custom-checkbox'><input type='checkbox' class='custom-control-input checkSingle checkitem'  value='"+i+"'  id= 'item_id_"+i+"' required><label class='custom-control-label ' for='item_id_"+i+"' style='margin-top: 15px;'></label></div> <input type='hidden' id='item_code_"+i+"' value='"+temp[i]['item_code']+"'> <input type='hidden' id='stock_code_"+i+"' value='"+temp[i]['stock_code']+"'>";
                                  var Kilo = "<input type='text' id='Kilo_"+i+"' class='form-control numonly' autocomplete='off' style='text-align:right'  placeholder='0.00'  onkeyup='Sumitem(\""+i+"\" ) ' >  ";
                                  var qtyperunit = "<input disabled type='text' id='qtyperunit_"+i+"' class='form-control '  value='"+temp[0]['Qtyperunit']+"'   autocomplete='off' style='text-align:right'  placeholder='0.00' >  ";
                                  var total_stock = "<input disabled type='text' id='total_stock_"+i+"' class='form-control '  value='"+temp[i]['Grade']+"'   autocomplete='off' style='text-align:right'  placeholder='0.00' >  ";
                                  var total_stock_nan = "<input hidden type='text' id='total_stock_nan_"+i+"' class='form-control '  value='"+temp[i]['Grade']+"'   autocomplete='off' style='text-align:right'  placeholder='0.00' >  ";
                                  var gram = "<input disabled type='text' id='gram_"+i+"' class='form-control '     autocomplete='off' style='text-align:right'  placeholder='0.00' >  ";

                                 StrTR = "<tr>"+
                                                "<td >"+chkinput+"</td>"+
                                                "<td style=' width: 15%; '>"+temp[i]['item_name']+"</td>"+
                                                "<td  >"+total_stock+"</td>"+
                                                "<td  >"+gram+"</td>"+
                                                "<td style='width: 13%;'>"+temp[i]['DocNo']+"</td>"+
                                                "<td  hidden>"+total_stock_nan+"</td>"+
                                                "<td >"+Kilo+"</td>"+
                                                "<td >"+qtyperunit+"</td>"+
                                                "<td >"+chkunit+"</td>"+
                                                "</tr>";
   
                                   $('#Tableitem tbody').append( StrTR );
                              }
                              $('.numonly').on('input', function()
                              {
                                this.value = this.value.replace(/[^0-9.]/g, ''); //<-- replace all other than given set of values
                              });
                    }
                    else if(temp["form"]=='check_unit')
                    {
                        $("#qtyperunit_"+temp['i']).val(temp['Qtyperunit']);

                        Sumitem(temp['i']);
                    }
                    else if(temp["form"]=='ShowDetail')
                    {
                        $( "#TableDetail tbody" ).empty();
                              for (var i = 0; i < temp['Row']; i++) 
                              {
                                var chkunit ="<select  class='form-control'  id='detailUnit_"+i+"' disabled style='width: 50%;'>";
                                    $.each(temp['Unit'], function(key, val)
                                    {
                                        if(temp[i]['UnitCode']==val.PackgeCode)
                                        {
                                            chkunit += '<option selected value=" '+val.PackgeCode+' " >'+val.PackgeName+'</option>';
                                        }
                                        else
                                        {
                                            chkunit += '<option value="' +val.PackgeCode+' ">'+val.PackgeName+'</option>';
                                        }
                                    });
                                    chkunit += "</select>";

                                  var chkinput = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input checkSingle checkdetail' name='detailrow'  value='"+temp[i]['item_code']+"'  id= ' Detail_id_"+i+" ' required><label class='custom-control-label ' for=' Detail_id_"+i+" ' style='margin-top: 15px;'></label></div> ";
                                  var Kilo = "<input type='text' id='Detail_Kilo_"+i+"' class='form-control ' style='text-align:right' autocomplete='off'  name='KiloArray'  placeholder='0.00' value='"+temp[i]['kilo']+"' disabled>  ";

                                   StrTR =   "<tr class='chkrow'>"+
                                                "<td style='width:10%'>"+chkinput+"</td>"+
                                                "<td style='width:40%'>"+temp[i]['item_name']+"</td>"+
                                                "<td style='width:10%'>"+Kilo+"</td>"+
                                                "<td style='width:20%'>"+chkunit+"</td>"+
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
                                        Status = "บันทึกสำเร็จ";
                                        Style  = "style='color: #20B80E;'";
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

                        // 

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
                        }else if(temp[0]['IsStatus'] == 1){
                            $("#P").attr('disabled' , false );
                        }
                        else
                        {
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
                        บันทึกการสั่งบรรจุภัณฑ์ข้าว
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" 
                        aria-controls="v-pills-all"><i class="icon icon-home2"></i>สั่งบรรจุภัณฑ์ข้าว</a>
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
                            <button type="button" class="btn "  id="P" onclick="report_rc()">
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
                                            <th>ปริมาณ</th>
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
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content" >
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
                                            <th>จำนวนทั้งหมด</th>
                                            <th>กิโลกรัม</th>
                                            <th>รหัสคลังสินค้า</th>
                                            <th>ปริมาณ</th>
                                            <th>กรัม</th>
                                            <th>หน่วยบรรจุภัณฑ์</th>
                                        </tr>
                                        </thead>

                                        <tbody  id="tbody"  >

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