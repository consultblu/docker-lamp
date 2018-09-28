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

function getValueReport($zenoti, $center){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = new DateTime($p["StartDate"]);
  $endDate = new DateTime($p["StartDate"]);

  $url = $url."v1/inventory/".$center."/getInventoryValue?InventoryDate=".$p["StartDate2"]."&ProductType=3&SearchString=&ValueType=1";
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

    echo("Week of ".$wk1Start." through ".$wk1End ."<br>");

    $centerListArr = getCenterList($zenoti);
    $centerCount = count($centerListArr);
    echo("There are ".$centerCount." centers.<br>");

    for($x=0; $x < $centerCount; $x++){
      // code...
      $center = $centerListArr[$x];
      $collection = getValueReport($zenoti,$center->Id);

      echo($center->Name." has started [");

      if($collection){
        //print_r($collection);
        $name = $center->Name;
        $id = $center->Id;

        foreach($collection as &$row){
          $newRow = [];

          $newRow["date"] = $day;
          $newRow["center_code"] = $row["center_code"];
          $newRow["center_name"] = $row["center_name"];
          $newRow["product_code"] = $row["product_code"];
          $newRow["product_name"] = $row["product_name"];
          $newRow["category"] = $row["category"];
          $newRow["sub_category"] = $row["sub_category"];
          $newRow["quantity"] = $row["quantity"];
          $newRow["store_quantity"] = $row["store_quantity"];
          $newRow["store_value"] = $row["store_value"];
          $newRow["store_tax_paid"] = $row["store_tax_paid"];
          $newRow["floor_quantity"] = $row["floor_quantity"];
          $newRow["floor_value"] = $row["floor_value"];
          $newRow["floor_tax_paid"] = $row["floor_tax_paid"];
          $newRow["product_type"] = $row["product_type"];

          echo("<br><br> Report Value => "); print_r($newRow);echo("<br><br>");

          array_push($newcollection, $newRow);
          echo(".");
        }
        echo("] and is now complete. Next!<br>");
      }else{
        echo("], however, It's empty. Next!<br>");
      }
    }

//jsonToCsv($newcollection,"Report-Value-by-Center-".$startDate->format('Y-m-d').".csv");
  }

jsonToCsv($newcollection,"Report-Value-by-Center-".$rangeStart."-".$rangeEnd.".csv");



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
