var api_key = 'yhubvu7krmyyuz9qx5rmcgp7';
var mmys = EDAPIEX.MMYS;
$(document).ready(function () {
    var t = $('#make');
    if ($('#make').length)
        setMake();
    $(document).on('click', '#vin-submit', function (e) {
        $('#preloader_list').fadeIn('fast');
        var $field, edmund, vin;
        e.preventDefault();
        $field = $('#vin-value');
        vin_value = $field.val();
        mmys.init(function (data) {
            mmys.getVin(vin_value, function (json) {
                var make = $('#make'),
                    model = $('#model'),
                    year = $('#year'),
                    engine = $('#engines'),
                    transmission = $('#transmission'),
                    body_type = $('#body_type'),
                    drive_type = $('#drive_type'),
                    doors = $('#doors'),
                    trim = $('#trim');

                $('.api_fields').prop('readonly', false).removeClass('disabled').val('');
                if (json.message != null && json.message) {
                    alert(json.message);
                    playcholder(0);
                } else {
                    var styles = json.years[0].styles[0].name.replace(/^.*\(/, '').replace(/\).*$/, '').split(' ');
                    if (typeof json.make === 'object') {
                        console.log(document.getElementById('make')[0]);
                        if(make[0].tagName === 'SELECT') {
                            make.hide().after('<input data-test="1" type="text" id="' + make.attr('id') + '" class="' + make.attr('class') + '" name="' + make.attr('name') + '" value="' + json.make.niceName + '"/>');
                            make[0].remove();
                        } else {
                             make.val(json.make.niceName);
                        }
                    }
                    if(typeof json.model === 'object') {
                        if(model[0].tagName === 'SELECT') {
                            model.hide().after('<input type="text" id="' + model.attr('id') + '" class="' + model.attr('class') + '" name="' + model.attr('name') + '" value="' + json.model.name + '"/>');
                            model[0].remove();
                        } else {
                             model.val(json.model.name);
                        }
                    }
                    if (json.drivenWheels != null && json.drivenWheels) {
                        if(drive_type[0].tagName === 'SELECT') {
                            drive_type.hide().after('<input type="text" id="' + drive_type.attr('id') + '" class="' + drive_type.attr('class') + '" name="' + drive_type.attr('name') + '" value="' + json.drivenWheels + '"/>');
                            drive_type[0].remove();
                        } else {
                             drive_type.val(json.drivenWheels);
                        }
                    }
                    if (json.years[0].styles[0].trim != null && json.years[0].styles[0].trim) {
                        if(trim[0].tagName === 'SELECT') {
                            trim.hide().after('<input type="text" id="' + trim.attr('id') + '" class="' + trim.attr('class') + '" name="' + trim.attr('name') + '" value="' + json.years[0].styles[0].trim + '"/>');
                            trim[0].remove();
                        } else {
                             trim.val(json.years[0].styles[0].trim);
                        }
                    }
                    if (styles[0] != null && styles[0]) {
                        if(engine[0].tagName === 'SELECT') {
                            engine.hide().after('<input type="text" id="' + engine.attr('id') + '" class="' + engine.attr('class') + '" name="' + engine.attr('name') + '" value="' + styles[0] + '"/>');
                            engine[0].remove();
                        } else {
                             engine.val(styles[0]);
                        }
                    }
                    if (styles[2] != null && styles[2]) {
                        if(transmission[0].tagName === 'SELECT') {
                            transmission.hide().after('<input type="text" id="' + transmission.attr('id') + '" class="' + transmission.attr('class') + '" name="' + transmission.attr('name') + '" value="' + styles[2] + '"/>');
                            transmission[0].remove();
                        } else {
                             transmission.val(styles[2]);
                        }
                    }
                    if (json.years[0].styles[0].submodel.body != null && json.years[0].styles[0].submodel.body) {
                        if(body_type[0].tagName === 'SELECT') {
                            body_type.hide().after('<input type="text" id="' + body_type.attr('id') + '" class="' + body_type.attr('class') + '" name="' + body_type.attr('name') + '" value="' + json.years[0].styles[0].submodel.body + '"/>');
                            body_type[0].remove();
                        } else {
                             body_type.val(json.years[0].styles[0].submodel.body);
                        }
                    }
                    if (json.numOfDoors != null && json.numOfDoors) {
                        if(doors[0].tagName === 'SELECT') {
                            doors.hide().after('<input type="text" id="' + doors.attr('id') + '" class="' + doors.attr('class') + '" name="' + doors.attr('name') + '" value="' + json.numOfDoors + '"/>');
                            doors[0].remove();
                        } else {
                             doors.val(json.numOfDoors);
                        }
                    }
                    if (json.years[0].year != null && json.years[0].year) {
                        if(year[0].tagName === 'SELECT') {
                            year.hide().after('<input type="text" id="' + year.attr('id') + '" class="' + year.attr('class') + '" name="' + year.attr('name') + '" value="' + json.years[0].year + '"/>');
                            year[0].remove();
                        } else {
                             year.val(json.years[0].year);
                        }
                    }

                    playcholder(0);
                }
            });
        }, {api_key: api_key});
    });

    $(document).on('change', '#make', function (e) {
        var that = $(this),
            make = that.find('option:selected').data('name');
        setModels(make);
    });

    $(document).on('change', '#model', function (e) {
        var that = $(this),
            model = that.find('option:selected').data('name'),
            make = $('#make').find('option:selected').data('name');
        setYear(make, model);
    });

    $(document).on('change', '#year', function (e) {
        var that = $(this),
            year = that.find('option:selected').data('name'),
            model = $('#model').find('option:selected').data('name'),
            make = $('#make').find('option:selected').data('name');
        setTrim(make, model, year);
    });

    $(document).on('change', '#trim', function (e) {
        var that = $(this),
            id = that.find('option:selected').data('name');
        setTransmissionEngines(id);
    });
});

function setMake() {
    playcholder(1);
    mmys.init(function (data) {
        var makes = mmys.getMakes(),
            el = $('#make');

        if (makes && typeof makes === 'object') {
            clean(el);
            for (var make in makes) {
                el.append('<option id="' + makes[make].id + '" data-name="' + make + '" value="' + makes[make].name + '">' + makes[make].name + '</option>');
            }
        }
        playcholder(0);
    }, {});
}

function setModels(make) {
    playcholder(1);
    mmys.init(function (data) {
        var el = $('#model');
        mmys.getModels(function (data) {
            if (data[make] && typeof data[make] === 'object') {
                clean(el);
                for (var model in data[make]) {
                    el.append('<option id="' + data[make][model].id + '" data-name="' + model + '" value="' + data[make][model].name + '">' + data[make][model].name + '</option>');
                }
            }
            playcholder(0);
        }, make);
    }, {});
}

function setYear(make, model) {
    playcholder(1);
    mmys.init(function (data) {
        var el = $('#year'),
            years = mmys.getYears(make, model);
        if (years && years !== undefined) {
            clean(el);
            for (var year in years) {
                el.append('<option data-name="' + years[year] + '" value="' + years[year] + '">' + years[year] + '</option>');
            }
        }
        playcholder(0);
    }, {});
}

function setTrim(make, model, year) {
    playcholder(1);
    mmys.init(function (data) {
        var el = $('#trim'),
            styles = mmys.getStyles(make, model, year);
        if (styles && styles !== undefined) {
            clean(el);
            console.log(styles);
            for (var style in styles) {
                el.append('<option data-name="' + styles[style]['id'] + '" value="' + styles[style]['trim'] + '">' + styles[style]['trim'] + '</option>');
            }
        }
        playcholder(0);
    }, {});

}

function setTransmissionEngines(id) {
    playcholder(1);
    id += '';
    mmys.init(function (data) {
        mmys.getProp(id, function(engines) {
            var el = $('#engines');
            if (engines && engines !== undefined) {
                clean(el);
                engines = engines['engines'];
                for (var engin in engines) {
                    el.append('<option value="' + engines[engin]['size'] + '">' + engines[engin]['size'] + '</option>');
                }
            }
        });
        mmys.getProp(id, function(transmissions) {
            var el = $('#transmission');
            if (transmissions && transmissions !== undefined) {
                clean(el);
                transmissions = transmissions['transmissions'];
                for (var transmission in transmissions) {
                    if(typeof transmissions[transmission]['options'] !== 'undefined') {
                        for (var option in transmissions[transmission]['options']) {
                            el.append('<option value="' + transmissions[transmission]['options'][option]['name'] + '">' + transmissions[transmission]['options'][option]['name'] + '</option>');
                        }
                    } else {
                        el.append('<option value="' + transmissions[transmission]['name'] + '">' + transmissions[transmission]['name'] + '</option>');
                    }
                }
            }
        }, 'transmissions');
        playcholder(0);
    }, {});
}

function getLvl() {
    var lvl = 0;
    $('.form-group select').each(function () {
        var that = $(this),
            el_lvl = that.data('lvl') + 1;
        if (el_lvl > lvl)
            lvl = el_lvl;
    });
    return lvl;
}

function clean(el) {
    var lvl = el.data('lvl'),
        html = '<option value="0" selected="selected">Please select the variant</option>';
    el.html(html);
    $('.form-group select').each(function () {
        var that = $(this);
        if (that.data('lvl') > lvl) {
            that.html('');
            that.removeAttr('data-lvl');
        }
    });
    el.removeAttr('data-lvl');
    el.attr('data-lvl', getLvl());
}

function playcholder(show) {
    var pl = $('#preloader_list');
    show = typeof show ? show : 0;
    if (show) {
        pl.fadeIn('fast');
    } else {
        pl.fadeOut('fast');
    }
}