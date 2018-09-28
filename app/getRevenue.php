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

function getCenterRevenue($zenoti,$p, $center){
  $h = $zenoti["header"];
  //$p = $zenoti["params"];
  $url = $zenoti["url"];

  $url = $url."v1/Revenues/InvoiceItem/".$center."?StartDate=".trim($p["StartDate"])."&EndDate=".trim($p["EndDate"])."&IncludeExpiredPackageRevenue=1&ConsiderRefunds=1";
  echo($url."<br>");
  $p["http_method"] = "GET";
  $response = json_decode(curlCall($url, $h, $p), true);
  //$json_response = json_decode($response);
  //echo("<div style='display:none;'>"); print_r($response); echo("</div>");
  if(!$response){
    echo("<br>Failed to get Collection data.</br>");
    return false;
  } else {
    return $response;
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
      $revenue = getCenterRevenue($zenoti,$wParams,$center->Id);
      //echo("<div style='display:none;'>"); print_r($revenue); echo("</div>");

      echo($center->Name." has started [");

      if($revenue){
        //print_r($collection);
        $centerName = $revenue["CenterName"];
        $id = $center->Id;
        $centerCode = $revenue["CenterCode"];
        $zone = $center->Zone;

        foreach($revenue["InvoiceItems"] as $row){
          $newRow = [];

          $newRow["CenterCode"] = $centerCode;
          $newRow["CenterName"] = $centerName;
          $newRow["Zone"] = $zone;

          $newRow["GC"] = $row["GC"]; // string Guest Code
          $newRow["GuestName"] = $row["Guest"]; // string Guest Name
          $newRow["Gender"] = $row["Gender"]; // enum Gender
          $newRow["InvNum"] = $row["InvNum"]; // string Invoice Number
          $newRow["ID"] = $row["ID"]; // string Invoice Date
          $newRow["CD"] = $row["CD"]; // string Closed Date
          $newRow["IN"] = $row["IN"]; // Item Name
          $newRow["IT"] = $row["IT"]; // Item Type
          $newRow["SAC"] = $row["SAC"];
          $newRow["HSN"] = $row["HSN"];
          $newRow["Cat"] = $row["Cat"]; // Category
          $newRow["SC"] = $row["SC"]; // Sub-Caegorty
          $newRow["BU_N"] = $row["BU"]["N"];
          $newRow["BU_I"] = $row["BU"]["I"];
          $newRow["Qty"] = $row["Qty"]; // Quantity
          $newRow["MRD"] = json_encode($row["PRD"]);
          $newRow["PRD_C"] = $row["PRD"][0]["C"];
          $newRow["PRD_N"] = $row["PRD"][0]["N"];
          $newRow["PRD_CR"] = $row["PRD"][0]["CR"];
          $newRow["PRD_InvNO"] = $row["PRD"][0]["InvNO"];
          $newRow["PRD_PD"] = $row["PRD"][0]["PD"];
          $newRow["MRD"] = json_encode($row["MRD"]);
          $newRow["GCRD_C"] = $row["GCRD"][0]["C"];
          $newRow["GCRD_N"] = $row["GCRD"][0]["N"];
          $newRow["GCRD_CR"] = $row["GCRD"][0]["CR"];
          $newRow["GCRD_InvNO"] = $row["GCRD"][0]["InvNO"];
          $newRow["GCRD_PD"] = $row["GCRD"][0]["PD"];
          $newRow["PPCRD"] = json_encode($row["PPCRD"]);
          $newRow["CR"] = $row["CR"]; // Cash Revenue
          $newRow["MRR"] = $row["MRR"]; // Membership Redemption Revenue
          $newRow["MSRR"] = $row["MSRR"]; // Membership Service Redemption Revenue
          $newRow["MIRR"] = $row["MIRR"]; // Membership Initial Recognized Revenue
          $newRow["MMRR"] = $row["MMRR"]; // Membership Monthly Recognized Revenue
          $newRow["PIRR"] = $row["PIRR"]; // Package Initial Recognition Revenue
          $newRow["PRR"] = $row["PRR"]; // Package Redemption Revenue
          $newRow["GCR"] = $row["GCR"]; // GiftCard Revenue
          $newRow["RFEP"] = $row["RFEP"]; // Revenue From Expired Packages
          $newRow["PPCR"] = $row["PPCR"]; // Prepaid Card Revenue
          $newRow["TR"] = $row["TR"]; // Total Revenue
          $newRow["STC"] = $row["STC"];
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

jsonToCsv($newcollection,"Report-Revenue-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");
