var shortcutContexts = [
    {
        title: text('sc-context-global'),
        keys: [
            {
                title: text('sc-key-help'),
                path: ['?'],
                handler: 'a#shortcuts-help'
            }, {
                title: text('sc-key-gt-search'),
                handler: function() {
                    setTimeout(function() {
                        $('input#quick-search').focus();
                    }, 80);
                },
                path: ['f','/','а','.']
            }, {
                title: text('sc-key-view-menu'),
                path: ['<',',','б'],
                handler: function(e) {
                    setTimeout(function() {
                        switchMenuState();
                    }, 100);
                }
            }, {
                title: text('sc-key-new-issue'),
                handler: '.quick-btn a#issue',
                path: ['a i','c','ф ш','с']
            }, {
                title: text('sc-key-new-bug'),
                handler: '.quick-btn a#bug',
                path: ['a b','ф и']
            }, {
                title: text('sc-key-new-task'),
                handler: '.quick-btn a#task',
                path: ['a t','ф е']
            }, {
                title: text('sc-key-new-epic'),
                handler: '.quick-btn a#quick-feature',
                path: ['a f','ф а']
            }, {
                title: text('sc-key-new-quest'),
                handler: '.quick-btn a#question',
                path: ['a q','ф й']
            }, {
                title: text('sc-key-gt-set'),
                handler: 'a[uid=settings-4-project]',
                path: ['g s','п ы']
            }, {
                title: text('sc-key-gt-back'),
                handler: function(e) {
                    shortcutObject.gotoModule('a[module=vms-issues-backlog]');
                },
                path: ['g b','п и']
            }, {
                title: text('sc-key-gt-ib'),
                handler: function() {
                    shortcutObject.gotoModule('a[module="vms-kanban/requests"],a[module=vms-issues-board]');
                },
                path: ['g i','п ш']
            }, {
                title: text('sc-key-gt-tb'),
                handler: function(e) {
                    shortcutObject.gotoModule('a[module=vms-tasks-board]');
                },
                path: ['g t','п е']
            }, {
                title: text('sc-key-gt-reqs'),
                handler: function(e) {
                    shortcutObject.gotoModule('a[module="vms-requirements/list"]');
                },
                path: ['g r','п к']
            }, {
                title: text('sc-key-gt-plan'),
                handler: function(e) {
                    shortcutObject.gotoModule('a[module=vms-project-plan-hierarchy]');
                },
                path: ['g l','п д']
            }, {
                title: text('sc-key-gt-kb'),
                handler: function(e) {
                    shortcutObject.gotoModule('a[module=vms-project-knowledgebase]');
                },
                path: ['g k','п л']
            }, {
                title: text('sc-key-gt-wn'),
                handler: function(e) {
                    window.location = '/pm/'+devpromOpts.project+'/whatsnew';
                },
                path: ['g n','п т']
            }, {
                title: text('sc-key-gt-prgm'),
                handler: 'a#navbar-portfolio',
                path: ['g p','п з']
            }
        ]
    }, {
        title: text('sc-context-view'),
        keys: [
            {
                title: text('sc-key-view-rightpane'),
                path: [']','ъ'],
                handler: function(e) {
                    toggleMasterDetails(true);
                }
            }, {
                title: text('sc-key-view-new'),
                path: ['+'],
                handler: '.filter-actions a.append-btn:eq(0), .filter-actions li>a[id*=append]:eq(0), a.new-at-form'
            }, {
                title: text('sc-key-view-all'),
                path: ['ctrl+a', 's a', 'ы а'],
                handler: function(e) {
                    if ( $('.board-table').length > 0 ) {
                        checkRowsTrue($('.board-table').attr('id'));
                        e.preventDefault();
                    }
                    if ( $('.wishes-table[uid]').length > 0 ) {
                        var checkBox = $('input[id*=to_delete_all]');
                        checkBox.attr('checked',!checkBox.attr('checked'));
                        checkRows($('.wishes-table[uid]').attr('id'));
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-view-edit'),
                path: ['e', 'у'],
                handler: 'a#modify'
            }, {
                title: text('sc-key-view-righttab'),
                path: ['alt+right'],
                handler: function(e) {
                    var selected = $('.ui-tabs-active');
                    if ( selected.length > 0 ) {
                        var index = $('.ui-tabs-tab').index(selected.next());
                        if ( index >= 0 ) {
                            shortcutObject.openTab(index);
                        }
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-view-lefttab'),
                path: ['alt+left'],
                handler: function(e) {
                    var selected = $('.ui-tabs-active');
                    if ( selected.length > 0 ) {
                        var index = $('.ui-tabs-tab').index(selected.prev());
                        if ( index >= 0 ) {
                            shortcutObject.openTab(index);
                        }
                        e.preventDefault();
                    }
                }
            },
            {
                title: text('sc-key-gt-submit'),
                handler: function(e) {
                    var el = $('button[id*=SubmitBtn],input[type=submit],span.cke_dialog_ui_button:eq(0)');
                    if ( el.length > 0 ) {
                        el.click();
                        e.preventDefault();
                    }
                },
                path: ['ctrl+enter', 'command+enter']
            }, {
                title: text('sc-key-gt-submitopen'),
                handler: function(e) {
                    var el = $('button[id*=SubmitOpenBtn]');
                    if ( el.length > 0 ) {
                        el.click();
                        e.preventDefault();
                    }
                },
                path: ['ctrl+alt+enter','command+option+enter']
            },
            {
                title: text('sc-key-view-state'),
                path: ['n', 'т'],
                handler: 'a.btn[id*=workflow-]:eq(0)'
            },
            {
                title: text('sc-key-view-next'),
                path: ['right'],
                handler: 'li.next-item a'
            },
            {
                title: text('sc-key-view-comment'),
                path: ['m', 'ь'],
                handler: function(e) {
                    clickAddCommentOnForm();
                    e.preventDefault();
                }
            },
            {
                title: text('sc-key-view-time'),
                path: ['t', 'е'],
                handler: 'a#spend-time'
            }
        ]
    }, {
        title: text('sc-context-editor'),
        keys: [
            {
                title: text('sc-key-edt-leftpane'),
                path: ['[', 'х'],
                handler: function(e) {
                    toggleDocumentStructure();
                }
            },
            {
                title: text('sc-key-help'),
                path: ['ctrl+shift+/'],
                handler: 'a#shortcuts-help'
            }, {
                title: text('sc-key-edt-mod'),
                path: ['ctrl+alt+e','ctrl+alt+у'],
                handler: function(e) {
                    clickDocumentRowElement('a#modify');
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-workflow'),
                path: ['ctrl+alt+n','ctrl+alt+т'],
                handler: function(e) {
                    clickDocumentRowElement('a[id*=workflow-]:eq(0)');
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-sblng'),
                path: ['ctrl+alt++','ctrl+alt+s','ctrl+alt+ы'],
                handler: function(e) {
                    clickDocumentRowElement('a#new-sibling:eq(0)');
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-chld'),
                path: ['ctrl+alt+c','ctrl+alt+с'],
                handler: function(e) {
                    clickDocumentRowElement('a#new-child');
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-up'),
                path: ['alt+up'],
                handler: function(e) {
                    if ( localOptions.visiblePages == 1 ) return false;
                    var selectedId = getPageSelected();
                    if ( selectedId < 1 ) return false;
                    var el = $('#tablePlaceholder .table-inner tr[object-id='+selectedId+']').prev('tr[object-id]');
                    if ( el.length < 1 ) return false;
                    if ( el.is('.row-empty') ) {
                        buildTopWaypoint(localOptions);
                    }
                    else {
                        setRowFocus(el.attr('object-id'));
                    }
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-down'),
                path: ['alt+down'],
                handler: function(e) {
                    if ( localOptions.visiblePages == 1 ) return false;
                    var selectedId = getPageSelected();
                    if ( selectedId < 1 ) return false;
                    var el = $('#tablePlaceholder .table-inner tr[object-id='+selectedId+']').next('tr[object-id]');
                    if ( el.length < 1 ) return false;
                    if ( el.is('.row-empty') ) {
                        buildBottomWaypoint(localOptions);
                    }
                    else {
                        setRowFocus(el.attr('object-id'));
                    }
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-link'),
                path: ['ctrl+alt+l','ctrl+alt+д'],
                handler: function(e) {
                    if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) {
                        CKEDITOR.currentInstance.execCommand('searchArtifact');
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-edt-issue'),
                path: ['ctrl+alt+i','ctrl+alt+ш'],
                handler: function(e) {
                    if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) {
                        CKEDITOR.currentInstance.execCommand('productivityCreateIssue');
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-edt-task'),
                path: ['ctrl+alt+t','ctrl+alt+е'],
                handler: function(e) {
                    if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) {
                        CKEDITOR.currentInstance.execCommand('productivityCreateTask');
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-edt-comment'),
                path: ['ctrl+alt+m','ctrl+alt+ь'],
                handler: function(e) {
                    if ( typeof CKEDITOR != 'undefined' && CKEDITOR.currentInstance != null ) {
                        CKEDITOR.currentInstance.execCommand('productivityComment');
                        e.preventDefault();
                    }
                }
            }, {
                title: text('sc-key-edt-trace'),
                path: ['ctrl+alt+r','ctrl+alt+к'],
                handler: function(e) {
                    clickDocumentRowElement('a#doc-page-trace');
                    e.preventDefault();
                }
            }, {
                title: text('sc-key-edt-fls'),
                path: ['ctrl+alt+f','ctrl+alt+а'],
                handler: function(e) {
                    clickDocumentRowElement('a#new-tag-file');
                    e.preventDefault();
                }
            }
        ]
    }
];
shortcutObject = {
    gotoModule: function(uid) {
        var el = $($('#quick-search').attr('data-content')).find(uid);
        if ( el.length > 0 && typeof el.attr('href') == 'string' ) window.location = el.attr('href');
    },
    openTab: function(index) {
        $('.ui-dialog .tabs').tabs( "option", "active", index );
        setTimeout(function() {
            $('.ui-tabs-panel:not(.ui-tabs-hide) [tabindex]:visible').filter(function() {
                return $(this).attr("tabindex") > 0;
            }).first().blur().focus();
        }, 250);
    }
};
$.each(shortcutContexts, function(index, context) {
    $.each(context.keys, function(index, key) {
        if ( typeof key.handler == 'string' ) {
            var jqueryPath = key.handler;
            Mousetrap.bind(key.path, function() {
                var itemToClick = $(jqueryPath);
                if ( itemToClick.length < 1 ) return false;
                if ( itemToClick.is('[href]') && itemToClick.attr('href') != '#' ) {
                    window.location = itemToClick.attr('href');
                }
                else {
                    itemToClick.trigger("click");
                }
            });
        }
        else {
            Mousetrap.bind(key.path, key.handler);
        }
    })
});
Mousetrap.prototype.wasStopCallback = Mousetrap.prototype.stopCallback;
Mousetrap.prototype.stopCallback = function(e, element, combo) {
    if ( combo.indexOf('ctrl') >= 0 && combo.indexOf('+a') < 0 ) return;
    if ( combo.indexOf('alt') >= 0 ) return;
    if ( combo.indexOf('command') >= 0 ) return;
    if ( combo.indexOf('option') >= 0 ) return;
    return this.wasStopCallback(e,element,combo);
};