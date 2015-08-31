<?php

$ClientID = "TranslatorVersion1";
$ClientSecret = "BentaBentaBentaBentaBenta720";
$ClientID = urlencode($ClientID);
$ClientSecret = urlencode($ClientID);

// Get 10-minute access page 
$url = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13";
$postParams = "grant_type=client_credentials&client_id=$ClientID&client_secret=$ClientSecret&scope=http://api.microsofttranslator.com";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$rsp = curl_exec($ch);

print $rsp;
?>
http://webspeechapi.blogspot.ca/2013/04/how-to-use-new-bing-translator-api-with.html