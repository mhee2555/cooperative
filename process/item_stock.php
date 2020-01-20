<?php
session_start();
require '../connect/connect.php';

  function Showitem($conn, $DATA)
  {
    $item_type = $DATA["item_type"];
    $item_type2 = $DATA["item_type2"];
    $count = 0;
    $table ='';
    if($item_type ==1) 
    {
      $table = 'item_stockproduct';
    }
    else
    {
      $table = 'item_stockfinished';
    }
    
    
        $Showitem = "SELECT
                                  item.item_name,
                                  $table.item_code,
                                  $table.item_qty,
                                  $table.Lot,
                                  $table.item_type,
                                  type_item.type_name,
                                  item_unit.UnitName
                                FROM
                                  $table
                                INNER JOIN item ON $table.item_code = item.item_code
                                INNER JOIN type_item ON item.item_type = type_item.id
                                INNER JOIN item_unit ON $table.item_unit = item_unit.UnitCode
                                WHERE $table.item_type LIKE '$item_type2'
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
        $return[$count]['Lot']          = $Result['Lot'];
        $return[$count]['item_type']          = $Result['item_type'];
        $return[$count]['type_name']          = $Result['type_name'];
        $return[$count]['UnitName']          = $Result['UnitName'];

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
    $item_type = $DATA["item_type"];
    $count=0;
    $Showtype = "SELECT 
                  type_item.id,
                  type_item.type_name 
                 FROM 
                  type_item
                 WHERE
                  type_item.item_type_main= '$item_type' ";
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