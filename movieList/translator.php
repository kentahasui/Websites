<?php
// Translator.php
// A class that contains translator methods
include 'tokenRetriever.php';
class Translator
{
  private $ClientID       = "TranslatorVersion1";
  private $ClientSecret   = "BentaBentaBentaBentaBenta720";
  private $GrantType      = 'client_credentials';
  private $ScopeURL       = 'http://api.microsofttranslator.com';
  private $AuthURL        = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
  private $AuthHeader     = "";
  private $TokenGetTime   = -1;
  private $TokenRetriever;
  
  public function __construct(){
    $this->TokenRetriever = new TokenRetriever();
  }
  
  /* Returns a newly generated access token */
  private function generateToken(){
    return $this->TokenRetriever->retrieveMicrosoftToken($this->ClientID, $this->ClientSecret, $this->GrantType, $this->ScopeURL, $this->AuthURL); 
  }
  
  // Retrieves the access token. Updates the token if more than 10 minutes since generated. 
  public function getAuthHeader(){
    // Find out how many seconds elapsed
    $timeNow = microtime(true);
    $timeDifference = $timeNow - $this->TokenGetTime;
    // If more than 10 minutes elapsed, get new token from Microsoft
    if($timeDifference > 599 || $TokenGetTime === -1){
      $TokenGetTime = microtime(true);
      $token = $this->generateToken();
      $this->AuthHeader = "Authorization: Bearer ". $token;
    }
    return $this->AuthHeader;
  }
  
  // Sends a curl request using info
  public function curlRequest($url, $postData=''){
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    //Set the HTTP HEADER Fields.
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array($this->AuthHeader,"Content-Type: text/plain"));
    //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
    if($postData) {
      //Set HTTP POST Request.
      curl_setopt($ch, CURLOPT_POST, TRUE);
      //Set data to POST in HTTP "POST" Operation.
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    //Execute the  cURL session.
    $curlResponse = curl_exec($ch);
    //Get the Error Code returned by Curl.
    $curlErrno = curl_errno($ch);
    if ($curlErrno) {
      $curlError = curl_error($ch);
      throw new Exception($curlError);
    }
    //Close a cURL session.
    curl_close($ch);
    return $curlResponse;
  }
  
  
  
}

$translator = new Translator();
echo $translator->getAuthHeader();

?>