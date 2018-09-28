<?php

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

print_r(getCenterList());
