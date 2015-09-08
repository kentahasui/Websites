// TranslateHelper.js
var allTextJSON = 
  ' {"Arabic":"أحبك","Bosnian (Latin)":"volim te","Bulgarian":"Обичам те","Catalan":"T\'estimo","Chinese Simplified":"我爱你",         '+ 
  ' "Chinese Traditional":"我愛你","Croatian":"volim te","Czech":"Miluju tě","Danish":"Jeg elsker dig","Dutch":"Ik hou van jou",       '+ 
  ' "Estonian":"Ma armastan sind","Finnish":"Minä rakastan sinua","French":"Je t\'aime","German":"Ich liebe dich",                    '+ 
  ' "Greek":"Σε αγαπώ","Haitian Creole":"Mwen renmen ou","Hebrew":"אני אוהב אותך","Hindi":"मुझे तुमसे प्यार है","Hmong Daw":"kuv hlub koj",      '+ 
  ' "Hungarian":"szeretlek","Indonesian":"Aku cinta kamu","Italian":"Ti amo","Japanese":"愛しています","Klingon":"qaparHa\'qu\'",         '+
  ' "Korean":"당신을 사랑해요","Latvian":"es tevi mīlu","Lithuanian":"aš tave myliu","Malay":"saya cintakan awak","Maltese":"Inħobbok",  '+
  ' "Yucatec Maya":"In yaabilmajech","Norwegian":"jeg elsker deg","Querétaro Otomi":"Xi di ne\'i","Persian":"دوستت دارم","Polish":"Kocham cię", '+
  ' "Portuguese":"Eu te amo","Romanian":"te iubesc","Russian":"Я тебя люблю","Serbian (Cyrillic)":"волим те","Serbian (Latin)":"volim te", '+
  ' "Slovak":"ľúbim ťa","Slovenian":"ljubim te","Spanish":"Te quiero","Swedish":"Jag älskar dig","Thai":"ฉันรักเธอ","Turkish":"Seni seviyorum", '+
  ' "Ukrainian":"Я тебе кохаю","Urdu":"ميں تم سے پيار کرتی ہوں","Vietnamese":"Anh yêu em","Welsh":"rydw i\'n dy garu di"}';
var allTextObject = JSON.parse(allTextJSON);
var allTextArray = $.map(allTextObject, function(text, language){return {'language':language, 'text': text};});
$.each(allTextArray, function(index, element){console.log(element)});
var arrayLength=allTextArray.length;

var delay = 750;

$(document).ready(function(){
  function makeDiv(index){
    if(index < 0)return makeFinalDiv();
    
    // vary size and color for fun
    var color = '#'+ Math.round(0xffffff * Math.random()).toString(16);
    var object = allTextArray[index % arrayLength];
    var text = object['text'];
    var language = object['language'];
    // Create new span and div to place in page
    var $newSpan = $('<span/>').addClass('translationText').css('color', color).html(text);
    var $newdiv = $('<div/>').css('opacity', '0');
    $('body').append($newdiv);
    $newdiv.append($newSpan);
    var divWidth = $newSpan.width();
    var divHeight = $newSpan.height();
    // make position sensitive to size and document's width
    var posx = (Math.random() * ($(document).width() - divWidth)).toFixed();
    var posy = (Math.random() * ($(document).height() - divHeight)).toFixed();
    
    $newdiv.css({
        'position':'absolute',
        'width':divWidth+'px',
        'height':divHeight+'px',
        'left':posx+'px',
        'top':posy+'px'
    }).delay(delay).fadeTo(1, 1.0, function(){
      $newSpan.fadeTo(1000, 1.0);
      makeDiv(index-1); 
    }); 
  }
  
  function makeFinalDiv(){
    $("#finalText").delay(delay).fadeTo(600, 1.0);
  }
  
  makeDiv(2);
  
  
}); // End document.ready