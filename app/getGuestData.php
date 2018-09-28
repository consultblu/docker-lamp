<?php

require('config.php');
require('jsonToCsv.php');
require('functions.php');

$zenoti = zenoti_ini('apis');

print_r($zenoti);

$z = $zenoti["header"];
$params = $zenoti["params"];
$url = $zenoti["url"];
$tokenRequest = $zenoti["tokenRequest"];


function searchGuests($zenoti,$e) {
  $h = $zenoti["header"];
  $p = $zenoti["params"];
  $url = $zenoti["url"];

  $url = $url.'v1/Guests?Count=1&SearchValue=' .$e;
  echo($url."<br>");
  $p["http_method"] = "GET";
  $response = curlCall($url, $h, $p);
  $json_response = json_decode($response);
  print_r($json_response);
  if(!$response){
    die("Failed to get Inventory Consumption data.");
  }else{
    return $json_response;
  }

}

array_push($zenoti["header"], "Authorization: " .getToken($zenoti));

$leads = getLeadsFromCSV();
echo("leads => <div style='display:none;'>");var_dump($leads);echo("</div><br>");
$leadCount = count($leads);
foreach($leads as $lead){
//echo("lead[0] => " .$leads[0]["email"]);
//searchGuests($zenoti, $leads[0]["email"]);


//for($x=0; $x <= 10; $x++){
  echo("<br>"); print_r($lead["email"]); echo("<br>");

    searchGuests($zenoti, $lead["email"]);

}

/*
var dataIn = {
  authorization: inputData.authorization,
  CenterId: inputData.CenterId,
  leadName: inputData.leadName,
  FirstName: inputData.FirstName,
  LastName: inputData.LastName,
  Email: inputData.Email,
  MobileNumber: inputData.MobileNumber
};

const headers = {
  'content-type': 'application/json',
  'accept': 'application/json',
  'apikey': 'SbGEFb0bLo0RwDQg5SWQ2rpouz86TUQD',
  'authorization': inputData.authorization,
  'cache-control': 'no-cache',
  'postman-token': '97aeacda-54f0-d67a-0a28-b42a34b4ed4f'
 };

/*var guestApiUrl = 'https://apis.zenoti.com/v5/guests?count=1&CenterId=' + inputData.CenterId + '&searchValue=' + dataIn.Email.trim();*/
/*
var guestApiUrl = 'https://apis.zenoti.com/v5/guests?count=1&search_value=' + dataIn.Email.trim();

  var res = await fetch(guestApiUrl, {
    method: 'GET',
    headers: headers,
  });
  var body = await res.text();
  var guestObj = JSON.parse(body.toString());
  console.log('guestObj = ', guestObj);

  try{
    console.log('Guest[0] = ', guestObj.guests[0]);
    console.log('Guest[0].Id = ', guestObj.guests[0].id);
    output = {id: guestObj.guests[0].id, customerId: guestObj.guests[0].code}
  }
  catch(error) {
    console.error(error);
    output = {id: 'false', customerId: 'false'}
  }

//output = {id: 1, hello: await Promise.resolve("world")};
*/
