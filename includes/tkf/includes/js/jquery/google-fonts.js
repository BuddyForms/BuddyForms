jQuery(document).ready(function($) {
  var target = $("#title_font_style"); 
  var api = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyB9YMeMGbezVsBiBoFXFlTXCgWhOFmanJg&sort=style';
  var gFontList = [];
  
  var onSuccess = function(data) {
    if(data.kind === "webfonts#webfontList") {
      $.each(data.items, function(index, value) {
        if(value.variants.length > 1) {
          gFontList.push(value);
        }
      })
      renderFontList(gFontList)
      grabFonts(gFontList)
    } else {
      onError()
    }
  }
  
  var onError = function() {
    target.html("Error loading webfonts.")
  }
 
  //*
  $.ajax({
    url : api,
    type : "GET",
    dataType : "jsonp",
    success : onSuccess,
    error : onError
  })
  //*/
  
  function renderFontList(fonts) {
    var template = "<option class='font' value='%name%' style='font-family:%name%'>%name%</option>";
    var html = "";
    var fontData;

    $.each(fonts, function(i, val) {  
      html += templatify(template, {
        'name' : val.family
      })
    })
    target.html(html);
    
    $("select").change(function(){
      $("select option:selected").each(function () {
        fontSelected = ($(this).text());
        $(".field_description").each(function (ii, m) {
          m.style.fontFamily = fontSelected;
        })
      });
    });
  }

  function templatify(html, data) {
    var r
    $.each(data, function(i, val) {
      r = new RegExp('%' + i + '%', 'g')
      html = html.replace(r, val);
    })
    return html;
  }

  function grabFonts(fontList) {
    var base = "http://fonts.googleapis.com/css?family=";
    var families = []; 
    var url; 
    var tail;
    
    $.each(fontList, function(i, v) {
      tail = v.family + ':' + v.variants.join(',')
      $('<link rel="stylesheet" href="' + base + tail + '" >').appendTo("head")
    })
  }

});