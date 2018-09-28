<?php
require('config.php');
require('jsonToCsv.php');
require('functions.php');

$zenoti = zenoti_ini('api');

//echo("<br/>"); print_r($zenoti); echo("<br/><br/>");

$z = $zenoti["header"];
$params = $zenoti["params"];
$url = $zenoti["url"];
$tokenRequest = $zenoti["tokenRequest"];

$startDate = new DateTime($params["StartDate"]);
$endDate = new DateTime($params["EndDate"]);
$zenoti["params"]["StartDate2"] = $startDate->format('Y-m-d');
$zenoti["params"]["EndDate2"] = $endDate->format('Y-m-d');
echo("z => "); print_r($z); echo("<br/><br/>");
echo("params => "); print_r($params);echo("<br/><br/>");
echo("urls => "); print_r($url);echo("<br/><br/>");
echo("tokenRequest => "); print_r($tokenRequest);echo("<br/><br/>");

function getInventoryConsumption($zenoti, $center){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = new DateTime($p["StartDate"]);
  $endDate = new DateTime($p["StartDate"]);

  $url = $url."v1/inventory/".$center."/getInventoryConsumption?StartDate=".$p["StartDate2"]."&EndDate=".$p["StartDate2"]."&ReportType=1&ValueType=1&ProductCode=";
  echo($url."<br><br>");
  $p["http_method"] = "GET";
  $response = json_decode(curlCall($url, $h, $p), true);
  //$json_response = json_decode($response);
  //print_r($response);
  if(!$response){
    die("Failed to get Collection data.");
  } else {
    if(!$response["list"]){
      echo("Looks like this Center doesnt have any transactions as yet. Next! <br>");
    } else {
      return $response["list"];
    }
  }
}


$zenoti["header"][1] = "Authorization: bearer ".getToken($zenoti);
//echo("<br><br> API Authorization => ");print_r($zenoti["header"]["Authorization"]);echo("<br><br>");
//echo("<br><br> API Authorization => ");print_r($zenoti["header"]);echo("<br><br>");

//array_push($zenoti["header"], $zenoti["AuthorizationStr"] .getToken($zenoti));

//print_r(getCenterList($z));
$rangeStart = $startDate->format('Y-m-d');
$rangeEnd = $endDate->format('Y-m-d');

$period = getDays($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

foreach($period as $date) {$dateRange[] = $date->format('Y-m-d');}
$newcollection = [];
//echo("<br><br> dateRange => "); print_r($dateRange); echo("</div>");

  foreach($dateRange as $day){
    $wParams = [
      "StartDate" => $day,
      "EndDate" => $day
    ];

    echo("Report for ".$day->date."<br>");

    $centerListArr = getCenterList($zenoti);
    $centerCount = count($centerListArr);
    echo("There are ".$centerCount." centers.<br>");

    for($x=0; $x < $centerCount; $x++){
      // code...
      $center = $centerListArr[$x];
      $collection = getInventoryConsumption($zenoti,$center->Id);

      echo($center->Name." has started [");

      if($collection){
        //print_r($collection);
        $name = $center->Name;
        $id = $center->Id;
        $CenterCode = $center->Code;
        $zone = $center->Zone;

        foreach($collection as &$row){
          $newRow = [];

          $newRow["date"] = $day;
          $newRow["center_code"] = $row["center_code"];
          $newRow["center_name"] = $row["center_name"];
          $newRow["product_code"] = $row["product_code"];
          $newRow["product_name"] = $row["product_name"];
          $newRow["unit_name"] = $row["unit_name"];
          $newRow["category"] = $row["category"];
          $newRow["sub_category"] = $row["sub_category"];
          $newRow["initial_store"] = $row["initial_store"];
          $newRow["initial_floor"] = $row["initial_floor"];
          $newRow["opening_value"] = $row["opening_value"];
          $newRow["qty_delivered_in_orders"] = $row["qty_delivered_in_orders"];
          $newRow["qty_delivered_in_transfers"] = $row["qty_delivered_in_transfers"];
          $newRow["conv_in"] = $row["conv_in"];
          $newRow["qty_return_in_po"] = $row["qty_return_in_po"];
          $newRow["qty_return"] = $row["qty_return"];
          $newRow["checkout_qty"] = $row["checkout_qty"];
          $newRow["qty_outpo"] = $row["qty_outpo"];
          $newRow["qty_out_transfers"] = $row["qty_out_transfers"];
          $newRow["qty_sold"] = $row["qty_sold"];
          $newRow["qty_consumed"] = $row["qty_consumed"];
          $newRow["conv_out"] = $row["conv_out"];
          $newRow["qty_in_transit_outward"] = $row["qty_in_transit_outward"];
          $newRow["qty_return_out_PO"] = $row["qty_return_out_PO"];
          $newRow["avg_unitvalue_consumed"] = $row["avg_unitvalue_consumed"];
          $newRow["closing_store"] = $row["closing_store"];
          $newRow["closing_floor"] = $row["closing_floor"];
          $newRow["closing_value"] = $row["closing_value"];
          $newRow["actual_used_qty"] = $row["actual_used_qty"];
          $newRow["projected_used_qty"] = $row["projected_used_qty"];
          $newRow["total_audits"] = $row["total_audits"];
          $newRow["unaccounted_store"] = $row["unaccounted_store"];
          $newRow["unaccounted_floor"] = $row["unaccounted_floor"];
          $newRow["manual_checkouts"] = $row["manual_checkouts"];
          $newRow["pos_returns_not_restocked"] = $row["pos_returns_not_restocked"];

          echo("<br><br> Consumption Report => "); print_r($newRow);echo("<br><br>");

          array_push($newcollection, $newRow);
          echo(".");
        }
        echo("] and is now complete. Next!<br>");
      }else{
        echo("], however, It's empty. Next!<br>");
      }
    }

//jsonToCsv($newcollection,"Report-Collections-by-Center-".$wParams["StartDate"]."-".$wParams["EndDate"].".csv");
  }

jsonToCsv($newcollection,"Report-ConsumptionReport-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");



/*
CREATE TABLE mytable(
   center_code    VARCHAR(2) NOT NULL PRIMARY KEY
  ,center_name    VARCHAR(9) NOT NULL
  ,product_code   VARCHAR(17) NOT NULL
  ,product_name   VARCHAR(44) NOT NULL
  ,category       VARCHAR(17) NOT NULL
  ,sub_category   VARCHAR(17) NOT NULL
  ,quantity       INTEGER  NOT NULL
  ,store_quantity INTEGER  NOT NULL
  ,store_value    INTEGER  NOT NULL
  ,store_tax_paid BIT  NOT NULL
  ,floor_quantity INTEGER  NOT NULL
  ,floor_value    INTEGER  NOT NULL
  ,floor_tax_paid BIT  NOT NULL
  ,product_type   VARCHAR(6) NOT NULL
);
INSERT INTO mytable(center_code,center_name,product_code,product_name,category,sub_category,quantity,store_quantity,store_value,store_tax_paid,floor_quantity,floor_value,floor_tax_paid,product_type) VALUES ('WD','Woodlands','qs-ch-bbq','BBQ Chips','Q-Snacks','Chips',109,110,77,0,-1,-1,0,'Retail');
*/


/*CREATE TABLE mytable(
   center_code                VARCHAR(2) NOT NULL PRIMARY KEY
  ,center_name                VARCHAR(9) NOT NULL
  ,product_code               VARCHAR(17) NOT NULL
  ,product_name               VARCHAR(48) NOT NULL
  ,unit_name                  VARCHAR(7) NOT NULL
  ,category                   VARCHAR(17) NOT NULL
  ,sub_category               VARCHAR(17) NOT NULL
  ,initial_store              INTEGER  NOT NULL
  ,initial_floor              BIT  NOT NULL
  ,opening_value              INTEGER  NOT NULL
  ,qty_delivered_in_orders    INTEGER  NOT NULL
  ,qty_delivered_in_transfers BIT  NOT NULL
  ,conv_in                    BIT  NOT NULL
  ,qty_return_in_po           BIT  NOT NULL
  ,qty_return                 BIT  NOT NULL
  ,checkout_qty               BIT  NOT NULL
  ,qty_outpo                  BIT  NOT NULL
  ,qty_out_transfers          BIT  NOT NULL
  ,qty_sold                   BIT  NOT NULL
  ,qty_consumed               BIT  NOT NULL
  ,conv_out                   BIT  NOT NULL
  ,qty_in_transit_outward     BIT  NOT NULL
  ,qty_return_out_PO          BIT  NOT NULL
  ,avg_unitvalue_consumed     INTEGER  NOT NULL
  ,closing_store              INTEGER  NOT NULL
  ,closing_floor              BIT  NOT NULL
  ,closing_value              INTEGER  NOT NULL
  ,actual_used_qty            INTEGER  NOT NULL
  ,projected_used_qty         BIT  NOT NULL
  ,total_audits               INTEGER  NOT NULL
  ,unaccounted_store          INTEGER  NOT NULL
  ,unaccounted_floor          INTEGER  NOT NULL
  ,manual_checkouts           BIT  NOT NULL
  ,pos_returns_not_restocked  BIT  NOT NULL
);*/
