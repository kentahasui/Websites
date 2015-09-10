
function translate(text, fromCode, toCode){
  var APIKey = 'trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d';
  var urlString = ""; 
  urlString += 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + APIKey;
  urlString += '&lang=' + fromCode + '-' + toCode;
  urlString += '&text=' + encodeURIComponent(text);
  $.ajax({
    url: urlString, 
    dataType: 'json'
  }).done(function(data){
      console.log(data);
    });
}

function translate2(text, translationCode){
  var APIKey = 'trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d';
  var urlString = ""; 
  urlString += 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + APIKey;
  urlString += '&lang=' + translationCode;
  urlString += '&text=' + encodeURIComponent(text);
  console.log(urlString);
  $.ajax({
    url: urlString, 
    dataType: 'json'
  }).done(function(data){
      console.log(data.text[0]);
    });
}
translate2('hello', 'en-es');
  
function getEnglishCodes(){
  function english(value){
    return value.indexOf('en-') > -1;
  }
  return $.ajax({
    url: 'https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d&ui=en', 
    dataType: 'json'
  }).done(function(data){
        var allPairs = data.dirs;
        var allEnglishPairs = allPairs.filter(english);
        for(var index=0; index<allEnglishPairs.length; index++){
          var code = allEnglishPairs[index];
          translate2('hello', code);
        }
        console.log(allEnglishPairs);
  });
}
getEnglishCodes();

////////////////////////////////////////
  
  
function getAllLangCodes(){
  return $.ajax({
    url: 'https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d&ui=en',
    dataType: 'json',
    success: function(data){
      //console.log(Object.keys(data.langs));
      return Object.keys(data.langs);
    }
  });
}

function ajaxCall(urlString){
  return $.ajax({
    url:urlString,
    dataType: 'json',
    success: function(data){
      return data;
    },
    fail: function(data){
      return "Failure!";
    }
  });
}

function getAllLangCodes(){
  var allCodesArray;
  $.when(ajaxCall('https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d&ui=en')).done(function( data, textStatus, jqXHR ){
    allCodesArray = Object.keys(data.langs);
    return allCodesArray;
  });
}


$.when(ajaxCall('https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d&ui=en')).done(function( data, textStatus, jqXHR ){
  console.log(data);
})

$.ajax({
  url: 'https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=trnsl.1.1.20150827T185353Z.5ec417e05b82daa4.5ee9f45671ed5d5dab77e90f2b3049fb6cd93a2d&ui=en',
  dataType: 'json',
  success: function(data){ 
    console.log(data);
    console.log(Object.keys(data.langs));
  }
});

