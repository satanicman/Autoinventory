$(document).ready(function() {
    var from = 0, to = 0, max, min, values, name, unit;
    $('.filter-left-block').each(function (e) {
        toggleMenu($(this));
    });

    $(document).on('click', '.filter-left-header', function (e) {
        e.preventDefault();
        toggleMenu($(this).parent());
    });

    // $('.filter-slider').each(function (e) {
    //     max = $(this).data('max');
    //     min = $(this).data('min');
    //     values = $(this).data('values').split('-');
    //     name = $(this).data('name');
    //     unit = $(this).data('unit');
    //
    //     $(this).slider({
    //         range: true,
    //         step: 1,
    //         min: min,
    //         max: max,
    //         values: [values[0], values[1]],
    //         slide: function(event, ui) {
    //             name = $(this).data('name');
    //
    //             if(name.toLowerCase() === 'price') {
    //                 from = formatCurrency(ui.values[0], 1, unit);
    //                 to = formatCurrency(ui.values[1], 1, unit);
    //             } else {
    //                 from =  ui.values[0];
    //                 to = ui.values[1];
    //             }
    //
    //             $('.filter-header-value.' + name + ' .from').html(from);
    //             $('.filter-header-value.' + name + ' .to').html(to);
    //         }
    //     });
    //
    //     if(name.toLowerCase() === 'price') {
    //         from = formatCurrency($(this).slider('values', 0), 1, unit);
    //         to = formatCurrency($(this).slider('values', 1), 1, unit);
    //     } else {
    //         from =  $(this).slider('values', 0);
    //         to = $(this).slider('values', 1);
    //     }
    //
    //     $('.filter-header-value.' + name + ' .from').html(from);
    //     $('.filter-header-value.' + name + ' .to').html(to);
    // });
});

function toggleMenu(target) {
    // console.log(target.find('.filter-left-header').text());
    if (target.hasClass('on')) {
        target.removeClass('on').addClass('off');
        target.find('.icon').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        target.find('.filter-left-list').slideUp('fast');
    } else if(target.hasClass('off')) {
        target.removeClass('off').addClass('on');
        target.find('.icon').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        target.find('.filter-left-list').slideDown('fast');
    } else if(target.find('.checkbox:checked').length) {
        target.removeClass('off').addClass('on');
        target.find('.icon').removeClass('icon-chevron-down').addClass('icon-chevron-up');
        target.find('.filter-left-list').slideDown('fast');
    }  else {
        target.removeClass('on').addClass('off');
        target.find('.icon').removeClass('icon-chevron-up').addClass('icon-chevron-down');
        target.find('.filter-left-list').hide();
    }
}