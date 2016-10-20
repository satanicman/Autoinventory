$(document).ready(function () {
    tinyMCE.init({
        mode: "none",
        theme: "advanced",
        plugins: "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen",
        // Theme options
        theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,cleanup,code,,|,forecolor,backcolor",
        theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl",
        theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak,|,fullscreen,|,anchor",
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "bottom",
        theme_advanced_resizing: false,
        content_css: baseUri + "themes/default-bootstrap/css/tinymce.css",
        document_base_url: baseUri ,
        width: "100%",
        height: "auto",
        font_size_style_values: "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
        elements: "nourlconvert",
        convert_urls: false,
        language: "en"
    });
    tinyMCE.execCommand("mceAddControl", true, "description");

    $('#filer_input').filer({
        limit: '5',
        size2: '5',
        fileMaxSize:'5',
        maxSize: '25',
        extensions: ['jpg', 'jpeg', 'png', 'gif'],
        changeInput: '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-folder"></i></div><div class="jFiler-input-text"><h3>Click on this box</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
        showThumbs: true,
        theme: "dragdropbox",
        templates: {
            box: '<ul class="jFiler-items-list jFiler-items-grid"></ul>',
            item: '<li class="jFiler-item">\
                    <div class="jFiler-item-container">\
                        <div class="jFiler-item-inner">\
                            <div class="jFiler-item-thumb">\
                                <div class="jFiler-item-status"></div>\
                                <div class="jFiler-item-info">\
                                    <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    <span class="jFiler-item-others">{{fi-size2}}</span>\
                                </div>\
                                {{fi-image}}\
                            </div>\
                            <div class="jFiler-item-assets jFiler-row">\
                                <ul class="list-inline pull-left"></ul>\
                                <ul class="list-inline pull-right">\
                                    <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                </ul>\
                            </div>\
                        </div>\
                    </div>\
                </li>',
            itemAppend: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        <span class="jFiler-item-others">{{fi-size2}}</span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
            itemAppendToEnd: false,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-items-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            },
            dragDrop: {
                dragEnter: null,
                dragLeave: null,
                drop: null,
            },
            uploadFile: {
                url: location.href,
                data: {
                    download : 1,
                    dn : $('#dn').val()
                },
                type: 'POST',
                enctype: 'multipart/form-data',
                beforeSend: function(){},
                success: function(data, el){
                    var parent = el.find(".jFiler-jProgressBar").parent();
                    el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                        jQuery("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                    });
                },
                error: function(el){
                    var parent = el.find(".jFiler-jProgressBar").parent();
                    el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                        jQuery("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
                    });
                },
                statusCode: null,
                onProgress: null,
                onComplete: null
            },
            files: null,
            addMore: false,
            clipBoardPaste: true,
            excludeName: null,
            beforeRender: null,
            afterRender: null,
            beforeShow: null,
            beforeSelect: null,
            onSelect: null,
            afterShow: null,
            onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
                jQuery('#submit-button').attr('disabled','disable');
                console.log(file);
                var file = file.name;
                jQuery.post(location.href, {file: file, remove: 1, dn : $('#dn').val()});
                jQuery(document).ajaxComplete(function(event,request,settings ) {
                    jQuery('#submit-button').removeAttr('disabled');
                });
            },
            onEmpty: null,
            options: null,
            captions: {
                button: "Choose Files",
                feedback: "Choose files To Upload",
                feedback2: "files were chosen",
                drop: "Drop file here to Upload",
                removeConfirmation: "Are you sure you want to remove this file?",
                errors: {
                    filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                    filesType: "Only Images are allowed to be uploaded.",
                    filesSize: "{{fi-name}} is too large! Please upload file up to 5 MB.",
                    filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
                }

            }
        }
    });
});