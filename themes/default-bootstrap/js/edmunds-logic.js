(function() {
    window.EDAPIEX = window.EDAPIEX || {};
    EDAPIEX.MMYS = EDAPIEX.MMYS || {
            API_KEY: "",
            BASE_URL: window.location.protocol == 'https:' ? 'https://api.edmunds.com' : 'http://api.edmunds.com',
            YEAR: 0,
            STATE: '',
            MAKE: '',
            MAKE_COUNT:0,
            MODEL: '',
            MODEL_COUNT:0,
            STYLE: '',
            makes: {}, models: {}, submodels: {}, styles: {},
            init: function(fn2, params) {
                if (!fn2) {
                    alert('You need a callback function to get started.');
                    return;
                }
                if (params && typeof params.api_key == 'string') {
                    this.API_KEY = params.api_key;
                }
                if (params && typeof params.state == 'string') {
                    this.STATE = params.state;
                }
                if (params && typeof params.year == 'number' || parseInt(params.year) > 0) {
                    this.YEAR = params.year;
                }
                if (params && typeof params.make == 'string') {
                    this.MAKE = params.make;
                }
                var that = this;
                var fn = function(data) {
                    var json = JSON.parse(data);
                    if (json.makes) {
                        var makes = json.makes;
                        var len = makes.length;
                        for (var i=0; i < len; i++) {
                            that.makes[makes[i].niceName] = makes[i];
                        }
                        that.MAKE_COUNT = json.makesCount;
                        fn2(that.makes);
                    } else if (json.id && that.MAKE) {
                        that.makes[json.niceName] = json;
                        fn2(that.makes);
                    } else {
                        alert('An error occured when calling /api/vehicle/v2/makes');
                    }
                };
                var endpoint = params.make ? '/api/vehicle/v2/'+params.make : '/api/vehicle/v2/makes';
                params.make = '';
                this.api(endpoint, params, fn);
            },
            getMakes: function(make) {
                if (make && this.makes[make]) {
                    return this.makes[make]
                } else {
                    return this.makes;
                }
            },
            getModels: function(fn2, make, params, force) {
                if (!fn2) {
                    alert('You need a callback function to process models.');
                    return;
                }
                // Legit make!

                if (make && this.makes[make]) {
                    // Cached?

                    if (this.models[make] && force !== true) {
                        return this.models[make];
                    } else { // Go grab the models
                        var that = this;
                        var params = params || {};
                        if (this.STATE) {params.state = this.STATE;}
                        if (this.YEAR) {params.year = this.YEAR;}
                        var fn = function(data) {
                            var json = JSON.parse(data);
                            if (json.models) {
                                var models = json.models;
                                var len = models.length;
                                that.models[make] = {};
                                for (var i=0; i < len; i++) {
                                    that.models[make][models[i].niceName] = {};
                                    that.models[make][models[i].niceName] = models[i];
                                }
                                that.MODEL_COUNT = json.modelsCount;
                                fn2(that.models);
                            } else {
                                alert('An error occured when /api/vehicle/v2/'+make+'/models');
                            }
                        };
                        this.api('/api/vehicle/v2/'+make+'/models', params, fn);
                    }
                } else {
                    return this.models;
                }
            },
            getSubmodels: function(make, model, year) {
                // Legit make and model!
                if (make && this.makes[make] && model && this.models[make]) {
                    // Cached?
                    if (this.submodels[make] &&  this.submodels[make][model]) {
                        if (year && this.submodels[make][model][year]) {
                            return this.submodels[make][model][year];
                        } else {
                            return this.submodels[make][model];
                        }
                    } else { // Go grab the models
                        var make = make || this.MAKE;
                        var model = model || this.MODEL;
                        var year = year || this.YEAR;
                        var submodel = {}
                        submodel[make] = {};
                        submodel[make][model] = {};
                        var len = this.models[make][model].years.length;
                        for (var i=0; i<len; i++) {
                            var len2 = this.models[make][model].years[i].styles.length
                            for (k=0; k<len2; k++) {
                                if (year && year != this.models[make][model].years[i].year) {
                                    continue
                                }
                                submodel[make][model][this.models[make][model].years[i].year] = submodel[make][model][this.models[make][model].years[i].year] || {};
                                submodel[make][model][this.models[make][model].years[i].year][this.models[make][model].years[i].styles[k].submodel.niceName] = submodel[make][model][this.models[make][model].years[i].year][this.models[make][model].years[i].styles[k].submodel.niceName] || []
                                submodel[make][model][this.models[make][model].years[i].year][this.models[make][model].years[i].styles[k].submodel.niceName].push(this.models[make][model].years[i].styles[k]);
                            }
                        }
                        this.submodels = submodel;
                        return submodel[make][model];
                    }
                } else {
                    alert('To return submodels, a valid car make and model values are needed.');
                }
            },
            getYears: function(make, model) {
                // Legit make and model!
                if (make && this.makes[make] && model && this.models[make]) {
                    var submodels = this.getSubmodels(make, model);
                    var years = [];
                    for(var key in submodels) {
                        if(submodels.hasOwnProperty(key)) {
                            years.push(key);
                        }
                    }
                    return years;
                } else {
                    alert('To return car years, a valid car make and model values are needed.');
                }
            },
            getStyles: function(make, model, year, submodel) {
                // Legit make and model!
                if (make && this.makes[make] && model && this.models[make][model] && year && this.submodels[make][model][year]) {
                    var styles = [];
                    var subs = this.getSubmodels(make, model, year);
                    if (submodel) {
                        styles.push(subs[submodel]);
                    } else {
                        for(var key in subs) {
                            if(subs.hasOwnProperty(key)) {
                                var arr = subs[key];
                                var len = arr.length;
                                for (var i=0; i<len; i++) {
                                    styles.push(arr[i]);
                                }
                            }
                        }
                    }
                    return styles;
                } else {
                    alert('To return car styles, a valid car make, model and year values are needed.');
                }
            },
            getVin: function(vin, fn2) {
                this.VIN = typeof vin == 'string' ? vin : alert('vin is missing.');
                var endpoint = this.VIN ? '/api/vehicle/v2/vins/'+ this.VIN : '/api/vehicle/v2/makes/';
                var fn = function(data) {
                    var json = JSON.parse(data);
                    fn2(json)
                }
                this.api(endpoint, {}, fn);
            },
            api: function(endpoint, params, fn, method, format) {
                endpoint = typeof endpoint == 'string' ? endpoint : alert('API endpoint is missing.');
                params = typeof params == 'object' ? params : {};
                method = typeof method == 'string' ? method : 'GET';
                fn = typeof fn == 'function' ? fn : function(data){var json = JSON.parse(data); console.log(json);};
                format = typeof format == 'string' ? format : 'json';
                function _serialize(params) {
                    var str = '';
                    for(var key in params) {
                        if(params.hasOwnProperty(key)) {
                            if (str !== '') str += "&";
                            str += key + "=" + params[key];
                        }
                    }
                    return str;
                }
                // Courtesy of http://www.nczonline.net/blog/2010/05/25/cross-domain-ajax-with-cross-origin-resource-sharing/
                function _createCORSRequest(method, url){
                    var xhr = new XMLHttpRequest();
                    if ("withCredentials" in xhr){
                        xhr.open(method, url, true);
                    } else if (typeof XDomainRequest != "undefined"){
                        xhr = new XDomainRequest();
                        xhr.open(method, url);
                    } else {
                        xhr = null;
                    }
                    return xhr;
                }

                var querystring = _serialize(params);
                querystring = (querystring) ? '?' + querystring + '&api_key=' + this.API_KEY + "&fmt=" + format : '?api_key=' + this.API_KEY + "&fmt=" + format;
                var request = _createCORSRequest(method, this.BASE_URL+endpoint+querystring);
                if (request) {
                    request.onload = function(){
                        fn(request.responseText);
                    };
                    request.send();
                }
            }
        };
})();