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

function getPurchaseOrders($zenoti, $center){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = new DateTime($p["StartDate"]);
  $endDate = new DateTime($p["StartDate"]);

  $url = $url."v1/inventory/".$center."/PurchaseOrders?StartDate=".$p["StartDate2"]."&EndDate=".$p["EndDate2"]."&ShowDeliveryDetails=1&DateCriteria=1";
  echo($url."<br><br>");
  $p["http_method"] = "GET";
  $response = json_decode(curlCall($url, $h, $p), true);
  //$json_response = json_decode($response);
  //print_r($response);
  if(!$response){
    die("Failed to get Collection data.");
  } else {
    if(!$response["PurchaseOrders"]){
      echo("Looks like this Center doesnt have any transactions as yet. Next! <br>");
    } else {
      return $response["PurchaseOrders"];
    }
  }
}


  $zenoti["header"][1] = "Authorization: bearer ".getToken($zenoti);
  //echo("<br><br> API Authorization => ");print_r($zenoti["header"]["Authorization"]);echo("<br><br>");
  //echo("<br><br> API Authorization => ");print_r($zenoti["header"]);echo("<br><br>");

  //array_push($zenoti["header"], $zenoti["AuthorizationStr"] .getToken($zenoti));

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
      $collection = getPurchaseOrders($zenoti,$center->Id);

      echo($center->Name." has started [");

      if($collection){
        //print_r($collection);
        $name = $center->Name;
        $id = $center->Id;
        $CenterCode = $center->Code;
        $zone = $center->Zone;

        foreach($collection as $row){
          echo("<br><br>"); print_r($row);echo("<br><br>");
          $newRow["CenterCode"] = $centerCode;
          $newRow["CenterName"] = $name;
          $newRow["Zone"] = $zone;
          $RN = $row["RN"];
          $OD = $row["OD"];
          $RD = $row["RD"];
          $Status = $row["Status"];
          $CD = $row["CD"];
          $VC = $row["VC"];
          $VN = $row["VN"];
          $CST = $row["CST"];
          $TIN = $row["TIN"];
          $InvNum = $row["InvNum"];

          echo("<br><br> Partials => "); var_dump($row["Partials"]);echo("<br><br>");

            foreach($row["Partials"] as $partial){
              $Partials_DD = $partial["DD"];
              $Partials_DV = $partial["DV"];
              $Partials_SAH = $partial["SAH"];
              $Partials_Other = $partial["Other"];
              $Partials_InvNum = $partial["InvNum"];
              $Partials_Notes = $partial["Notes"];

              foreach($partial["Items"] as $Item){

                $newRow["RN"] = $RN;
                $newRow["OD"] = $OD;
                $newRow["RD"] = $RD;
                $newRow["Status"] = $Status;
                $newRow["CD"] = $CD;
                $newRow["VC"] = $VC;
                $newRow["VN"] = $VN;
                $newRow["CST"] = $CST;
                $newRow["TIN"] = $TIN;
                $newRow["InvNum"] = $InvNum;

                $newRow["Partials_DD"] = $Partials_DD;
                $newRow["Partials_DV"] = $Partials_DV;
                $newRow["Partials_SAH"] = $Partials_SAH;
                $newRow["Partials_Other"] = $Partials_Other;
                $newRow["Partials_InvNum"] = $Partials_InvNum;
                $newRow["Partials_Notes"] = $Partials_Notes;

                $newRow["Partials_Items_PC"] = $Item["PC"];
                $newRow["Partials_Items_HSN"] = $Item["HSN"];
                $newRow["Partials_Items_PN"] = $Item["PN"];
                $newRow["Partials_Items_MRP"] = $Item["MRP"];
                $newRow["Partials_Items_RQO"] = $Item["RQO"];
                $newRow["Partials_Items_CQO"] = $Item["CQO"];
                $newRow["Partials_Items_UPO"] = $Item["UPO"];
                $newRow["Partials_Items_OTB"] = json_encode($Item["OTB"]);
                $newRow["Partials_Items_OTN"] = $Item["OTN"];
                $newRow["Partials_Items_OT"] = $Item["OT"];
                $newRow["Partials_Items_RQD"] = $Item["RQD"];
                $newRow["Partials_Items_CQD"] = $Item["CQD"];
                $newRow["Partials_Items_UPD"] = $Item["UPD"];
                $newRow["Partials_Items_DTD"] = $Item["DTD"];
                $newRow["Partials_Items_DVD"] = $Item["DVD"];
                $newRow["Partials_Items_DT"] = $Item["DT"];
                $newRow["Partials_Items_DTB"] = json_encode($Item["DTB"]);
                $newRow["Partials_Items_DTN"] = $Item["DTN"];
                $newRow["Partials_Items_Notes"] = $Item["Notes"];
              }
            }
            $newRow["CenterId"] = $id;


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

jsonToCsv($newcollection,"Report-PurchaseOrder-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");
