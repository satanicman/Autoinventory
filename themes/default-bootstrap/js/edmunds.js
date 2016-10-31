var api_key = 'yhubvu7krmyyuz9qx5rmcgp7';
var mmys = EDAPIEX.MMYS;
(function() {
    mmys.init(function(data){
        var makes = mmys.getMakes();
        setMake(makes);

    }, {api_key: api_key});

    $(document).on('change', '#make', function(e) {
        var make = $(this).find('option:selected').data('name');
        $('#preloader_list').fadeIn('fast');
        setModels(make);
    });


    $(document).on('click', '#vin-submit', function(e) {
        $('#preloader_list').fadeIn('fast');
        var $field, edmund, vin;
        e.preventDefault();
        $field = $('#vin-value');
        vin_value = $field.val();
        mmys.init(function(data){
            var vin = mmys.getVin(vin_value, function(json) {
                $('.api_fields').prop('readonly', false).removeClass('disabled').val('');
                if(json.message != null && json.message) {
                    alert(json.message);
                    $('#preloader_list').fadeOut('fast');
                } else {
                    var styles = json.years[0].styles[0].name.replace(/^.*\(/, '').replace(/\).*$/, '').split(' ');
                    if (typeof json.make === 'object' && typeof json.model === 'object') {
                        $("#make option#" + json.make.id).prop('selected', true);
                        setMake(false, json.make.niceName, json.model.name);
                    }
                    if (json.drivenWheels != null && json.drivenWheels)
                        $("#drive_type").val(json.drivenWheels);
                    if (json.years[0].styles[0].trim != null && json.years[0].styles[0].trim)
                        $("#trim").val(json.years[0].styles[0].trim);
                    if (styles[0] != null && styles[0])
                        $("#engine").val(styles[0]);
                    if (styles[2] != null && styles[2])
                        $("#transmission").val(styles[2]);
                    if (json.years[0].styles[0].submodel.body != null && json.years[0].styles[0].submodel.body)
                        $("#body_type").val(json.years[0].styles[0].submodel.body);
                    if (json.numOfDoors != null && json.numOfDoors)
                        $("#doors").val(json.numOfDoors);
                    if (json.years[0].year != null && json.years[0].year)
                        $("#year").val(json.years[0].year);
                }
            });
        }, {api_key: api_key});
    });
})();

function setModels(make, selected) {
    var model_dom = $('#model');
    model_dom.html('');
    var i = 0;
    var length;
    mmys.init(function(data){
        mmys.getModels(function(data) {
            length = Object.keys(data[make]).length;
            for(var model in data[make]) {
                model_dom.append('<option value="' + model + '"' + (model === $('#model_post').val() || (typeof selected !== 'undefined' && selected.toLowerCase() === model) ? 'selected="selected"' : '') + '>' + model + '</option>');
                i++;
            }
        }, make);
    }, {api_key: api_key});
    var interval = setInterval(function() {
        if(i === length) {
            $('#preloader_list').fadeOut('fast');
            clearInterval(interval);
        }
    }, 100);
}

function setMake(makes, selected_make, selected_model) {
    if(!$('#make').length)
        return false;
    $('#preloader_list').fadeIn('fast');
    if(makes && typeof makes === 'object') {
        $('#make').html('');
        for (var make in makes) {
            $('#make').append('<option id="' + makes[make].id + '" data-name="' + make + '" value="' + makes[make].name + '"' + (makes[make].name == $('#make_post').val() || (selected_make !== 'undefined' && selected_make === makes[make].name) ? 'selected="selected"' : '') + '>' + makes[make].name + '</option>');
        }

        if ($('#make option:selected').length) {
            selected_make = $('#make option:selected').data('name');
        } else {
            selected_make = $('#make option:first-of-type').data('name');
        }
    }
    setModels(selected_make, selected_model);
}