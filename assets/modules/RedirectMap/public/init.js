(function($){
    $(function() {
        $.AJAXTable = {
            clickAction: function(el){
                jQuery.ajax({
                    type: 'get',
                    url: $(el).attr('href'),
                    success: function(string){
                        $("#logBlock").html(string);
                        $.AJAXTable.reloadGrid();
                    }
                });
            },
            reloadGrid: function(){
                jQuery.ajax({
                    type : 'get',
                    url  : window.location.href,
                    data : {action:'lists'},
                    success: function(string) {
                        $("#ajaxTable").html(string);
                        $(".click").editable($.URLAction.saveValue, {
                            id: 'data',
                            data: 'set',
                            type: 'text',
                            onblur: 'submit',
                            cssclass : 'editclass',
                            onsubmit: function(settings, selfObj) {
                                $("#logBlock").html('');
                                var out = false;
                                var match = $(selfObj).attr('id').match(/^(.*)_(\d+)$/);
                                if(match!==null){
                                    switch(match[1]){
                                        case 'quency':{
                                            out = /^\d+$/.test($(selfObj).find('input').val());
                                            if(!out){
                                                alert('Необходимо ввести число');
                                            }
                                            break;
                                        }
                                        case 'key':{
                                            jQuery.ajax({
                                                type: "post",
                                                url: $.URLAction.checkUniq,
                                                async: false,
                                                data: {
                                                    data: $(selfObj).attr('id'),
                                                    value: $(selfObj).find('input').val()
                                                },
                                                success: function(string){
                                                    if(string != 'true'){
                                                        $(selfObj).find('input').addClass('errorInput');
                                                        alert(string);
                                                    }else{
                                                        out = true;
                                                    }
                                                }
                                            });
                                            break;
                                        }
                                        default:{
                                            out = true;
                                        }
                                    }
                                }
                                return out;
                            },
                            loadtype: 'POST',
                            loadurl  : $.URLAction.getValue,
                            indicator : "<img src='/assets/modules/RedirectMap/public/jeditable/img/indicator.gif'>",
                            placeholder: "Для редактирования нужно кликнуть...",
                            loadtext: "Загрузка...",
                            tooltip   : "Для редактирования нужно кликнуть...",
                            style  : "inherit",
                            callback : function(value, settings) {
                                $.AJAXTable.reloadGrid();
                            }
                        });
                    }
                });
            }
        };

        $.AJAXTable.reloadGrid();
        $('#ajaxTable').on('click', '.fulldel_action', function(e){
            $.AJAXTable.clickAction($(this));
            e.preventDefault();
        });
        $('#ajaxTable').on('click', '.is_active', function(e){
            $.AJAXTable.clickAction($(this));
            e.preventDefault();
        });
    });
})(jQuery);