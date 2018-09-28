<?php
require('config.php');
require('jsonToCsv.php');
require('functions.php');

$zenoti = zenoti_ini('apis');

//print_r($zenoti);

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

function getAppointments($zenoti, $p = null, $c = null) {
  $h = $zenoti["header"];
  #$p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = $p["StartDate"];
  $endDate = $p["EndDate"];

  $url = "https://apis.zenoti.com/v1/Appointments?CenterId=" . $c . "&StartDate=".$p["StartDate"]."&EndDate=".$p["EndDate"]."&Status=1";
  //echo($url."<br>");
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //print_r($json_response);
  if(!$response){
    die("Failed to get Inventory Consumption data.");
  }else{
    return $json_response;
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
      "StartDate" => $wk1Start . " T00:00:00.000Z",
      "EndDate" => $wk1End . " T23:59:59.999Z"
    ];

    echo("Week of ".$wk1Start." through ".$wk1End ."<br>");

    $centerListArr = getCenterList($z);
    $centerCount = count($centerListArr);
    //echo("There are ".$centerCount." centers.<br>");

    for($x=0; $x < $centerCount; $x++){
      // code...
      $center = $centerListArr[$x];
      $collection = getAppointments($zenoti,$wParams,$center->Id);

      echo($center->Name." has started [");

      if($collection){
        //print_r($collection->Appointments);
        $name = $center->Name;
        $id = $center->Id;
        $centerCode = $center->Code;
        $zone = $center->Zone;

        foreach($collection->Appointments as &$row){
          $newRow = [];
          $newRow["CenterCode"] = $centerCode;
          $newRow["CenterName"] = $name;
          $newRow["Zone"] = $zone;
          $newRow["GI"] = $row->GI;
          $newRow["Inv"] = $row->Inv;
          $newRow["AI"] = $row->AI;
          $newRow["SI"] = $row->SI;
          $newRow["SN"] = $row->SN;
          $newRow["ST"] = $row->ST;
          $newRow["ET"] = $row->ET;
          $newRow["Sta"] = $row->Sta;
          $newRow["AS"] = $row->AS;
          $newRow["IL"] = $row->IL;
          $newRow["GuI"] = $row->GuI;
          $newRow["GFN"] = $row->GFN;
          $newRow["GLN"] = $row->GLN;
          $newRow["GP"] = $row->GP;
          $newRow["EMAIL"] = $row->EMAIL;
          $newRow["GID"] = $row->GID;
          $newRow["EI"] = $row->EI;
          $newRow["EFN"] = $row->EFN;
          $newRow["ELN"] = $row->ELN;
          $newRow["EG"] = $row->EG;
          $newRow["Cat"] = $row->Cat;
          $newRow["SC"] = $row->SC;
          $newRow["SCD"] = $row->SCD;
          $newRow["ASI"] = $row->ASI;
          $newRow["Not"] = $row->Not;
          $newRow["Price_CurrencyId"] = $row->Price->CurrencyId;
          $newRow["Price_Sales"] = $row->Price->Sales;
          $newRow["Price_Tax"] = $row->Price->Tax;
          $newRow["Price_Final"] = $row->Price->Final;
          $newRow["Price_Final1"] = $row->Price->Final1;
          $newRow["Price_Discount"] = $row->Price->Discount;
          $newRow["Price_Tip"] = $row->Price->Tip;
          $newRow["Price_SSG"] = $row->Price->SSG;
          $newRow["Price_RoundingCorrection"] = $row->Price->RoundingCorrection;
          $newRow["Price_DiscountedPrice"] = $row->Price->DiscountedPrice;
          $newRow["GPC"] = $row->GPC;
          $newRow["EVI"] = $row->EVI;
          $newRow["Pro"] = $row->Pro;
          $newRow["AST"] = $row->AST;
          $newRow["ACT"] = $row->ACT;
          $newRow["CT"] = $row->CT;
          $newRow["TPT"] = $row->TPT;
          $newRow["FI"] = $row->FI;
          $newRow["IA"] = $row->IA;
          $newRow["ViewOnly"] = $row->ViewOnly;
          $newRow["CenterId"] = $id;

          //$id = $db->insert ('sales_appointments', $newRow);
          //print_r($newRow);
          //echo("<br>");
          array_push($newcollection, $newRow);
          echo(".");

        }
        echo("] and is now complete. Next!<br>");
      }else{
        echo("], however, It's empty. Next!<br>");
      }
    }
  }

jsonToCsv($newcollection,"Report-Appointments-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");
