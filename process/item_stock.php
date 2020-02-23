<?php
session_start();
require '../connect/connect.php';

  function Showitem($conn, $DATA)
  {
    $type = $DATA["type"];
    $count = 0;
    $table ='';

    if($type ==1 || $type ==2) 
    {
      $table = 'stock_unprocess';
    }
    else if($type ==3 || $type ==4) 
    {
      $table = 'stock_process';
    }
    
    
        $Showitem = "SELECT
                                  item.item_name,
                                  $table.item_code,
                                  $table.item_qty
                                FROM
                                  $table
                                INNER JOIN item ON $table.item_code = item.item_code
                                WHERE item.item_type = $type
        ";

        // ค้นหาจาก item_type
        // if($item_type > 0) {$Showitem .=" WHERE item.item_type = $item_type";}
        // 

      $Showitem.=" ORDER BY item.item_code DESC";

      $return['SQL'] = $Showitem;  
      $meQuery = mysqli_query($conn, $Showitem);
      while ($Result = mysqli_fetch_assoc($meQuery)) {
        $return[$count]['item_code']          = $Result['item_code'];
        $return[$count]['item_name']          = $Result['item_name'];
        $return[$count]['item_qty']          = $Result['item_qty'];
        $count++;
      }
      $return['count']  = $count;
    if($count>0){
      $return['status'] = "success";
      $return['form'] = "Showitem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }else{
      $return['status'] = "success";
      $return['form'] = "Showitem";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }

  }

  function Showtype($conn, $DATA)
  {
    $count=0;
    $Showtype = "SELECT 
                  type_item.id,
                  type_item.type_name 
                 FROM 
                  type_item ";
      $meQuery = mysqli_query($conn, $Showtype);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['id']          = $Result['id'];
        $return[$count]['type_name']   = $Result['type_name'];
        $count++;
      }
      $return['count']  = $count;
      if($count>0)
      {
        $return['status'] = "success";
        $return['form'] = "Showtype";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }
      else
      {
        $return['status'] = "success";
        $return['form'] = "Showtype";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }
  }

  function ShowSearch($conn, $DATA)
  {
    $datepicker  = $DATA["datepicker"]==''?date('Y-m-d'):$DATA["datepicker"];
    $boolean = false;
    $count = 0;

    $Showsearch = "SELECT
                    d.DocNo,
                    d.DocDate,
                    TIME(d.Modify_Date) AS  Modify_Date, 
                    emp.FName AS employee ,
                    d.IsStatus
                  FROM
                    draw d
                  INNER JOIN employee emp ON emp.ID = d.Employee_ID
                  WHERE d.DocDate = '$datepicker' AND IsStatus <> 0 AND IsStatus <> 9  ORDER BY d.DocNo DESC ";

      $meQuery = mysqli_query($conn, $Showsearch);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['DocNo']         = $Result['DocNo'];
        $return[$count]['DocDate']       = $Result['DocDate'];
        $return[$count]['Modify_Date']   = $Result['Modify_Date'];
        $return[$count]['employee']      = $Result['employee'];
        $return[$count]['IsStatus']      = $Result['IsStatus'];

        $count ++ ;
        $boolean = true;
    
      }
      $return['Row'] = $count;

    if ($boolean) 
    {
      $return['status'] = "success";
      $return['form'] = "ShowSearch";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
    else
    {
      $return['status'] = "failed";
      $return['msg'] = "searchfailed";
      $return['status'] = "failed";
      $return['date'] = $datepicker;
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
  }

  function ShowSearch_rice($conn, $DATA)
  {
    $datepicker  = $DATA["datepicker"]==''?date('Y-m-d'):$DATA["datepicker"];
    $boolean = false;
    $count = 0;

    $Showsearch = "SELECT
                    d.DocNo,
                    d.DocDate,
                    TIME(d.Modify_Date) AS  Modify_Date, 
                    emp.FName AS employee ,
                    d.IsStatus
                  FROM
                    draw_rice d
                  INNER JOIN employee emp ON emp.ID = d.Employee_ID
                  WHERE d.DocDate = '$datepicker' AND IsStatus <> 0 AND IsStatus <> 9  ORDER BY d.DocNo DESC ";

      $meQuery = mysqli_query($conn, $Showsearch);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['DocNo']         = $Result['DocNo'];
        $return[$count]['DocDate']       = $Result['DocDate'];
        $return[$count]['Modify_Date']   = $Result['Modify_Date'];
        $return[$count]['employee']      = $Result['employee'];
        $return[$count]['IsStatus']      = $Result['IsStatus'];

        $count ++ ;
        $boolean = true;
    
      }
      $return['Row'] = $count;

    if ($boolean) 
    {
      $return['status'] = "success";
      $return['form'] = "ShowSearch_rice";
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
    else
    {
      $return['status'] = "failed";
      $return['msg'] = "searchfailed";
      $return['status'] = "failed";
      $return['date'] = $datepicker;
      echo json_encode($return);
      mysqli_close($conn);
      die;
    }
  }

  function showdetaildraw($conn, $DATA)
  {
    $count = 0;
    $boolean = false;
    $DocNo   = $DATA["DocNo"] ;

    $Sql = "SELECT
              dds.draw_DocNo,
              dds.kilo,
              item.item_name ,
              sup.item_ccqty,
              DATE(sup.Date_exp) as date,
              TIME(sup.Date_exp) as time,
              dds.stock_code,
              dds.draw_DocNo
            FROM draw_detail_sub dds
            INNER JOIN item ON item.item_code = dds.item_code
            INNER JOIN stock_unprocess sup ON sup.stock_code = dds.stock_code 
            WHERE dds.draw_DocNo = '$DocNo' ";

      $meQuery = mysqli_query($conn, $Sql);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['kilo'] = $Result['kilo'];
        $return[$count]['item_name'] = $Result['item_name'];
        $return[$count]['item_ccqty'] = $Result['item_ccqty'];
        $return[$count]['date'] = $Result['date'];
        $return[$count]['time'] = $Result['time'];
        $return[$count]['stock_code'] = $Result['stock_code'];
        $return[$count]['draw_DocNo'] = $Result['draw_DocNo'];
        $count++;
        $boolean = true;
      }
      $return['Row'] = $count;

      if ($boolean)
      {
        $return['sql'] = $Sql;
        $return['status'] = "success";
        $return['form'] = "showdetaildraw";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      } 
      else 
      {
        $return['sql'] = $Sql;
        $return['status'] = "success";
        $return['form'] = "showdetaildraw";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }

  }
  function showdetaildraw_rice($conn, $DATA)
  {
    $count = 0;
    $boolean = false;
    $DocNo   = $DATA["DocNo"] ;

    $Sql = "SELECT
              dds.draw_DocNo,
              dds.kilo,
              item.item_name ,
              sup.item_ccqty,
              DATE(sup.Date_exp) as date,
              TIME(sup.Date_exp) as time,
              dds.stock_code,
              dds.draw_DocNo
            FROM draw_rice_detail_sub dds
            INNER JOIN item ON item.item_code = dds.item_code
            INNER JOIN stock_unprocess sup ON sup.stock_code = dds.stock_code 
            WHERE dds.draw_DocNo = '$DocNo' ";

      $meQuery = mysqli_query($conn, $Sql);
      while ($Result = mysqli_fetch_assoc($meQuery)) 
      {
        $return[$count]['kilo'] = $Result['kilo'];
        $return[$count]['item_name'] = $Result['item_name'];
        $return[$count]['item_ccqty'] = $Result['item_ccqty'];
        $return[$count]['date'] = $Result['date'];
        $return[$count]['time'] = $Result['time'];
        $return[$count]['stock_code'] = $Result['stock_code'];
        $return[$count]['draw_DocNo'] = $Result['draw_DocNo'];
        $count++;
        $boolean = true;
      }
      $return['Row'] = $count;

      if ($boolean)
      {
        $return['sql'] = $Sql;
        $return['status'] = "success";
        $return['form'] = "showdetaildraw_rice";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      } 
      else 
      {
        $return['sql'] = $Sql;
        $return['status'] = "success";
        $return['form'] = "showdetaildraw_rice";
        echo json_encode($return);
        mysqli_close($conn);
        die;
      }

  }

  function approve($conn, $DATA)
  {
    $stock_code   = $DATA["stock_code"] ;
    $give   = $DATA["give"] ;
    $DocNo   = $DATA["DocNo"] ;
    $status   = $DATA["status"] ;
    $ID = $_SESSION['ID'];

    #========================================
    $stock_codex  = explode(",", $stock_code);
    $givex       = explode(",", $give);
    #========================================

    if($status ==2)
    {
      // Update status = 2
      $updateSTATUS = "UPDATE draw SET IsStatus = '$status' WHERE DocNo = '$DocNo'";
      mysqli_query($conn, $updateSTATUS);

      foreach ($givex as $key => $value)
      {
        $updateSTOCK="UPDATE stock_unprocess SET item_ccqty = (item_ccqty - $value) WHERE stock_code = '$stock_codex[$key]' ";
        mysqli_query($conn, $updateSTOCK);

        $updateDRAWSUB="UPDATE draw_detail_sub SET give = $value WHERE stock_code = '$stock_codex[$key]' AND draw_detail_sub.draw_DocNo = '$DocNo' ";
        mysqli_query($conn, $updateDRAWSUB);
      }

        // UPDATE ผลรวมเข้า Detail
        $sum = "SELECT
                  SUM( give ) AS givex,
                  item_code 
                FROM
                  draw_detail_sub 
                WHERE
                  draw_DocNo = '$DocNo' 
                GROUP BY
                  item_code ";
        $meQuery = mysqli_query($conn, $sum);
        while ($Result = mysqli_fetch_assoc($meQuery))
        {
          $givex          = $Result['givex'];
          $item_code      = $Result['item_code'];

          $updatedetail = " UPDATE draw_detail SET kilo = '$givex' WHERE item_code = '$item_code' AND draw_DocNo = '$DocNo' ";
          mysqli_query($conn, $updatedetail);
        }

        
        $updateuserAP = " UPDATE draw SET Approver_ID = '$ID' WHERE DocNo = '$DocNo' ";
        mysqli_query($conn, $updateuserAP);
    }
    else
    {
      // Update status = 9
      $updateSTATUS = "UPDATE draw SET IsStatus = '$status' WHERE DocNo = '$DocNo' ";
      mysqli_query($conn, $updateSTATUS);
    }
    ShowSearch($conn, $DATA);
  }

  function approve_rice($conn, $DATA)
  {
    $stock_code   = $DATA["stock_code"] ;
    $give   = $DATA["give"] ;
    $DocNo   = $DATA["DocNo"] ;
    $status   = $DATA["status"] ;
    $ID = $_SESSION['ID'];

    #========================================
    $stock_codex  = explode(",", $stock_code);
    $givex       = explode(",", $give);
    #========================================

    if($status ==2)
    {
      // Update status = 2
      $updateSTATUS = "UPDATE draw_rice SET IsStatus = '$status' WHERE DocNo = '$DocNo'";
      mysqli_query($conn, $updateSTATUS);

      foreach ($givex as $key => $value)
      {
        $updateSTOCK="UPDATE stock_unprocess SET item_ccqty = (item_ccqty - $value) WHERE stock_code = '$stock_codex[$key]' ";
        mysqli_query($conn, $updateSTOCK);

        $updateDRAWSUB="UPDATE draw_rice_detail_sub SET give = $value WHERE stock_code = '$stock_codex[$key]' AND draw_rice_detail_sub.draw_DocNo = '$DocNo' ";
        mysqli_query($conn, $updateDRAWSUB);
      }

        // UPDATE ผลรวมเข้า Detail
        $sum = "SELECT
                  SUM( give ) AS givex,
                  item_code 
                FROM
                draw_rice_detail_sub 
                WHERE
                  draw_DocNo = '$DocNo' 
                GROUP BY
                  item_code ";
        $meQuery = mysqli_query($conn, $sum);
        while ($Result = mysqli_fetch_assoc($meQuery))
        {
          $givex          = $Result['givex'];
          $item_code      = $Result['item_code'];

          $updatedetail = " UPDATE draw_rice_detail SET kilo = '$givex' WHERE item_code = '$item_code' AND draw_DocNo = '$DocNo' ";
          mysqli_query($conn, $updatedetail);
        }

        $updateuserAP = " UPDATE draw_rice SET Approver_ID = '$ID' WHERE DocNo = '$DocNo' ";
        mysqli_query($conn, $updateuserAP);
    }
    else
    {
      // Update status = 9
      $updateSTATUS = "UPDATE draw_rice SET IsStatus = '$status' WHERE DocNo = '$DocNo' ";
      mysqli_query($conn, $updateSTATUS);
    }
    ShowSearch_rice($conn, $DATA);
  }
//-----------------------------------------------------------------------

  $data = $_POST['DATA'];
  $DATA = json_decode(str_replace ('\"','"', $data), true);

      if ($DATA['STATUS'] == 'Showitem')
      {
        Showitem($conn, $DATA);
      }
      else if($DATA['STATUS'] == 'Showtype')
      {
        Showtype($conn, $DATA);
      } 
      else if($DATA['STATUS'] == 'ShowSearch')
      {
        ShowSearch($conn, $DATA);
      }
      else if($DATA['STATUS'] == 'ShowSearch_rice')
      {
        ShowSearch_rice($conn, $DATA);
      }  
      else if($DATA['STATUS'] == 'showdetaildraw')
      {
        showdetaildraw($conn, $DATA);
      }
      else if($DATA['STATUS'] == 'showdetaildraw_rice')
      {
        showdetaildraw_rice($conn, $DATA);
      }          
      else if($DATA['STATUS'] == 'approve')
      {
        approve($conn, $DATA);
      }
      else if($DATA['STATUS'] == 'approve_rice')
      {
        approve_rice($conn, $DATA);
      }       
    else
    {
        $return['status'] = "error";
        $return['msg'] = 'noinput';
        echo json_encode($return);
        mysqli_close($conn);
        die;
    }



















?>