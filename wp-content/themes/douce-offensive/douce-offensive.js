$(document).ready(function() {
  
  $("div.nav_photo").click(function() {
    $("#main_photo").attr("src",$(this).attr("url"));
  });
  
});