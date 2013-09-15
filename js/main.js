$(function () {
    // $.getJSON('/ajax/people', function (data) {
    // });

    $('#people').html(
        tmpl('tmplpeople', {
            people: [
                { screen_name: 'Rogozin' },
                { screen_name: 'nnikiforov' },
                { screen_name: 'rosnano_ru' },
                { screen_name: 'dumagovru' },
                { screen_name: 'rosreestr_info' },
                { screen_name: 'Rostransnadzor' },
                { screen_name: 'Minselhoz' }
            ]
        })
    );

    $('button').on('click', function (e) {
        var screenName = $(this).data('screen_name');

        // $.getJSON('/people', { screen_name: screenName }, function (data) {
        // });
    });
});
