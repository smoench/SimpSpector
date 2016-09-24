import "babel-polyfill";
import $ from "jquery";
import "prismjs";
import "semantic-ui-css/semantic";
import "semantic-ui-css/semantic.css";
import "../css/main.css";
import "./filter";

$(document).ready(function () {
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
