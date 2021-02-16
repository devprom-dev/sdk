// Lists
var isLoading = false,
    feedNum = 5,
    tabsNav = null,
    popupReply = null,
    projectsPopup = null,
    theme = 'ios'; //mobiscroll.autoTheme;

$(function ()
{
    // Init tabs
    $('#ham-nav').mobiscroll().nav({
        theme: theme,
        lang: window.language,
        type: 'hamburger',
        onItemTap: function(event, inst) {
            window.location = $(event.target).attr('data-url');
        }
    });

    tabsNav = $('#tabs').mobiscroll().nav({
        theme: theme,
        lang: window.language,
        onItemTap: function (event) {
            $('.app-tab-active').removeClass('app-tab-active');
            $('#tab-' + event.target.getAttribute('data-tab')).addClass('app-tab-active');

            initContainers();
        }
    }).mobiscroll('getInst');

    // Popups
    popupReply = $('#popup-reply').mobiscroll().popup({
        display: 'center',
        layout: 'liquid',
        lang: window.language,
        cssClass: 'mbsc-no-padding',
        buttons: [
            {
                text: mobiscroll.i18n[window.language]['saveButton'],
                handler: 'set'
            },
            'cancel'
        ],
        onSet: function (event, inst) {
            var url = $(inst.element).attr('data-url');
            if ( url ) {
                submitData(url, {'Caption' : $(inst.element).find('textarea').val()}, function() {
                    $(inst.element).find('textarea').val('');
                    window.location.reload();
                });
            }
        }
    }).mobiscroll('getInst');

    projectsPopup = $('#projects-popup').mobiscroll().popup({
        buttons: [],
        display: 'bottom',
        cssClass: 'mbsc-no-padding'
    }).mobiscroll('getInst');

    $('#projects-list').mobiscroll().listview({
        enhance: true,
        swipe: false,
        onItemTap: function (event, inst) {
            projectsPopup.hide();
            window.location = $(inst.element).attr('new-url') + '?project=' + $(event.target).attr('project');
        }
    });

    $('button[new-url]').on('click', function() {
        if ( $('#projects-list').find('li').length > 1 ) {
            $('#projects-list').attr('new-url', $(this).attr('new-url'));
            projectsPopup.show();
        }
        else {
            window.location = $(this).attr('new-url');
        }
    });

    $('[mbsc-form] [mbsc-card]').each(function() {
        $(this).mobiscroll('getInst').tap($(this), function(event, instance) {
            $(popupReply.element).attr('data-url', $(instance.element).attr('data-url'));
            popupReply.show();
        });
    });

    $('#submit').on('click', function() {
        var form = $('[mbsc-form][data-url]');
        if ( form.length < 1 ) return;

        var data = {};
        form.find('input[name], select[name], textarea[name]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });
        submitData(form.attr('data-url'), data);
    });

    $('#comment').on('click', function() {
        popupReply.show();
    })

    var parts = window.location.toString().split('#');
    if ( parts.length > 1 ) {
        var tab = parts[1];
        if ( $('#tab-'+tab).length > 0 ) {
            $('.app-tab-active').removeClass('app-tab-active');
            $('#tab-'+tab).addClass('app-tab-active');
        }
    }

    initContainers();
    completeUI();
});

function completeUI()
{
    $('.hie-lv').mobiscroll().listview({
        swipe: false,
        enhance: true,
        lang: window.language,
        theme: theme,
    });

    $('button[translate]').each(function() {
        $(this).html(mobiscroll.i18n[window.language][$(this).attr('translate')]);
    });

    $('input[field-type=number]').mobiscroll().numpad({
        lang: window.language,
        theme: theme,
        preset: 'decimal',
        min: 0,
        scale: 0
    });
}

function initContainers()
{
    var listElement = $('.app-tab-active ul[data-source]');
    if ( listElement.length > 0 ) {
        listElement.html('');
        listElement.mobiscroll().listview({
            theme: theme,
            striped: true,
            animateAddRemove: false,
            swipe: listElement.attr('data-id') == 'whatsnew' || listElement.attr('data-id') == 'messages',
            stages: [{
                percent: -50,
                action: dismissNotification,
                undo: false
                }, {
                percent: 50,
                action: dismissNotification,
                undo: false
            }],
            onListEnd: function (event, inst) {
                if (!isLoading) {
                    isLoading = true;
                    feedNum += 5;
                    getListviewFeed(inst);
                }
            },
            onItemTap: function (event, inst) {
                var urlElement = $(event.target).find('[data-url]');
                if ( urlElement.length > 0 && urlElement.attr("data-url") != '' ) {
                    window.location = urlElement.attr("data-url");
                }
            },
            onInit: function (event, inst) {
                getListviewFeed(inst);
            }
        });
    }

    var cardsElement = $('.app-tab-active div[data-source]');
    if ( cardsElement.length > 0 )
    {
        var loadingInst = cardsElement.prev().mobiscroll()
            .listview({
                theme: theme,
            }).mobiscroll('getInst');
        loadingInst.showLoading();

        cardsElement.hide();
        cardsElement.html('');

        $.ajax({
            url: cardsElement.attr("data-source"),
            success: function( data ) {
                var templateId = cardsElement.attr("data-template");
                var template = Handlebars.compile($('#' + templateId).html());
                try {
                    $.each(data, function(index,item) {
                        cardsElement.append(template(item));
                    });
                    cardsElement.find('.mbsc-card').mobiscroll().card({
                        theme: theme,
                        lang: window.language
                    });
                    $('.md-lv').mobiscroll().listview({
                        swipe: false,
                        enhance: true,
                        theme: theme,
                        lang: window.language,
                        onItemTap: function (event, inst) {
                            var url = $(event.target).attr('data-url');
                            if ( url ) {
                                window.location = url;
                            }
                        }
                    });

                    setTimeout(function() {
                        loadingInst.hideLoading();
                        cardsElement.show();
                        completeUI();
                    }, 800);
                }
                catch(e) {
                    console.log(e);
                }
            },
            error: function() {
            }
        });
    }
}

function getListviewFeed( list )
{
    if (list) {
        list.showLoading();
    }

    var listEl = $(list.element);

    $.ajax({
        url: listEl.attr("data-source"),
        data: {
            last: listEl.find("li[data-id]:last").attr("data-id")
        },
        success: function( data ) {
            var templateId = listEl.attr("data-template");
            var template = Handlebars.compile($('#' + templateId).html());
            try {
                $.each(data, function(index,item) {
                    if ( listEl.find('li[data-id="'+item.id+'"]').length > 0 ) {
                        list.remove(item.id);
                    }
                    list.add(item.id, template(item));
                    listEl.find('li[data-id="'+item.id+'"]')
                        .addClass("mbsc-card mbsc-form mbsc-no-touch mbsc-"+theme+" mbsc-ltr")
                        .attr("entity", item.entity);
                });
            }
            catch(e) {}
            isLoading = false;
            list.hideLoading();
            completeUI();
        },
        error: function() {
            isLoading = false;
            list.hideLoading();
        }
    });
}

function submitData( url, data, callback )
{
    $.ajax({
        url: url,
        data: JSON.stringify(data),
        method: 'POST',
        dataType: 'json',
        success: function( data ) {
            if ( data.error ) {
                mobiscroll.toast({
                    message: data.error,
                    display: 'top',
                    color: 'danger'
                });
            }
            else {
                mobiscroll.toast({
                    message: mobiscroll.i18n[window.language]['storeOk'],
                    display: 'top'
                });
                if ( callback ) {
                    callback();
                }
                else if (data.mobile) {
                    window.location = data.mobile;
                }
            }
        },
        error: function() {
            mobiscroll.toast({
                message: mobiscroll.i18n[window.language]['storeFailed'],
                display: 'top',
                color: 'danger'
            });
        }
    });
}

function dismissNotification( event, inst )
{
    var urlElement = $(event.target).find('[data-url]');
    if ( urlElement.length > 0 && urlElement.attr("data-url") != '' ) {
        inst.remove(event.target);
        $.ajax({
            url: urlElement.attr("data-url"),
            method: 'DELETE',
            success: function( data ) {
            },
            error: function() {
                inst.undo();
                mobiscroll.toast({
                    message: mobiscroll.i18n[window.language]['storeFailed'],
                    display: 'top',
                    color: 'danger'
                });
            }
        });
    }
}