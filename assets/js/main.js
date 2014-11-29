$(document).ready(function () {
    $('.ui.accordion').accordion({
        exclusive: false
    });
    $('a.phpstorm').click(function (e) {
        e.preventDefault();
        $.getJSON($(this).attr("href"));
    });
});
