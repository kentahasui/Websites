 <?php
/* TokenRetriever class
 * Wrapper for the static retrieveMicrosoftToken
 * The token is needed to access the Microsoft Translate API
 */
class TokenRetriever
{ 
 /*
  * Get the 10-minute access token.
  * @param string $clientID     Application client ID.
  * @param string $clientSecret Application client ID.
  * @param string $grantType    Grant type.
  * @param string $scopeUrl     Application Scope URL.
  * @param string $authUrl      Oauth Url. 
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
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array($this->getAuthHeader(),"Content-Type: text/xml"));
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
  
  /* Returns all the language codes of languages we can translate to
   * @return Array         Array of strings representing all language codes supported by microsoft translator
   */
  public function getLanguagesForTranslate(){
    // Retrieves all language codes. Caches the information in an array
    if(count($this->LanguageCodes)==0){
      echo "\nRetrieving all language codes...\n";
      $languagesURL = "http://api.microsofttranslator.com/V2/Http.svc/GetLanguagesForTranslate";
      $langsXML = $this->curlRequest($languagesURL); // Pass in null 
      // Converts xml string as an object
      $langsXMLObject = simplexml_load_string($langsXML);
      // Places all 
      foreach($langsXMLObject->string as $language){
        $languageCode = strip_tags($language);
        $this->LanguageCodes[] = $languageCode;
      }
    }
    return $this->LanguageCodes;
  }
  
  /* Returns an array of language names
   * 
   */
  public function getAllLanguageNames(){
    echo "\nRetrieving all language names ... ";
    // First get all language codes as xml string
    $languagesURL = "http://api.microsofttranslator.com/v2/Http.svc/GetLanguagesForTranslate";
    $langsXML = $this->curlRequest($languagesURL);
    $locale = 'en';
    $url = "http://api.microsofttranslator.com/v2/Http.svc/GetLanguageNames?locale=$locale";
    $langNamesXML = $this->curlRequest($url, $langsXML);
    $langNamesXMLObject = simplexml_load_string($langNamesXML);
    // Places all lang names in an array
    $LangNames = array();
    foreach($langNamesXMLObject->string as $langName){
      $LangNames[] = strip_tags($langName);
    }
    return $LangNames;
  }
  
  /* Translates a string from one language to another
   * @param string $text   The text to translate
   * @param string $from   The language code of the original text 
   * @param string $to     The language code of the language to translate to 
   *
   * @return string        The translated text
   */
  public function translate($text, $from, $to){
    // Construct parameters to pass in through the HTTP GET method
    $params = "from=$from".
              "&to=$to".
              "&text=".urlencode($text);
    //HTTP getTranslationsMethod URL
    $translationURL = "http://api.microsofttranslator.com/v2/Http.svc/Translate?$params";
    $translationString = $this->curlRequest($translationURL);
    return strip_tags($translationString);
  }
  
  /* Translates a string into all other languages. 
   * @param string $text    The text to translate
   * @param string $from    The language code to translate from. Default is English
   * @return Array          Array of translations as text(maybe JSON string?)
   */
  public function translateToAllLanguages($text, $from='en'){
    echo "   \n Translating all ...";
    // Temporary variable to store results
    $OutputArray = array();
    // All of the language names
    $ArrayOfLanguageNames = $this->getAllLanguageNames();
    // All of the language codes
    $ArrayOfLanguageCodes = $this->getLanguagesForTranslate();
    // Place all of the translations into the output array
    foreach($ArrayOfLanguageCodes as $index=>$code){
      $name = $ArrayOfLanguageNames[$index];
      $OutputArray[$name] = $this->translate($text, $from, $code);
    }
    return $OutputArray;
  }
  
  /* Translates a string into all other
   * @param string $text    The text to translate
   * @param string $from    The language code to translate from. Default is English
   * @return string         Final result string. Maybe an associative array containing input, final result, and stack trace
   */
  public function telephone($text, $from='en'){
  
  }
  
} // END TRANSLATOR CLASS


// Main thread of execution
try{
  $translator = new Translator();
  //echo $translator->getAuthHeader();
  echo "\n";
  echo "\n";
  //print_r ($translator->getLanguagesForTranslate());
  echo "\n";
  echo "\n";
  //print_r ($translator->getAllLanguageNames());
  //$output = $translator->translate("hello", 'en', 'ja');
  //echo json_encode($Array, JSON_UNESCAPED_UNICODE);
  echo json_encode(($translator->translateToAllLanguages("I love you")), JSON_UNESCAPED_UNICODE);
}catch (Exception $e) {
  echo "Exception: " . $e->getMessage() . PHP_EOL;
}

/*
without code: 
["أحبك","volim te","Обичам те","T'estimo","我爱你","我愛你","volim te","Miluju tě","Jeg elsker dig","Ik hou van jou","Ma armastan sind","Minä rakastan sinua","Je t'aime","Ich liebe dich","Σε αγαπώ","Mwen renmen ou","אני אוהב אותך","मुझे तुमसे प्यार है","kuv hlub koj","szeretlek","Aku cinta kamu","Ti amo","愛しています","qaparHa'qu'","","당신을 사랑해요","es tevi mīlu","aš tave myliu","saya cintakan awak","Inħobbok","In yaabilmajech","jeg elsker deg","Xi di ne'i","دوستت دارم","Kocham cię","Eu te amo","te iubesc","Я тебя люблю","волим те","volim te","ľúbim ťa","ljubim te","Te quiero","Jag älskar dig","ฉันรักเธอ","Seni seviyorum","Я тебе кохаю","ميں تم سے پيار کرتی ہوں","Anh yêu em","rydw i'n dy garu di","I love you"]

with code:
{"ar":"أحبك","bs-Latn":"volim te","bg":"Обичам те","ca":"T'estimo","zh-CHS":"我爱你","zh-CHT":"我愛你","hr":"volim te","cs":"Miluju tě","da":"Jeg elsker dig","nl":"Ik hou van jou","en":"I love you","et":"Ma armastan sind","fi":"Minä rakastan sinua","fr":"Je t'aime","de":"Ich liebe dich","el":"Σε αγαπώ","ht":"Mwen renmen ou","he":"אני אוהב אותך","hi":"मुझे तुमसे प्यार है","mww":"kuv hlub koj","hu":"szeretlek","id":"Aku cinta kamu","it":"Ti amo","ja":"愛しています","tlh":"qaparHa'qu'","tlh-Qaak":"","ko":"당신을 사랑해요","lv":"es tevi mīlu","lt":"aš tave myliu","ms":"saya cintakan awak","mt":"Inħobbok","yua":"In yaabilmajech","no":"jeg elsker deg","otq":"Xi di ne'i","fa":"دوستت دارم","pl":"Kocham cię","pt":"Eu te amo","ro":"te iubesc","ru":"Я тебя люблю","sr-Cyrl":"волим те","sr-Latn":"volim te","sk":"ľúbim ťa","sl":"ljubim te","es":"Te quiero","sv":"Jag älskar dig","th":"ฉันรักเธอ","tr":"Seni seviyorum","uk":"Я тебе кохаю","ur":"ميں تم سے پيار کرتی ہوں","vi":"Anh yêu em","cy":"rydw i'n dy garu di"}

with name:
{"Arabic":"أحبك","Bosnian (Latin)":"volim te","Bulgarian":"Обичам те","Catalan":"T'estimo","Chinese Simplified":"我爱你","Chinese Traditional":"我愛你","Croatian":"volim te","Czech":"Miluju tě","Danish":"Jeg elsker dig","Dutch":"Ik hou van jou","English":"I love you","Estonian":"Ma armastan sind","Finnish":"Minä rakastan sinua","French":"Je t'aime","German":"Ich liebe dich","Greek":"Σε αγαπώ","Haitian Creole":"Mwen renmen ou","Hebrew":"אני אוהב אותך","Hindi":"मुझे तुमसे प्यार है","Hmong Daw":"kuv hlub koj","Hungarian":"szeretlek","Indonesian":"Aku cinta kamu","Italian":"Ti amo","Japanese":"愛しています","Klingon":"qaparHa'qu'","Klingon (pIqaD)":"","Korean":"당신을 사랑해요","Latvian":"es tevi mīlu","Lithuanian":"aš tave myliu","Malay":"saya cintakan awak","Maltese":"Inħobbok","Yucatec Maya":"In yaabilmajech","Norwegian":"jeg elsker deg","Querétaro Otomi":"Xi di ne'i","Persian":"دوستت دارم","Polish":"Kocham cię","Portuguese":"Eu te amo","Romanian":"te iubesc","Russian":"Я тебя люблю","Serbian (Cyrillic)":"волим те","Serbian (Latin)":"volim te","Slovak":"ľúbim ťa","Slovenian":"ljubim te","Spanish":"Te quiero","Swedish":"Jag älskar dig","Thai":"ฉันรักเธอ","Turkish":"Seni seviyorum","Ukrainian":"Я тебе кохаю","Urdu":"ميں تم سے پيار کرتی ہوں","Vietnamese":"Anh yêu em","Welsh":"rydw i'n dy garu di"}

*/

?>