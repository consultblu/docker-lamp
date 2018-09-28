<?php
ini_set('memory_limit', '-1');

function zenoti_ini($env){
$zenoti = [];

  if($env == "api"){
    $zenoti["header"] = [
          "Content-Type: application/json",
          "Authorization: AN:quickweightloss|i8P9_Anq9iFfqvoDVeE5QQ5Dwx4jQpT-M7OrI8pKEtmQfWDS60Nkf2SSsFSpK9-425DVO5zlX3HjqllfHjcnTCKI7Efqqp9u7j0JK8k8TyBwIOxrgzAtKlTI79NhS0w4os03KFfFA9KXZ_c0JvcnZTGb3nRm-u-y583OYwWCBLtCDjT8sGyPfezM3BXZQDC1Ss73pCf2Sllu0-dgU6zn8hJSHCkAE89NWP0kyl5fGVTK8AhesPD0NfFacFWD4yIWqF8za-oWU5zL2eF-PjKdZ6v0LT07xreHDq1eO4aXBoKn0ROJLAE9JYN8CXouxytR3R_1CKmtRBC5THN0qZQkVQa1yEMwn1B1OFWKNlaa52ltLWOcDikJpMqFjtbXg0A2y3yDThq9c_4jg55yQU3f_J1LwLqqhUHBnkaK3rUOAhyYYSQm2hJjoKjlFPApRB2RGqwoEKVGnONAzpuI2GY77WW-4ixNHEJrNGzsH4GKjid2q82TTsnwgc21F3KYaaTZRmYPrWBasiNomwQ3HG_MBemV5aYNYnW7OZR6vG9JltLsapGmiMx65jaM5nhcIal1qw2-zspIfqzsz5tDprzMB6xrS1MNs4OfeWRGy3nKhDiKTf1hKYzoAV4bU9fXHP_0Hql984hHbZrEPcM20ag4g1W6AFcrVjJyvSdArZqt8fhrJ_pXCM3Lv6TuQ2P29Ot14UjBM1lNk7Cr8VD3KDq80lOdnXrjf9oBgkszKeSyHiA",
          "Postman-Token: aa0f6cbf-871d-43f4-b953-4830651770d9"
        ];
    $zenoti["tokenRequest"] = "username=apiuser&password=APIUser@123&clientId=quickweightloss&grant_type=password";

    $zenoti["url"] = "http://api.zenoti.com/";
    $zenoti["tokenUrl"] = "Token";

  }else{
    $zenoti["header"] = [
        "Accept: application/json",
        "ApiKey: SbGEFb0bLo0RwDQg5SWQ2rpouz86TUQD",
        "Cache-Control: no-cache",
        "Content-Type: application/json",
        "Postman-Token: aa0f6cbf-871d-43f4-b953-4830651770d9"
    ];
    $zenoti["tokenRequest"] = "username=carlo.smith@qwlc.net&password=Welcome@123&clientId=quickweightloss&grant_type=password";
    $zenoti["url"] = "https://apis.zenoti.com/";
    $zenoti["tokenUrl"] = "v1/tokens";
}
  $zenoti["params"] = [
    "StartDate" => "2018-09-19T00:00:00.000Z",
    "EndDate" => "2018-09-20T23:59:59.999Z"
  ];

  return $zenoti;
};
