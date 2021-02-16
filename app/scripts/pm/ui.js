var uiOptions = {
    x: 0,
    y: 0,
    left: 0,
    down: false
};

function makeUpApp()
{
    if ( !devpromOpts.uiExtensionsEnabled ) return;

    var jqe = $(document);

    filterLocation.makeup();

    var clipboard = new Clipboard('.clipboard');
    clipboard.on('success', function(event) {
        var target = $(event.trigger);
        try {
            target.popover({
                'content': target.attr('data-message'),
                'title': function() {
                    return '';
                },
                'placement': 'top',
                'container': 'body'
            });
            target.popover('show');
        }
        catch(e) {
        }
        setTimeout(function() {target.popover('hide');}, 4000);
        event.clearSelection();
    });

    var defaultSliderPos = cookies.get('list-slider-pos') != null
        ? cookies.get('list-slider-pos') : 2;
    $('#list-slider').slider({
        value: defaultSliderPos,
        min: 1,
        max: 2,
        step: 1,
        slide: function( event, ui ) {
            setDocumentListSize(ui.value);
        }
    });

    jqe.find('.dropdown-item-search input')
        .on('click', function(e) {
            e.stopImmediatePropagation();
            return false;
        })
        .on('keyup', function(e) {
            var parentNode = $(this).parents('ul.dropdown-menu');
            var items = parentNode.find('li:not([uid=none]):not([uid=all]):not([uid=search]):not([uid=show-all]):not(.divider):not(:has(a.checked))');
            var text = $(this).val();
            if ( text == "" ) {
                var visibleItems = items;
            }
            else {
                var visibleItems = items.filter(function(i, el) {
                    return $(el).text().match(new RegExp(escapeRegExp(text), "ig"));
                });
            }
            items.hide();
            visibleItems.show();
        })
        .trigger('keyup');

    jqe.find('.dropdown-item-all a')
        .on('click', function(e) {
            $(this).parents('ul.dropdown-menu').find('li').show();
            $(this).parent().hide();
            e.stopImmediatePropagation();
            return false;
        });

    jqe.find('.project-search input')
        .on('click', function(e) {
            e.stopImmediatePropagation();
            return false;
        })
        .on('keyup', function(e) {
            var items = $(this).parents('table').find('tr:eq(1) .p-link');
            items.removeClass('p-found').hide();
            if ( $(this).val() == "" ) {
                var visibleItems = $(this).parents('table').find('.p-left-recent .p-link, .p-node');
            }
            else {
                var text = $(this).val();
                var visibleItems = items.filter(function(i, el) {
                    return $(el).text().match(new RegExp(text, "ig")) && !$(el).hasClass('p-recent');
                });
                visibleItems.addClass('p-found');
            }
            visibleItems.show();
            $('#project-list-all').show();
        })
        .trigger('keyup');

    jqe.find('#project-list-all').click(function(){
        $('tr:eq(1) .p-link').show();
        $(this).hide();
        $('.project-search input').val('');
    });

    jqe.find('#rightTab a:first').tab('show');
    jqe.find('#rightTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    jqe.find('.search-query').each( function() {
        quickSearchAutoComplete($(this));
    });

    jqe.resize( function(e) {
        var tbl = $('.wiki-page-document .table-inner-div');
        if ( tbl.length > 0 ) {
            var height = $(window).height() - tbl.offset().top - 15;
            var hintHolder = $('.wiki-page-document .hint-holder');
            if ( hintHolder.length > 0 ) {
                height -= hintHolder.height();
            }
            tbl.height(height);
            if ( documentOptions.scrollbar ) {
                documentOptions.scrollbar.update();
            }
        }
    });

    setInterval(function() {
        var txt = cookies.get('last-action-message');
        if ( txt && txt != "" ) {
            if ( !lastActionBar ) {
                lastActionBar = new $.peekABar({
                    cssClass: 'alert alert-success',
                    backgroundColor: '#dff0d8',
                    animation: {
                        type: 'fade',
                        duration: 450
                    },
                    delay: 8000,
                    html: txt.replace(/\+/g, ' '),
                    autohide: true,
                    closeOnClick: true,
                    onHide: function() {
                        cookies.set('last-action-message', '');
                        lastActionBar = null;
                    }
                });
            }
            if ( !$('.peek-a-bar').is(':visible') ) {
                var width = Math.max($(window).width() * 1 / 3, 600);
                $('.peek-a-bar').css({
                    width: width,
                    left: ($(window).width() - width) / 2
                });
                lastActionBar.show();
            }
        }
    }, 1000);

    $('#quick-search').popover({
        placement: 'bottom',
        container: 'body',
        html: true,
        trigger: 'focus'
    });
    $('.vertical-menu-short a').tooltip({
        placement: 'right',
        container: 'body'
    });
    $('#main-sidebar ul a').tooltip({
        placement: 'right',
        container: 'body'
    });
    $('.vertical-menu-short a:not([module])').popover({
        placement: 'right',
        container: 'body',
        html: true
    });

    $('.autosave-input').change( function() {
        var data = $.parseJSON($(this).attr('data-save'));
        data = $.extend(data, {value: $(this).val()});
        runMethod( $(this).attr('data-href'), data, 'donothing', '' );
    });

    highlightComment();

    $("body").on("contextmenu", "#tablePlaceholder .table-inner tr[object-id]", function(e)
    {
        if ( $(e.target).closest('a, .btn-group').length > 0 ) return;
        $('.dropdown-fixed.open, .btn-group.open').removeClass('open');
        var row = $(this).find('td#operations');
        var item = row.find('.dropdown-menu')
        if ( item.length > 0 ) {
            row.find('.dropdown-fixed').detach();
            var cont = $('<div class="btn-group dropdown-fixed open"></div>').prependTo(row);
            item.first().clone().prependTo(cont);
            cont.css({
                left: e.originalEvent.clientX,
                top: e.originalEvent.clientY,
            });
            if ( !cont.find('ul>li:last').isInViewport() ) {
                cont.find('ul').css({
                    top: 'unset',
                    bottom: 0
                });
            }
            if ( !cont.find('ul>li:first').isInViewport() ) {
                cont.find('ul').css({
                    top: '',
                    bottom: ''
                });
                cont.css({
                    top: 0
                });
            }
            return false;
        }
    })
        .on("keydown", function(e) {
            if ( e.which == 39 || e.which == 37 ) {
                $('.table-master:visible, .wysiwyg:not([contenteditable])').animate({
                    scrollLeft: e.which == 39 ? '+=50' : '-=50'
                }, 0, 'swing');
            }
        });

    var keywords_stored = $('td#search-area input').val();
    window.setInterval(function() {
        if ( $('td#search-area input').val() != keywords_stored ) {
            keywords_stored = $('td#search-area input').val();
            $('td#search-area input').trigger('onchange');
        }
    }, 500);

    if ( $('.page-details .details-body').length > 0 ) {
        const detailsPerfect = new PerfectScrollbar('.page-details .details-body', {
            suppressScrollX: true
        });
    }
    if ( $('#tablePlaceholder.placeholder-fixed .table-master').length > 0 ) {
        const tableMasterPerfect = new PerfectScrollbar('#tablePlaceholder.placeholder-fixed .table-master');
    }
    if ( $('.form-container').length > 0 ) {
        const formContainerPerfect = new PerfectScrollbar('.form-container', {
            suppressScrollX: true
        });
    }
    if ( $('.treeview-container').length > 0 ) {
        const treeContainerPerfect = new PerfectScrollbar('.treeview-container');
    }
    if ( $('ul.navbar-menu').length > 0 ) {
        const menuPerfect = new PerfectScrollbar('ul.navbar-menu');
    }
    if ( $('.right-side-tab .tab-pane').length > 0 ) {
        $('.right-side-tab .tab-pane').each(function() {
            new PerfectScrollbar(this);
        });
    }

    $('#tablePlaceholder.placeholder-fixed .table-master')
        .mousedown(function(e){
            if ( !$(e.target).is('tr,td,th,.list_cell') ) return true;
            uiOptions.down = true;
            uiOptions.x = e.pageX;
            uiOptions.y = e.pageY;
            uiOptions.left = $(this).scrollLeft();
        });

    $("body").mousemove(function(e){
            if( uiOptions.down ) {
                $("#tablePlaceholder.placeholder-fixed .table-master").scrollLeft(uiOptions.left - e.pageX + uiOptions.x);
            }
        })
        .mouseup(function(e) {
            uiOptions.down = false;
        });

    filterComments();
}

function completeChartsUI( jqe )
{
    jqe.find('.plot[url]').bind("plotclick", function (event, pos, item) {
        window.location = $(this).attr('url');
    }).css('cursor', 'pointer');

    jqe.find('.plot-wide').each(function(index) {
        $(this).css('width', $('#tablePlaceholder').width() - 20);
    });

    jqe.find('.plot').each(function(index) {
        $(this).bind("plotclick", function (event, pos, item) {
            if (!item) return;
            if ( typeof item.series.urls != 'undefined' )
            {
                var url = item.series.urls[item.datapoint[0]];
                if ( typeof url != 'undefined' ) window.location = url;
            }
        });
        $(this).bind("plothover", function (event, pos, item) {
            if ( pos && pos.x ) $("#x").text(pos.x.toFixed(2));
            if ( pos && pos.y ) $("#y").text(pos.y.toFixed(2));
            if ( item ) {
                var xValue = '';
                var yValue = '';
                switch( typeof item.datapoint[0] )
                {
                    case 'number':
                        if ( item.datapoint[0] > 1000000 ) {
                            var dt = new Date(item.datapoint[0]);
                            xValue = dt.toString(devpromOpts.datejsformat);
                        }
                        else {
                            xValue = item.datapoint[0];
                        }
                        break;
                    default:
                        xValue = item.datapoint[0];
                }
                if ( typeof bar_labels != 'undefined' ) {
                    xValue = bar_labels[item.datapoint[0]];
                }
                if ( xValue == "" && typeof item.series.xaxis.ticks != 'undefined' && item.series.xaxis.ticks.length > 0 ) {
                    if ( typeof xValue == 'number' ) xValue = item.series.xaxis.ticks[xValue].label;
                }
                else if ( typeof item.series.label != 'undefined' ) {
                    yValue = item.series.data[item.dataIndex][1];
                }
                else {
                    yValue = "";
                }

                if ( typeof item.series.axisDescription != 'undefined' ) {
                    if ( typeof item.series.axisDescription.xaxis != 'undefined' ) {
                        xValue = item.series.axisDescription.xaxis + ": " + xValue;
                    }
                    else {
                        xValue = "";
                    }
                    if ( typeof item.series.axisDescription.yaxis != 'undefined' ) {
                        yValue = item.series.axisDescription.yaxis + ": " + item.series.data[item.dataIndex][1];
                    }
                    else {
                        yValue = "";
                    }
                }
                var text = (typeof item.series.label != 'undefined' ? item.series.label + ": " : "")
                    + yValue + ( xValue != '' ? " [" + xValue + "]" : "" );

                showFlotTooltip(item.pageX, item.pageY, text, 'flottooltip' + item.dataIndex + item.seriesIndex);
            }
            else {
                $(".charttooltip").remove();
            }
        });
    });
}

function completeUICustomFields( formId, anchor, fields, values )
{
    $('#'+formId+' '+anchor).change( function() {
        jQuery.each(fields, function(key, value) {
            $('#'+formId+' #fieldRow'+value).hide();
        });
        var selected = '';
        var option = $(this).find('option[value="'+$(this).val()+'"]');
        if ( option.length > 0 ) {
            selected = option.attr('referenceName');
        }
        else {
            selected = $(this).attr('referenceName');
        }
        jQuery.each(fields, function(key, value) {
            if ( selected == values[key] ) $('#'+formId+' #fieldRow'+value).show();
        });
    });
    $(anchor).change();
}


function completeUIView(jqe)
{
    jqe.find('.treeview.sticks-top').css({
        height: $(window).height()
    });

    toggleLoadMoreButton();

    var options = $.extend({},devpromOpts.datepickerOptions);
    jqe.find( ".datepicker-filter" ).datepicker(
        $.extend( options, devpromOpts.datepickerFilterOptions )
    );
    jqe.find("img.wiki_page_image").each( function() {
        if ( $.browser.msie ) {
            this.setAttribute('href', $(this).attr('src'));
        } else {
            this.href = $(this).attr('src');
        }
    });
    jqe.find("img.wiki_page_image").fancybox({
        hideOnContentClick: true
    });

    jqe.find('td').each(function()
    {
        $(this).find('.diff-html-added').css('background', '#90EC90');
        $(this).find('.diff-html-removed').css('background', '#F59191');
    });

    jqe.find('.comments-toolbar-btn').click(function() {
        $(this).parent().is('.collapsed')
            ? $(this).parent().removeClass('collapsed')
            : $(this).parent().addClass('collapsed');
    });
}

function completeUIControls(jqe)
{
    jqe.find('textarea.wysiwyg,div[contenteditable]').each(function() {
        if ( $(this).parents('#documentCache,.embedded_form').length < 1 ) {
            if ( $(this).is('.wysiwyg-text') ) {
                $(this).parent()
                    .hover(function() {
                            if ( !$(this).find('.wysiwyg-text').is('.cke_focus') ) $(this).addClass('wysiwyg-hover');
                            $('.wysiwyg-welcome[for-id='+$(this).attr('id')+']').css('border-top', '2px solid white');
                        },
                        function() {
                            $(this).removeClass('wysiwyg-hover');
                            $('.wysiwyg-welcome[for-id='+$(this).attr('id')+']').css('border', 'none');
                        });
            }
            setupWysiwygEditor($(this).attr('id'), $(this).attr('toolbar'),
                $(this).attr('userHeight'), $(this).attr('modifyUrl'),
                $(this).attr('appVersion'), $(this).attr('project'));
        }
    });

    setTimeout(function() {
        jqe.find("img[alt]:visible").imageLens({ lensSize: 311 });
    }, 1000);

    jqe.find("[data-fancybox='gallery']:visible").fancybox({
        'hideOnContentClick': true
    });

    jqe.find('.with-popover').popover({
        placement: 'bottom',
        html: true
    });

    jqe.find('.with-tooltip').popover({
        placement: function() {
            if ( $(this.$element).is('[placement]') ) return $(this.$element).attr('placement');
            return $(this.$element).offset().left < $(window).width() / 2 ? 'right' : 'left';
        },
        html:true,
        trigger: 'manual',
        container: 'body'
    });

    jqe.find('a.with-tooltip[info]')
        .hover(
            function(e) {
                showTooltip($(this));
            }, function(e) {
                $(this).data('popover').hide();
            }
        )
        .bind('contextmenu', function(e) {
            e.stopPropagation();
        });

    jqe.find("a.modify_image").click( function(e) {
        window.location = $(this).attr('href');
        e.stopImmediatePropagation();
    });

    jqe.find('.collapse')
        .on('show', function() {
            var element = $(this);
            window.setTimeout( function() { if ( element.hasClass('in') ) { element.css('overflow', 'visible'); }}, 500 );
        })
        .on('hide', function() {
            $(this).css('overflow', 'hidden');
        });

    jqe.find('[data-toggle="popover"]').popover({
        trigger: 'hover'
    });
    $('[data-toggle="tooltip-bottom"]').tooltip({
        placement: 'bottom',
        container: 'body'
    });
}

function completeUIForm(jqe)
{
    jqe.find('.form-container,.details-body').scroll( function(e) {
        processStickedLayout($(this));
    });

    $('select.dictionary[multiple]').multiselect({
        buttonClass: 'btn btn-light',
        numberDisplayed: 0,
        enableHTML: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        filterPlaceholder: text('ms-search'),
        nonSelectedText: '...',
        buttonContainer: '<div class="btn-group input-block-level" />',
        templates: {
            filterClearBtn: '',
        },
        buttonText: function(options, select) {
            var labels = [];
            var separator = $(select).is('[newline]') ? '<br/>' : ', ';
            options.each(function () {
                labels.push($(this).text());
            });
            if ( labels.length < 1 ) return '...';
            return labels.join(separator);
        }
    });

    jqe.find('[name*=Capacity]').keydown(function() {
        updateLeftWork($(this));
    });

    jqe.find('.file-browse').each(function() {
        uploadFiles($(this));
    });

    jqe.find('.file-drop-target').bind('dragover', function(){
        $(this).addClass('drag-over');
    });
    jqe.find('.file-drop-target').bind('dragleave', function(){
        $(this).removeClass('drag-over');
    });

    jqe.find( ".datepicker" ).datepicker(devpromOpts.datepickerOptions);

    jqe.find("input[placeholder!=''], textarea[placeholder!='']").each( function() {
        $(this).keypress( function() {
            if ( $(this).val() != $(this).attr('placeholder') ) {
                $(this).removeClass('ac_welcome');
            }
            else {
                $(this).addClass('ac_welcome');
            }
        })
            .blur( function() {
                if ( $(this).val() == $(this).attr('placeholder') ) {
                    $(this).addClass('ac_welcome');
                }
            });

        if ( $(this).val() == $(this).attr('placeholder') ) $(this).addClass('ac_welcome');
    });

    if ( !$.browser.msie ) {
        jqe.find("input:file:not([multiple])").filestyle({
            classInput: 'custom-file',
            classButton: 'btn btn-light custom-file',
            buttonText: '',
            icon: true,
            classIcon: 'icon-folder-open'
        });
    }
    else {
        jqe.find("input:file").css({'width':'100%'});
    }

    jqe.find('.fieldautocompleteobject').each( function() {
        if ( $(this).attr('object') == '' || $(this).attr('id') == '' ) return true;

        objectAutoComplete(
            $(this),
            $(this).attr('object'),
            $(this).attr('caption'),
            $(this).attr('searchattrs').split(','),
            $(this).attr('project')
        );
    });
}

function completeUIExt( jqe )
{
    completeChartsUI(jqe);
    completeUIView(jqe);
    completeUIForm(jqe);
    completeUIControls(jqe);
    makeupAnnotations(jqe, false);
}
