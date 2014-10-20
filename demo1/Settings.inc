<?php

$environments = array(
    "www"     => "http://www.devmate.com",
    "staging" => "http://test.devmate.com",
);

$GLOBALS['settings']['webserver'] = $environments["www"];
$GLOBALS['settings']['browser']   = "chrome";
$GLOBALS['settings']['selenium']  = "http://localhost:4444/wd/hub";

?>