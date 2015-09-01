<?php
/* TokenRetriever class
 * Wrapper for the static retrieveMicrosoftToken
 * The token is needed to access the Microsoft Translate API
 */
class TokenRetriever
{ 
 /*
  * Get the 10-minute access token.
  *
  * @param string $clientID     Application client ID.
  * @param string $clientSecret Application client ID.
  * @param string $grantType    Grant type.
  * @param string $scopeUrl     Application Scope URL.
  * @param string $authUrl      Oauth Url.
  *
  * @return string.
  */
  public function retrieveMicrosoftToken($clientID, $clientSecret, $grantType, $scopeUrl, $authUrl)
  {
    // Create new curl object, and set its url and post parameters
    $curl = curl_init();
    // Construct the post parameters as an associative array, then convert to http string
    $parameterArray = array(
      'client_id'     => $clientID, 
      'client_secret' => $clientSecret, 
      'grant_type'    => $grantType, 
      'scope'         => $scopeUrl
    );
    $parameterArray = http_build_query($parameterArray);
    // Set the curl url
    curl_setopt($curl, CURLOPT_URL, $authUrl);
    // Configure to use HTTP POST
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameterArray);
    // Output the curl results as string, to be stored in a variable
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $token = curl_exec($curl);
    //Get the Error Code returned by Curl.
    $curlErrno = curl_errno($curl);
    if($curlErrno){
      $curlError = curl_error($curl);
      throw new Exception($curlError);
    }
    curl_close($curl);
    return $token;
  }
} // END TOKENRETRIEVER CLASS

/* Translator class
 * Encapsulates a TokenRetriever object
 * Contains various methods for translations using microsoft translate api
 */
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
  private $LanguageCodes;
  
  public function __construct(){
    $this->TokenRetriever = new TokenRetriever();
    $this->LanguageCodes = array();
  }
  
  /* Returns a newly generated access token */
  private function generateToken(){
    return $this->TokenRetriever->retrieveMicrosoftToken($this->ClientID, $this->ClientSecret, $this->GrantType, $this->ScopeURL, $this->AuthURL); 
  }
  
  // Retrieves the access token. Updates the token if more than 10 minutes since generated. 
  public function getAuthHeader(){
    // Find out how many seconds elapsed
    $timeNow = time();
    $timeDifference = $timeNow - $this->TokenGetTime;
    // If more than 10 minutes elapsed, get new token from Microsoft
    if($timeDifference > 599 || $TokenGetTime === -1){
      $TokenGetTime = time();
      // Retrieve token as JSON string, then convert to array using json_encode, and then construct the authentication header 
      $responseString = $this->generateToken();
      $responseArray = json_decode($responseString);
      $token = $responseArray->access_token;
      $this->AuthHeader = "Authorization: Bearer ". $token;
    }
    return $this->AuthHeader;
  }
  
  // Sends a curl request using info
  public function curlRequest($url, $postData=''){
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    //Set the HTTP HEADER Fields.
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array($this->AuthHeader,"Content-Type: text/xml"));
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
  
  /* Returns the languages to translate */
  public function getLanguagesForTranslate(){
    // Retrieves all language codes. Caches the information in an array
    if(count($this->LanguageCodes)==0){
      echo "\nRetrieving all language codes...\n";
      $languagesURL = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguagesForTranslate";
      $langsXML = $this->curlRequest($languagesURL); // Pass in null 
      // Converts xml string as an object
      $langsObject = simplexml_load_string($langsXML);
      // Places all 
      foreach($langsObject->string as $language){
        echo $language . ":";
        echo get_class($language) . "\n";
        $languageCode = strip_tags($language->asXML());
        $this->LanguageCodes[] = $languageCode;
      }
    }
    return $this->LanguageCodes;
  }
  
  public function translate($text, $from, $to, $maxTranslations, $options, $contentType, $uri, $user){
    // Construct parameters to pass in through the HTTP GET method
    $params = "from=$from".
              "&to=$to".
              "&maxTranslations=$maxTranslations".
              "&text=".urlencode($text).
              "&user=$user".
              "&uri=$uri".
              "&contentType=$contentType";
    //HTTP getTranslationsMethod URL.
    $translationURL = "http://api.microsofttranslator.com/V2/Http.svc/GetTranslations?$params";
  }
  
} // END TRANSLATOR CLASS

try{
  $translator = new Translator();
  echo $translator->getAuthHeader();
  echo "\n";
  print_r ($translator->getLanguagesForTranslate());
}catch (Exception $e) {
  echo "Exception: " . $e->getMessage() . PHP_EOL;
}

?>