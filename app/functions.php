<?php
require_once ('MysqliDb.php');

$db = new MysqliDb (Array (
                'host' => 'localhost',
                'username' => 'root',
                'password' => 'root',
                'db'=> 'Zenoti2',
                'port' => 8889,
                'prefix' => 'my_',
                'charset' => 'utf8'));

function getWeeks($startDate, $endDate){
  $start = new DateTime($startDate);
  $end = new DateTime($endDate);
  $interval = new DateInterval('P1D');
  $dateRange = new DatePeriod($start, $interval, $end);

  $weekNumber = 1;
  $weeks = array();
  print_r($weeks);
  foreach ($dateRange as $date) {
      $weeks[$weekNumber][] = $date->format('Y-m-d ');
      if ($date->format('w') == 6) {
          $weekNumber++;
      }
  }
  return $weeks;
}

function getDays($startDate, $endDate){
  $start = new DateTime($startDate);
  $end = new DateTime($endDate);
  $interval = new DateInterval('P1D');
  $dateRange = new DatePeriod($start, $interval, $end);
  return $dateRange;
}


function joinFiles(array $files, $result) {
    if(!is_array($files)) {
        throw new Exception('`$files` must be an array');
    }

    $wH = fopen($result, "w+");

    foreach($files as $file) {
        $fh = fopen($file, "r");
        while(!feof($fh)) {
            fwrite($wH, fgets($fh));
        }
        fclose($fh);
        unset($fh);
        fwrite($wH, "\n"); //usually last line doesn't have a newline
    }
    fclose($wH);
    unset($wH);
}

function curlCall($url, $h, $p = null) {
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => $p["http_method"],
    CURLOPT_POSTFIELDS => $p["postfields"],
    CURLOPT_HTTPHEADER => $h,
  ));
  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #:" . $err;
    return false;
  } else {
    return $response;
  }
}

function getToken($z) {
  $h = $z["header"];
  $p = $z["params"];
  $url = $z["url"];
  $tokenRequest = $z["tokenRequest"];

  $url = $url.$z["tokenUrl"];
  $p["http_method"] = "POST";
  $p["postfields"] = $tokenRequest;

  $response = json_decode(curlCall($url, $h, $p), true);
  print_r($response);
  if(!$response){
    die("Failed to get Token.");
  }else{
    return $response["access_token"];
  }
}
function getLeadsFromCSV($csvFile = 'QWLC-ReferralSource.csv'){

  if (($handle = fopen($csvFile, 'r')) === false) {
    die('Error opening file');
  }

  $headers = fgetcsv($handle, 1024, ',');
  $complete = array();

  while ($row = fgetcsv($handle, 1024, ',')) {
    $complete[] = array_combine($headers, $row);
  }

  fclose($handle);

  return $complete;
}

function getJsonFromFile($jsonFile = null){

  if (($file = file_get_contents($jsonFile)) === false) {
    die('Error opening file');
  }

  $json = json_decode($file);

  return $file;
}


function getCenterList(){

  $centers = json_decode(getJsonFromFile('qwlc_center_list.json'));
  return $centers->Centers;
}
