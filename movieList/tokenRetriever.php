<?php
// Translate.php
// Script to retrieve an access token from the Microsoft Translate API

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
}

?>
