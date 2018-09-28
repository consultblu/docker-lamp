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

function getLeadsCount($zenoti){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = $p["StartDate"];
  $endDate = $p["EndDate"];

  $url = "https://apis.zenoti.com/v2/opportunities?view_id=5be22857-1003-40c5-a518-35b093361fd4&creation_from_date=" . $startDate . "&creation_to_date=" . $endDate;
  //echo $url ."<br>";
  //echo "<br> getLeadCount header => ";print_r($h); echo $url ."<br>";
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //print_r($json_response);
  if(!$response){
    die("Failed to get Zenoti Opportunity data.");
  }else{
    return $json_response;
  }

}

function getLeads($zenoti){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];
  $page = 1;

  $startDate = $p["StartDate"];
  $endDate = $p["EndDate"];

  $url = "https://apis.zenoti.com/v2/opportunities?view_id=5be22857-1003-40c5-a518-35b093361fd4&page_num=" . $page . "&records=50&creation_from_date=" . $startDate . "&creation_to_date=" . $endDate;
  //echo $url ."<br>";
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //echo $json_response;
  if(!$response){
    die("Failed to get Zenoti Opporttunity data.");
  }else{
    return $json_response;
  }
}

function getOpportunityDetail($zenoti, $id = null){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = $p["StartDate2"];
  $endDate = $p["EndDate2"];

  $url = "https://apis.zenoti.com/v2/Opportunities/" . $id;
  //echo $url ."<br>";
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //print_r($json_response);
  if(!$response){
    die("Failed to get Zenoti Opporttunity data.");
  }else{
    return $json_response;
  }
}

function getGuestDetail($zenoti, $userCode = null){
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $startDate = $p["StartDate2"];
  $endDate = $p["EndDate2"];

  $url = "https://apis.zenoti.com/v1/Guests?Count=1&SearchValue=" . $userCode;
  //echo $url ."<br>";
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  //echo $json_response;
  if(!$response){
    die("Failed to get Zenoti Opporttunity data.");
  }else{
    return $json_response;
  }
}


array_push($zenoti["header"], "Authorization: ".getToken($zenoti));

//print_r(getCenterList($z));
$dateRange = getWeeks($params["StartDate"], $params["EndDate"]);
$leadReport = [];

$leadsCounts = getLeadsCount($zenoti);
//print_r($leadsCounts);

$totalRecords = $leadsCounts->total_records;
$pages = ceil($totalRecords / 50);
echo("pages = ".$pages."<br>");

for($p = 0; $p <= $pages; $p++){
  echo("Page of ".$p." of ".$pages ."<br>");
  $leads = getLeads($zenoti, $p);
  //print_r($leads);

  foreach($leads->opportunities as &$row){
    //echo("<br><br> row =>"); print_r($row->opportunity_id);echo("<br><br>");
    $opportunity_id = $row->opportunity_id;
    $newRow = [];
    $newRow["opportunity_id"] = $opportunity_id;
    $newRow["opportunity_no"] = $row->opportunity_no;
    $newRow["opportunity_title"] = $row->opportunity_title;
    $newRow["center_name"] = $row->center_name;
    $newRow["sales_owner"] = $row->sales_owner;
    $newRow["stage_name"] = $row->stage_name;
    $newRow["creation_date"] = $row->creation_date;
    $newRow["followup_date"] = $row->followup_date;
    $newRow["price"] = $row->price;
    $newRow["call_status"] = $row->call_status;

    $newRow["guest_id"] = $row->guest->guest_id;
    $newRow["guest_name"] = $row->guest->guest_name;
    $newRow["guest_code"] = $row->guest->guest_code;
    $newRow["guest_phone_number"] = $row->guest->phone_number;

    $gD = getGuestDetail($zenoti, $row->guest->guest_code);
    $gD = $gD->Guests[0];
      //echo("<br><br>"); print_r($gD);echo("<br><br>");
      //echo("<br><br>"); print_r($gD);echo("<br><br>");
      $newRow["guest_FirstName"] = $gD->FirstName;
      $newRow["guest_MiddleName"] = $gD->MiddleName;
      $newRow["guest_LastName"] = $gD->LastName;
      $newRow["guest_Email"] = $gD->Email;
      $newRow["guest_MobileNumber"] = $gD->MobileNumber;
      $newRow["guest_ReferralSource"] = $gD->ReferralSource;
      $newRow["guest_ReferredGuestId"] = $gD->ReferredGuestId;

    $oD =  getOpportunityDetail($zenoti,$opportunity_id);
    $oD = $oD->oppportunity;
      //echo("<br><br>"); print_r($oD);echo("<br><br>");
      //echo("<br><br>oD => "); print_r($oD);echo("<br><br>");
      //$newRow["opportunity_id"] = $oD->opportunity_id;
      //$newRow["opportunity_title"] = $oD->opportunity_title;
      $newRow["opportunity_description"] = $oD->opportunity_description;
      $newRow["opportunity_user_id"] = $oD->opportunity_user_id;
      $newRow["opportunity_owner_id"] = $oD->opportunity_owner_id;
      $newRow["opportunity_type"] = $oD->opportunity_type;
      //$newRow["opportunity_no"] = $oD->opportunity_no;
      $newRow["lead_source_code"] = $oD->lead_source->code;
      $newRow["lead_source_id"] = $oD->lead_source->id;
      $newRow["lead_source_name"] = $oD->lead_source->name;
      $newRow["creation_date"] = $oD->creation_date;
      $newRow["expected_close_date"] = $oD->expected_close_date;
      $newRow["followup_date"] = $oD->followup_date;
      $newRow["created_by"] = $oD->created_by;
      $newRow["sales_owner"] = $oD->sales_owner;
      $newRow["therapist_id"] = $oD->therapist_id;
      $newRow["ticket_id"] = $oD->ticket_id;
      //"custom_fields": [],
      $newRow["optional_optional_field_1"] = $oD->optional->optional_field_1;
      $newRow["optional_optional_field_2"] = $oD->optional->optional_field_2;
      $newRow["optional_optional_field_3"] = $oD->optional->optional_field_3;
      $newRow["price_listed_price"] = $oD->price->listed_price;
      $newRow["price_listed_price"] = $oD->price->listed_price;
      $newRow["stage_stage_name"] = $oD->stage->stage_name;
      $newRow["stage_stage_id"] = $oD->stage->stage_id;
      $newRow["priority_priority_name"] = $oD->priority->priority_name;
      $newRow["priority_priority_id"] = $oD->priority->priority_id;
      $newRow["guest_guest_id"] = $oD->guest->guest_id;
      $newRow["guest_guest_name"] = $oD->guest->guest_name;
      $newRow["center_center_id"] = $oD->center->center_id;
      $newRow["center_center_name"] = $oD->center->center_name;
      $newRow["disposition_id"] = $oD->disposition->id;
      $newRow["disposition_name"] = $oD->disposition->name;

    //print_r($newRow);
    //echo("<br>");
    //$id = $db->insert ('sales_opportunities', $newRow);
    array_push($leadReport, $newRow);
    echo(".");
  }
}
jsonToCsv($leadReport,"Report-Opportunities-by-Center-".$startDate->format('Y-m-d')."-".$endDate->format('Y-m-d').".csv");
