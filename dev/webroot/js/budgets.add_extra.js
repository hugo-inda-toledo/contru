$(document).on('click', '.toggle-open', function(event) {
    event.preventDefault();
    $('.panel-collapse').collapse('toggle');
    setTimeout(toggleOpenBtnText, 1000);
});

function toggleOpenBtnText () {
    if ($('.panel-collapse').first().hasClass('in')) {
        $('.toggle-open').text('Contraer Partidas');
    } else {
        $('.toggle-open').text('Expandir Partidas');
    }
}
