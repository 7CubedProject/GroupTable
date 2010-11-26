$(document).ready(function() {
  $.get("http://127.0.0.1/list/getList?size=1", function(data) {

    var jobj = eval(data);
    alert(jobj[0].venue_name);

    for (var i=0; i< jobj.length; i=i+1) {
      $("#gt_venue_list").append(
        "<li class='gtc_venue'>" + jobj[i].venue_name + "</li>");
    }
 
    //$("#gt_venue_list").html(data);
  });
});
