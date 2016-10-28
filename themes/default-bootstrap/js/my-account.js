$(document).ready(function () {
    $('.nav-tabs > li:first-of-type, #categories > option:selected').addClass('active');
    $('.tab-content > *:first-of-type').addClass('in active');

    $(document).on('change', '#categories', function(e) {
        var that = $(this),
            tab,
            type = $('#type');
        type.val('');
        that.find('option').removeClass('active');
        $('.tab-content').prepend($('#buy-fields').html());
        tab = that.find('option:selected').addClass('active').data('tab');
        $(tab).addClass('in active').siblings().removeClass('in active');
        if(parseInt(that.val()) === 919) {
            type.val('leas');
            $('.tab-content .tab-pane').not('.active').each(function() {
                $('#buy-fields').html($(this).remove());
            });
        }
    })
});