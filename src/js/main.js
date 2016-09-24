import $ from "jquery";
import "prismjs";
import "semantic-ui-css/semantic";
import "semantic-ui-css/semantic.css";
import "../css/main.css";

$(document).ready(function () {

  $('.ui.accordion').accordion({
    exclusive: false
  });

  $('a.phpstorm').click(function (e) {
    e.preventDefault();
    $.getJSON($(this).attr("href"));
  });

  $('.ui.secondary.pointing.menu .item').tab();

  $('.tooltip').popup();

  $('.scroll-down').each(function () {
    $(this).scrollTop(50000);
  });
});
