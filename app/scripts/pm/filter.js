var filterXHR = null;
var filterLocation = {
    setup: function( value, timeout )
    {
        var values = value.split('=');

        this.parms[values[0]] = values[1];

        // do nothing if there were no changes in a filter
        var urlIsChanged =
            this.location.split(/#/).shift() != window.location.toString().split(/#/).shift();

        if ( value == '' && !urlIsChanged ) return;

        // cancel of data refresh
        this.cancel();

        // build updated location
        this.location = updateLocation( value, this.location );

        // do nothing if application is waiting for user activity
        if ( timeout == 0 ) return;

        if ( typeof timeout == 'undefined' ) timeout = 1500;

        var location = this.location.indexOf('&filterlocation') < 0 ? this.location+"&filterlocation" : this.location;

        this.timeout = window.setTimeout(
            " window.location = '"+location.replace(/\\/, "\\\\\\\\")+"'; ", timeout );
    },

    resetFilter: function()
    {
        for ( var key in this.parms )
        {
            if ( key == '' ) continue;
            if ($.inArray(key,['viewmode','viewpages','treeoptions','show','hide','group','sort','sort2','sort3','sort4','infosections','color','rows']) >= 0) {
                this.location = this.location.replace(new RegExp(key+'=[^\\&]*\\&?', 'i'), '');
                continue;
            }
            if ($.inArray(this.parms[key],['','all','hide']) >= 0) continue;
            this.location = updateLocation( key+'=all', this.location );
        }
        this.location = updateLocation( 'ids=', this.location );
        window.location = this.location;
    },

    restoreFilter: function()
    {
        for ( var key in this.parms )
        {
            var re = new RegExp('[\\?\\&]'+key+'=[^\\&]*', 'gi');
            if ( re.exec( this.location ) ) {
                this.location = updateLocation( key+'=', this.location );
            }
        }
        window.location = this.location;
    },

    restoreColumns: function()
    {
        if ( this.visibleColumns.length < 1 )
        {
            var re = new RegExp('show=([^\\&]+)');
            var match = re.exec( this.location );

            if ( match != null )
            {
                this.visibleColumns = match[1].split('-');
            }
        }

        if ( this.hiddenColumns.length < 1 )
        {
            var re = new RegExp('hide=([^\\&]+)');
            var match = re.exec( this.location );

            if ( match != null )
            {
                this.hiddenColumns = match[1].split('-');
            }
        }
    },

    showColumn: function ( name, timeout )
    {
        this.restoreColumns();

        var columns = [];
        for ( var i = 0; i < this.hiddenColumns.length; i++ )
        {
            if ( this.hiddenColumns[i] == name ) continue;
            columns.push( this.hiddenColumns[i] );
        }

        this.hiddenColumns = columns;
        found = false;

        for ( var i = 0; i < this.visibleColumns.length; i++ )
        {
            if ( this.visibleColumns[i] == name ) {
                found = true;
                break;
            }
        }

        if ( !found )
        {
            this.visibleColumns.push( name );
        }

        this.setup( 'show=' + this.visibleColumns.join('-'), timeout );
        this.setup( 'hide=' + this.hiddenColumns.join('-'), timeout );
    },

    hideColumn: function ( name, timeout )
    {
        this.restoreColumns();

        var columns = [];
        for ( var i = 0; i < this.visibleColumns.length; i++ )
        {
            if ( this.visibleColumns[i] == name ) continue;
            columns.push( this.visibleColumns[i] );
        }

        this.visibleColumns = columns;
        found = false;

        for ( var i = 0; i < this.hiddenColumns.length; i++ )
        {
            if ( this.hiddenColumns[i] == name ) {
                found = true;
                break;
            }
        }

        if ( !found )
        {
            this.hiddenColumns.push( name );
        }

        this.setup( 'show=' + this.visibleColumns.join('-'), timeout );
        this.setup( 'hide=' + this.hiddenColumns.join('-'), timeout );
    },

    turnOn: function ( parm, name, timeout )
    {
        if ( name == '' || name == 'all' ) {
            this.setup( parm+'='+name, timeout );
            return;
        }
        if ( typeof this.parms[parm] == 'undefined' ) {
            this.parms[parm] = 'all';
        }

        var values = [];
        var names = name.split(',');
        if ( this.parms[parm] != 'none' && this.parms[parm] != 'all' ) {
            var values = this.parms[parm].split(',');
        }
        this.setup( parm+'='+$.grep($.unique($.merge(values, names)),function(n){ return n != '' }).join(','), timeout );
    },

    turnOff: function ( parm, name, timeout )
    {
        if ( name == 'all' ) {
            this.setup( parm+'=', timeout );
            return;
        }
        if ( typeof this.parms[parm] == 'undefined' ) {
            this.parms[parm] = 'all';
        }

        var values = this.parms[parm].split(',');
        var names = name.split(',');

        var newvalues = [];

        for ( var i = 0; i < values.length; i++ )
        {
            if ( $.inArray(values[i], names) < 0 ) newvalues.push( values[i] );
        }

        this.setup( parm+'='+(newvalues.length < 1 ? 'none' : newvalues.join(',')), timeout );
    },

    setSort: function( sort_parm, field )
    {
        if ( $('li[uid='+sort_parm+'-a]>a').hasClass('checked') )
        {
            value = field+".A";
        }

        if ( $('li[uid='+sort_parm+'-d]>a').hasClass('checked') )
        {
            value = field+".D";
        }

        this.setup( sort_parm+'='+value, 1 );
    },

    setSortType: function( sort_parm, sort_type )
    {
        var parts = this.parms[sort_parm].split('.');

        if ( sort_type == 'asc' )
        {
            this.setup( sort_parm+'='+parts[0]+".A", 1 );
        }

        if ( sort_type == 'desc' )
        {
            this.setup( sort_parm+'='+parts[0]+".D", 1 );
        }
    },

    cancel: function()
    {
        if ( typeof this.timeout == "number" ) {
            window.clearTimeout( this.timeout );
            delete this.timeout;
        }
    },

    locationTableOnly: function()
    {
        var url = this.location;

        var items = url.split('#');

        url = items[0];

        if ( url.indexOf('?') < 0 )
        {
            url += '?tableonly=true';
        }
        else
        {
            url += '&tableonly=true';
        }

        var re = new RegExp('offset1=([^\\&]+)');
        var match = re.exec( url );
        if ( match ) {
            url += '&offset1=' + match[1];
        }

        return url;
    },

    getParametersString: function ()
    {
        var keys = [];

        for ( var key in this.parms )
        {
            if ( key == '' ) continue;

            keys.push(key);
        }

        return keys.join(',');
    },

    getValuesString: function()
    {
        var values = [];

        for ( var key in this.parms )
        {
            if ( key == '' ) continue;

            values.push(this.parms[key]);
        }

        return values.join(';');
    },

    getEmptyValuesString: function()
    {
        var values = [];

        for ( var key in this.parms )
        {
            if ( key == '' ) continue;

            values.push('');
        }

        return values.join(';');
    },

    location: window.location.toString(),
    visibleColumns: [],
    hiddenColumns: [],
    parms: [],
    filterPopover: null,

    datepickerDaysButton: function(input, days, title) {
        setTimeout(function () {
            var buttonPane = $(input).datepicker("widget").find(".ui-datepicker-buttonpane");
            var date = Date.today().addDays(days);
            var dateValue = '';
            switch( days ) {
                case -365:
                    dateValue = 'last-year';
                    break;
                case -90:
                    dateValue = 'last-quarter';
                    break;
                case -30:
                    dateValue = 'last-month';
                    break;
                case -7:
                    dateValue = 'last-week';
                    break;
                case 7:
                    dateValue = 'next-week';
                    break;
            }
            $("<button>", {
                text: title,
                click: function () {
                    var i = $(input);
                    i.datepicker('setDate', date);
                    i.datepicker('hide');
                    if ( dateValue != '' ) {
                        i.attr("date-key", dateValue);
                    } else {
                        i.removeAttr("date-key");
                    }
                }
            }).appendTo(buttonPane).addClass("ui-datepicker-clear ui-state-default ui-priority-primary ui-corner-all");
        }, 1);
    },

    buildPopover: function(popoverElement, container, d, parameter) {
        var self = this;
        return popoverElement.popover({
            content: d,
            placement: 'bottom',
            html:true,
            trigger: 'click',
            //container: container
        })
        .on('show.bs.popover', function(e) {
            setTimeout(function() {
                container.find('#btnSave').click(function() {
                    var parmValues = [];
                    $(this).parents('.popover-content').find('select[name],input[name]').each(function() {
                        var val = String($(this).val() ? $(this).val() : 'all').trim();
                        if ( $(this).is('.datepicker-filter[date-key]') ) {
                            val = $(this).attr("date-key");
                        }
                        if ( parameter != '' ) {
                            parmValues.push($(this).attr('name') + ':' + val);
                        }
                        else {
                            self.setup($(this).attr('name') + '=' + val, 0);
                        }
                    });
                    if ( parmValues.length > 0 ) {
                        self.setup(parameter + '=' + parmValues.join(';'), 1);
                    }
                    else {
                        self.setup('', 1);
                    }
                    popoverElement.popover('hide');
                });
                container.find('#btnClose').click(function() {
                    popoverElement.popover('hide');
                });

                container.find('select.filter').multiselect({
                    buttonClass: 'btn btn-block',
                    numberDisplayed: 0,
                    enableFiltering: true,
                    enableCaseInsensitiveFiltering: true,
                    filterPlaceholder: text('ms-search'),
                    resetText: text('ms-reset'),
                    nonSelectedText: '...',
                    includeResetOption: true,
                    includeResetDivider: true,
                    selectAllNumber: false,
                    allSelectedText: false,
                    buttonContainer: '<div class="btn-group input-block-level" />',
                    templates: {
                        filterClearBtn: '',
                        resetButton: '<li class="multiselect-reset"><div class="input-group"><a class="btn btn-xs btn-link"></a> <a target="_blank" class="stg btn btn-xs width: 50px;btn-link"></a></div></li>'
                    },
                    onInitialized: function(select, container) {
                        configureMultiselect(select, container);
                        var settingsUrl = $(select).attr('settings-url');
                        if ( settingsUrl != '' ) {
                            $(container).find('.multiselect-reset a.stg')
                                .attr('href', settingsUrl).html(text('ms-settings'));
                        }
                        else {
                            $(container).find('.multiselect-reset a.stg').detach();
                        }
                        this.options.setButtonClass(select, this.getSelected().length);

                        var lazyUrl = $(select).attr('lazyurl');
                        var lastSearchText = '';

                        if ( lazyUrl ) {
                            $(container).find('input.multiselect-search').on('keydown', function(e) {
                                if ( lastSearchText == $(this).val() ) return;
                                lastSearchText = $(this).val();

                                if ( filterXHR ) {
                                    filterXHR.abort();
                                    filterXHR = null;
                                }

                                var le = $(container).find('ul');
                                le.find('li:not([class]),li.loader').remove();
                                le.append('<li class="loader"><img src="/images/ajax-loader.gif"></li>');

                                filterXHR = $.getJSON(lazyUrl + '&term=' + (lastSearchText == "" ? "+" : lastSearchText), function(d) {
                                    le.find('li.loader').remove();
                                    var select = le.parents('.multiselect-native-select').find('select');
                                    $.each(d, function(key,item) {
                                        le.append('<li><a><label class="checkbox"><input type="checkbox" value="'+item.id+'">'+item.label+'</label></a></li>');
                                        select.append('<option value="'+item.id+'">'+item.label+'</option>');
                                    });
                                });
                            })
                        }
                    },
                    onChange: function(option, checked, select) {
                        this.options.setButtonClass($(option).parent(), this.getSelected().length);
                    },
                    setButtonClass: function(select, selected) {
                        var button = $(select).parents('.multiselect-native-select').find('button.dropdown-toggle');
                        if ( selected > 0 ) {
                            button.addClass('btn-info');
                            button.removeClass('btn-block');
                        }
                        else {
                            button.removeClass('btn-info');
                            button.addClass('btn-block');
                        }
                    }
                });

                var options = $.extend({},devpromOpts.datepickerOptions);
                container.find(".datepicker-filter").datepicker(
                    $.extend( options, {
                        beforeShow: function (input) {
                            options.datepickerClearButton(input);
                            self.datepickerDaysButton(input, 7, text('datepicker.nextmonth.btn'));
                            self.datepickerDaysButton(input, -7, text('datepicker.prevweek.btn'));
                            self.datepickerDaysButton(input, -30, text('datepicker.prevmonth.btn'));
                            self.datepickerDaysButton(input, -90, text('datepicker.prevquart.btn'));
                            self.datepickerDaysButton(input, -365, text('datepicker.prevyear.btn'));
                        },
                        onChangeMonthYear: function (yy, mm, inst) {
                            var input = inst.input;
                            options.datepickerClearButton(input);
                            self.datepickerDaysButton(input, 7, text('datepicker.nextmonth.btn'));
                            self.datepickerDaysButton(input, -7, text('datepicker.prevweek.btn'));
                            self.datepickerDaysButton(input, -30, text('datepicker.prevmonth.btn'));
                            self.datepickerDaysButton(input, -90, text('datepicker.prevquart.btn'));
                            self.datepickerDaysButton(input, -365, text('datepicker.prevyear.btn'));
                        }
                    })
                );

                container.find('.row-fluid').fadeTo('fast', 1);
            }, 1);
        });
    },

    showPopover: function() {
        if ( $('.popover').length > 0 ) return;
        if ( this.filterPopover ) this.filterPopover.popover('show');
    },

    hidePopover: function() {
        if ( this.filterPopover ) this.filterPopover.popover('hide');
    },

    makeup: function() {
        var self = this;

        $('.btn-close').on('click', function(e) {
            e.preventDefault();
            self.setup($(this).attr('parm-name')+'=all',1);
        });

        var url = window.location.toString().replace('#','');
        if ( url.indexOf('?') < 0 ) {
            url += '?settings=true';
        }
        else {
            url += '&settings=true';
        }
        $.get(url, function(d) {
            if ($(d).find('select[name],input[name]').length < 1) return;
            self.filterPopover = self.buildPopover($('input.search'), $('#page-filter'), d, '');
        });

        $('#filter-settings').click(function() {
            pageSettings();
        })
    }
};

function showFilterSettingsPopover(e, parameter)
{
    var url = window.location.toString().replace('#','');
    if ( url.indexOf('?') < 0 ) {
        url += '?settings=true';
    }
    else {
        url += '&settings=true';
    }
    url += '&parameter='+parameter;

    $.get(url, function(d) {
        $('#page-filter').append("<div id='page-filter-"+parameter+"'></div>");
        filterLocation.buildPopover($('#page-filter-'+parameter), $('body'), d, parameter).popover('show');
    });
}

function pageSettings()
{
    beforeUnload();
    if ( $('#modal-form').length > 0 ) {
        if ( !workflowHandleBeforeClose() ) {
            return;
        }
    }

    var form_url = window.location.toString().replace('#','');
    if ( form_url.indexOf('?') < 0 ) {
        form_url += '?view-settings=true';
    }
    else {
        form_url += '&view-settings=true';
    }

    $.ajax({
        type: "GET",
        url: form_url,
        dataType: "html",
        async: true,
        cache: false,
        proccessData: false,
        success:
            function(result, status, xhr)
            {
                workflowCloseDialog();

                if ( xhr.getResponseHeader('status') == '500' ) {
                    window.location = '/500';
                }
                if ( xhr.getResponseHeader('status') == '404' ) {
                    return;
                }

                $('body').append('<div id="modal-form" style="display:none;"></div>');
                $(result).prependTo($('#modal-form'));

                $('#modal-form').dialog({
                    width: $(window).width() * 0.8,
                    modal: true,
                    height: 'auto',
                    resizable: false,
                    closeText: "",
                    open: function() {
                        completeUIExt($('#modal-form').parent());
                        $('#modal-form').css({
                            overflow: 'inherit'
                        });
                        $('#modal-form').find('select.filter[multicolumn]').multiselect({
                            buttonClass: 'btn btn-block',
                            numberDisplayed: 0,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            filterPlaceholder: text('ms-search'),
                            resetText: text('ms-reset'),
                            nonSelectedText: '...',
                            includeResetOption: true,
                            includeResetDivider: true,
                            allSelectedText: false,
                            buttonContainer: '<div class="btn-group input-block-level multicolumn" />',
                            templates: {
                                filterClearBtn: '',
                                resetButton: '<li class="multiselect-reset"><div class="input-group"><a class="btn btn-xs btn-link"></a></div></li>'
                            },
                            onInitialized: function(select, container) {
                                configureMultiselect(select, container);
                            }
                        });
                        $('#modal-form').find('select.filter:not([multicolumn])').multiselect({
                            buttonClass: 'btn btn-block',
                            numberDisplayed: 0,
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: true,
                            filterPlaceholder: text('ms-search'),
                            resetText: text('ms-reset'),
                            nonSelectedText: '...',
                            includeResetOption: true,
                            includeResetDivider: true,
                            allSelectedText: false,
                            buttonContainer: '<div class="btn-group input-block-level" />',
                            templates: {
                                filterClearBtn: '',
                                resetButton: '<li class="multiselect-reset"><div class="input-group"><a class="btn btn-xs btn-link"></a></div></li>'
                            },
                            onInitialized: function(select, container) {
                                configureMultiselect(select, container);
                            }
                        });
                    },
                    create: function() {
                        workflowBuildDialog($(this),{
                            form_title: text('pg-settings')
                        });
                    },
                    buttons:[
                        {
                            tabindex: 1,
                            text: text('form-submit'),
                            id: 'SubmitBtn',
                            click: function () {
                                var parmValues = [];
                                $(this).find('select[name]').each(function() {
                                    if ( Array.isArray($(this).val()) ) {
                                        var value = $(this).val();
                                        if ( $(this).attr('name') == 'hiddencolumns' ) {
                                            value = [];
                                            $(this).find('option:not(:checked)').each(function() {
                                                value.push($(this).val());
                                            })
                                        }
                                        filterLocation.setup($(this).attr('name') + '=' + value.join('-'), 0);
                                    }
                                    else {
                                        var value = $(this).val();
                                        var sortName = 'group'+$(this).attr('name');
                                        if ( $('input[name='+sortName+']').length > 0 ) {
                                            value += '.' + $('input[name='+sortName+']:checked').val()
                                        }
                                        filterLocation.setup($(this).attr('name') + '=' + value, 0);
                                    }
                                    if ( $(this).attr('name') == 'show' ) {
                                        filterLocation.setup('hide=none', 0);
                                    }
                                });
                                filterLocation.setup('go=1', 1);
                                $(this).dialog('close');
                            }
                        },
                        {
                            tabindex: 2,
                            text: text('form-close'),
                            id: 'CancelBtn',
                            click: function() {
                                $(this).dialog('close');
                            }
                        }
                    ]
                });
            }
    });
}

function configureMultiselect(c, e)
{
    if ( !$(c).is('[multiple]') ) return;
    $('<a class="selall btn btn-xs btn-link">'+text('ms-selectall')+'</a>')
        .insertAfter($(e).find('.multiselect-reset a.btn:first'))
        .click(function() {
            c.multiselect('selectAll', false);
            c.multiselect('updateButtonText');
        });
}

function updateLocation( component, original )
{
    if ( component == '' ) return original;

    var parms = component.split('=');
    var location = original;

    var re = new RegExp('\\?'+escapeRegExp(parms[0])+'=[^\\&]*', 'gi');
    var match = re.exec( location );

    if ( parms[1] == '' && match != null )
    {
        location = location.replace(new RegExp(escapeRegExp(parms[0])+'=[^\\&]*\\&?', 'i'), '');
    }
    else
    {
        if ( match != null )
        {
            location = location.replace(re, '?'+component);
            return location;
        }

        var re = new RegExp('\\&'+escapeRegExp(parms[0])+'=[^\\&]*', 'gi');
        var match = re.exec( location );

        if ( match != null )
        {
            location = location.replace(re, '&'+component);
            return location;
        }

        location = location.replace(/[\?]/, '?'+component+'&');
        if ( location == original )
        {
            location += '?'+component;
        }
    }

    return location;
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}