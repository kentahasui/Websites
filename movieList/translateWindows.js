// Global variables
var clientID = 'TranslatorVersion1';
var clientSecret = 'BentaBentaBentaBentaBenta720';
var scope = 'http://api.microsofttranslator.com';
var grant_type = 'client_credentials';

function translate(text, fromCode, toCode){
  
  var urlString = ""; 
  urlString += 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + APIKey;
  urlString += '&lang=' + fromCode + '-' + toCode;
  urlString += '&text=' + encodeURIComponent(text);
  console.log(urlString);
  $.ajax({
    url: urlString, 
    dataType: 'json'
  }).done(function(data){
      console.log(data);
    });
}