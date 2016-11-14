$(document).ready(function () {
    $(document).on('click', '#unSubscribe', function (e) {
        e.preventDefault();
        var that = $(this);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                unSubscribe: 1,
                ajax: 1,
            },
            success: function (jsonData) {
                if (jsonData.hasError) {
                    var errors = '';
                    for (var error in jsonData.errors)
                        //IE6 bug fix
                        if (error !== 'indexOf')
                            errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                    if (!!$.prototype.fancybox)
                        $.fancybox.open([
                                {
                                    type: 'inline',
                                    autoScale: true,
                                    minHeight: 30,
                                    content: '<p class="fancybox-error">' + errors + '</p>'
                                }],
                            {
                                padding: 0
                            });
                    else
                        alert(errors);
                }
                else {
                    var message = 'You successfully unsubscribe';
                    if (!!$.prototype.fancybox)
                        $.fancybox.open([
                                {
                                    type: 'inline',
                                    autoScale: true,
                                    minHeight: 30,
                                    padding: 50,
                                    content: '<p class="unsubscribe-success">' + message + '</p>'
                                }],
                            {
                                padding: 0
                            });
                    else
                        alert(message);

                    that.remove();
                }

                setTimeout(function() {
                    $.fancybox.close();
                }, 3000);
            }
        });
    });
});