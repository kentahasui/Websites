// TranslateHelper.js
$(document).ready(function(){
  var $centeredDiv = $("#centeredDiv");
  var $divLeft = $centeredDiv.position().left;
  var $divRight = $divLeft + $centeredDiv.outerWidth();
  var $divTop = $centeredDiv.position().top;
  var $divBottom = $divTop + $centeredDiv.outerHeight();
  
  function makeDiv(count){
    if (count<=0) return makeFinalDiv();
    
    // vary size and color for fun
    var color = '#'+ Math.round(0xffffff * Math.random()).toString(16);
    
    // Create new span and div to place in page
    var $newSpan = $('<span/>').addClass('translationText').css('color', color).html('raaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaadom');
    var $newdiv = $('<div/>').css('opacity', '0');
    
    $('body').append($newdiv);
    $newdiv.append($newSpan);
    
    var divsize = $newSpan.width();
    var divHeight = $newSpan.height();
    console.log(divsize);
    
    // make position sensitive to size and document's width
    
    //$(document).height() - divsize // upper bound
    //($(document).height() - divsize) - $divRight; // # pixels between right edge of center div and left edge of center edge
    //(Math.random() * (($(document).height() - divsize) - $divRight)) + $divRight;

    var posx = (Math.random() < 0.5) ? (Math.random() * ($divLeft - divsize)).toFixed() // Place left
                                     : ((Math.random() * ($(document).width() - divsize - $divRight)) + $divRight).toFixed(); // place right
    var posy = (Math.random() < 0.5) ? (Math.random() * ($divTop - divHeight)).toFixed() // Place above
                                     : ((Math.random() * ($(document).height() - divHeight - $divBottom)) + $divBottom).toFixed(); // place below
    
    $newdiv.css({
        'position':'absolute',
        'width':divsize+'px',
        'height':divHeight+'px',
        'left':posx+'px',
        'top':posy+'px',
        'background-color': '#f3f3f3',
    }).delay(1000).fadeTo(500, 1.0, function(){
      $newSpan.fadeTo(1, 1.0);
      makeDiv(count-1); 
    }); 
  }
  
  function makeFinalDiv(){
    $centeredDiv.fadeTo(600, 1.0);
  }
  
  function getWord(jsonAllLangs){
    
  }
  
}); // End document.ready