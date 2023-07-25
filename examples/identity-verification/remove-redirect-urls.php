<?php

use VerifyMyContent\SDK\VerifyMyContent;

require_once __DIR__ . "/../../vendor/autoload.php";

# Getting env variables
$API_KEY = getenv("API_KEY");
$API_SECRET = getenv("API_SECRET");

# Setup SDK
$vmc = new VerifyMyContent($API_KEY, $API_SECRET);
$vmc->useSandbox();

try{
    $vmc->removeRedirectUrls(["https://teste2.com"]);
    echo "done!";
}catch(Exception $e){
    echo "error";
    var_export($e);
}
