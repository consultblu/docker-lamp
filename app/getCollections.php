<?php
require('config.php');
require('jsonToCsv.php');
require('functions.php');

$zenoti = zenoti_ini('apis');

//echo("<br/>"); print_r($zenoti); echo("<br/><br/>");

$z = $zenoti["header"];
$params = $zenoti["params"];
$url = $zenoti["url"];
$tokenRequest = $zenoti["tokenRequest"];

$startDate = new DateTime($params["StartDate"]);
$endDate = new DateTime($params["EndDate"]);
$zenoti["params"]["StartDate2"] = $startDate->format('Y-m-d');
$zenoti["params"]["EndDate2"] = $endDate->format('Y-m-d');

/*echo("z => "); print_r($z); echo("<br/><br/>");
echo("params => "); print_r($params);echo("<br/><br/>");
echo("urls => "); print_r($url);echo("<br/><br/>");
echo("tokenRequest => "); print_r($tokenRequest);echo("<br/><br/>");*/

function getInventoryConsumption($zenoti, $c = null){
  $z = $zenoti["header"];
  $params = $zenoti["params"];
  $url = $zenoti["url"];

  $url = $url."v1/Collections/Center/".$c."/PurchaseOrders?StartDate=".$p["StartDate"]."&EndDate=".$p["EndDate"]."&ShowTaxBreakUp=1";
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //echo $json_response;
  if(!$response){
    die("Failed to get Inventory Consumption data.");
  }else{
    return $json_response;
  }

}

function getCenterCollection($zenoti,$p, $center){
  $h = $zenoti["header"];
  //$p = $zenoti["params"];
  $url = $zenoti["url"];

  $url = $url."v1/Collections/Center/".$center."?StartDate=".trim($p["StartDate"])."&EndDate=".trim($p["EndDate"])."&ShowTaxBreakUp=1";
  echo($url."<br>");
  $p["http_method"] = "GET";
  $response = json_decode(curlCall($url, $h, $p), true);
  //$json_response = json_decode($response);
  //echo("<div style='display:none;'>"); print_r($response); echo("</div>");
  if(!$response){
    die("Failed to get Collection data.");
  } else {
    if(!$response["CenterCollections"]){
      echo("Looks like this Center doesnt have any transactions as yet. Next! <br>");
    } else {
      return $response["CenterCollections"];
    }
  }
}



  array_push($zenoti["header"], "Authorization: " .getToken($zenoti));

  //print_r(getCenterList($z));
  $dateRange = getWeeks($params["StartDate"], $params["EndDate"]);
  $newcollection = [];
  foreach($dateRange as $week){

    $wk1Start = array_shift($week); //gives you first day of week 1
    $wk1End = array_pop($week); // give you the last day of week 1

    $wParams = [
      "StartDate" => $wk1Start,
      "EndDate" => $wk1End
    ];

    echo("Week of ".$wk1Start." through ".$wk1End ."<br>");

    $centerListArr = getCenterList($zenoti);
    $centerCount = count($centerListArr);
    echo("There are ".$centerCount." centers.<br>");

    for($x=0; $x < $centerCount; $x++){
      // code...
      $center = $centerListArr[$x];
      $collection = getCenterCollection($zenoti,$wParams,$center->Id);
      echo("<div style='display:none;'>"); print_r($collection); echo("</div>");

      echo($center->Name." has started [");

      if($collection){
        //print_r($collection);
        $centerName = $center->Name;
        $id = $center->Id;
        $centerCode = $center->CenterCode;
        $zone = $center->Zone;

        foreach($collection as &$row){
          $newRow = [];

          $newRow["CenterCode"] = $centerCode;
          $newRow["CenterName"] = $centerName;
          $newRow["Zone"] = $zone;
          $newRow["DT"] = $row["DT"];
          $newRow["GCODE"] = $row["GCODE"];
          $newRow["Guest"] = $row["Guest"];
          $newRow["guest_mobile"] = $row["GM"];
          $newRow["gender"] = $row["G"];
          $newRow["employee_code"] = $row["EC"];
          $newRow["EN"] = $row["EN"];
          $newRow["EJC"] = $row["EJC"];
          $newRow["I"] = $row["I"];
          $newRow["IC"] = $row["IC"];
          $newRow["SAC"] = $row["SAC"];
          $newRow["HSN"] = $row["HSN"];
          $newRow["IN"] = $row["IN"];
          $newRow["IT"] = $row["IT"];
          $newRow["BU"] = $row["BU"];
          $newRow["Cat"] = $row["Cat"];
          $newRow["SC"] = $row["SC"];
          $newRow["C"] = $row["C"];
          $newRow["CC"] = $row["CC"];
          $newRow["CHK"] = $row["CHK"];
          $newRow["CST"] = $row["CST"];
          $newRow["LP"] = $row["LP"];
          $newRow["GC"] = $row["GC"];
          $newRow["PPC"] = $row["PPC"];
          $newRow["TC"] = $row["TC"];
          $newRow["GIV"] = $row["GIV"];
          $newRow["INVD"] = $row["INVD"];
          $newRow["NIV"] = $row["NIV"];
          $newRow["INV"] = $row["INV"];
          $newRow["RCPT"] = $row["RCPT"];
          $newRow["PNO"] = $row["PNO"];
          $newRow["ICC"] = $row["ICC"];
          $newRow["STC"] = $row["STC"];
          $newRow["INVC"] = $row["INVC"];
          $newRow["IQTY"] = $row["IQTY"];
          $newRow["IBP"] = $row["IBP"];
          $newRow["IDT"] = $row["IDT"];
          $newRow["UP"] = $row["UP"];
          $newRow["IDSC"] = $row["IDSC"];
          $newRow["NP"] = $row["NP"];
          $newRow["TS"] = $row["TS"];
          $newRow["TT"] = $row["TT"];
          $newRow["RN"] = $row["RN"];
          $newRow["IRR"] = $row["IRR"];
          $newRow["GE"] = $row["GE"];
          $newRow["GMM"] = $row["GMM"];
          $newRow["IDF"] = $row["IDF"];
          $newRow["ID"] = $row["ID"];
          $newRow["SD"] = $row["SD"];
          $newRow["CenterId"] = $id;

          echo("<br> newRow => "); print_r($newRow); echo("<br>");

          array_push($newcollection, $newRow);
          echo(".");

          //$id = $db->insert ('collection_report', $newRow);

        }
        echo("] and is now complete. Next!<br>");
      }else{
        echo("], however, It's empty. Next!<br>");
      }
    }

//jsonToCsv($newcollection,"Report-Collections-by-Center-".$wParams["StartDate"]."-".$wParams["EndDate"].".csv");
  }

jsonToCsv($newcollection,"Report-Collections-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");
