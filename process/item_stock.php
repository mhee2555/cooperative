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
    else
    {
        $return['status'] = "error";
        $return['msg'] = 'noinput';
        echo json_encode($return);
        mysqli_close($conn);
        die;
    }



















?>