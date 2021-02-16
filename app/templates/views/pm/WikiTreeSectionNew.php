<?php
    $submitUrl = '';
    if ( is_array($actions['create']) ) {
        $submitUrl =  str_replace('item-id-template', $document_id, $actions['create']['url']);
        $submitTitle = $actions['create']['name'];
    }
?>

<div class="treeview" heightStyle="window">
	<div id="treeview-container" class="treeview-container">
		<div id="wikitree" data-type="json" style="display: none;">
			<?=\JsonWrapper::encode($data)?>
		</div>
        <div class="treeview-buttons">
            <a class="btn btn-xs" onclick="toggleTreeNodes()" title="<?=text(2455)?>"><i class="icon-folder-open"></i></a>
            <a class="btn btn-xs" onclick="toggleDocumentStructure()" title="<?=text(2204)?>"><i class="icon-chevron-left"></i></a>
            <?php if ( $submitUrl != '' ) { ?>
                <a class="btn btn-xs" onclick="<?=$submitUrl?>" title="<?=$submitTitle?>"><i class="icon-plus-sign"></i></a>
            <?php } ?>
        </div>
    </div>
</div>
<div id="context-menu-tree" class="btn-group dropdown-fixed">
	<?php echo $view->render('core/PopupMenu.php', array ( 'items' => $actions) ); ?>
</div>
<script type="text/javascript">
	var initContextMenu = function(tree, selector, menu, actions) {
		tree.$container.on("contextmenu", function(e) {
			var node = $.ui.fancytree.getNode(e);
			if(node) {
				$('#context-menu-tree.open').remove();
				$('.dropdown-fixed.open, .btn-group.open').removeClass('open');

				var item = $('#context-menu-tree').clone().appendTo('body');
				if ( item.length > 0 ) {
					item.find('a').each(function() {
						var href = $(this).attr('href').toString();
						href = href.replace(/item-id-template/gi, node.key);
						if ( node.data.project && node.data.project != devpromOpts.project ) {
							href = href.replace("/" + devpromOpts.project + "/", "/" + node.data.project + "/");
							href = href.replace("\\/" + devpromOpts.project + "\\/", "\\/" + node.data.project + "\\/");
						}
						$(this).attr('href', href);
					});
					item.addClass('open').removeClass('last')
						.css({
							left: e.pageX,
							top: e.pageY
						});
					if ( !item.find('ul>li:last').isInViewport() ) {
                        item.find('ul').css({
                           top: 'unset',
                           bottom: 0
                        });
                    }
					$('li[uid=open-new]>a').click(function() {
						$(this).attr('target', '_blank');
					});
					e.preventDefault();
					return false;
				}
			}
		});
	};
	$.ui.fancytree.registerExtension({
		name: "contextMenu",
		version: "1.0",
		contextMenu: {
			selector: "fancytree-title",
			menu: {},
			actions: {}
		},
		treeInit: function(ctx) {
			this._superApply(arguments);
			initContextMenu(ctx.tree,
				ctx.options.contextMenu.selector || "fancytree-title",
				ctx.options.contextMenu.menu,
				ctx.options.contextMenu.actions);
		}
	});
	var activeKey = '';
	var ps;
	var wikitree = null;

	$(function() {
        wikitree = $('#wikitree');
        wikitree.fancytree({
			extensions: ["dnd5","contextMenu"],
			quicksearch: true,
			debugLevel: 0,
			activate: function(event, data){
			    if ( data.node.extraClasses.indexOf('filtered') != -1 ) return;
			    setTimeout(function() {
                    gotoRandomPage(data.node.key, 4, true);
                    $(document).trigger("trackerItemSelected", [data.node.key]);
                }, 100);
			},
			create: function() {
                wikitree.show();
			},
			init: function() {
				if ( activeKey != "" ) {
					var nodeObj = $.ui.fancytree.getTree("#wikitree").getNodeByKey(activeKey);
                    if(nodeObj) {
                        nodeObj.setActive(true, {noEvents:true, noFocus:true});
                    }
				}
				if ( ps ) {
                    ps.update();
                }
			},
            createNode: function(event, data) {
                $(data.node.span)
                    .hover(
                        function(){
                            $(this).find('span.fancytree-title').css({
                                'position': 'fixed',
                                'top' : $(this).position().top + $('#wikitree ul').offset().top - $(document).scrollTop()
                            });
                        },
                        function() {
                            $(this).find('span.fancytree-title').css({
                                'position': '',
                                'top': '',
                            });
                        }
                    );
            },
            lazyLoad: function(event, data) {
                var node = data.node;
                data.result = {
                    url: "<?=$url?>",
                    data: { lazyroot: node.key },
                    cache: false
                };
            },
			dnd5: {
				autoExpandMS: 400,
				focusOnClick: false,
                preventRecursion: true,
                preventSameParent: false,
                preventVoidMoves: true,
                preventLazyParents: false,
				dragStart: function(node, data) {
                    data.effectAllowed = "all";
					return true;
				},
				dragEnter: function(node, data) {
					return true;
				},
                dragOver: function(node, data) {
                    data.dropEffect = data.dropEffectSuggested;
                },
				dragDrop: function(node, data) {
                    data.otherNode.moveTo(node, data.hitMode);

					var item = parentNode = prev = '';
					switch(data.hitMode) {
						case 'over':
							item = data.otherNode.key;
                            parentNode = node.key;
							prev = '';
							break;
						case 'after':
							item = data.otherNode.key;
							prev = node.key;
                            parentNode = node.parent ? node.parent.key : '';
							break;
						case 'before':
							item = data.otherNode.key;
                            parentNode = node.parent ? node.parent.key : '';
							prev = node.key;
					}

					var url = '/pm/'+devpromOpts.project+"/command.php?class=wikipagemove" +
						"&object_id=" + item + "&ParentPage=" + parentNode + "&"+data.hitMode+"=" + prev + "&action=2";

					$.ajax({
                        url: url,
                        dataType: 'json',
                        type: 'POST',
                        success: function (data, status) {
                            if (data.state && data.state == 'error') {
                                reportError(data.message);
                                return;
                            }
                            $.each(data, function (index, value) {
                                var row = $('tr[object-id=' + value.id + '][sort-value]');
                                row.attr('sort-value', value.si);
                                row.find('.sec-num').html(value.sn);
                            });
                            setTimeout(function () {
                                gotoRandomPage(item, 0, false);
                            }, 100);
                        },
                        error: function (xhr, status, error) {
                            reportError(ajaxErrorExplain(xhr, error));
                        }
                    });
				}
			},
            strings: {
                loading: "<?=text(1708)?>",
                loadError: "<?=text(677)?>",
                noData: "<?=text(2649)?>"
            }
		});

        $(document).scroll(function() {
            $('span.fancytree-node:hover span.fancytree-title').css({
                'top': '',
                'position': ''
            });
        });

        $('.wiki-page-tree').resizable({
            handles: 'e',
            alsoResize: '#treeview-container',
            minWidth: 130
        });
	});
	function loadContentTree( callback )
	{
	    if ( !wikitree ) return;
        $.ui.fancytree.getTree("#wikitree")
			.reload({
				url: "<?=$url?>"
			})
			.done(function() {
				if ( typeof callback != 'undefined' ) callback();
			});
	}
	function activateTreeNode( id ) {
		activeKey = id;
        if ( !wikitree ) return;
		var tree = $.ui.fancytree.getTree("#wikitree");

		if ( typeof tree.getActiveNode == "undefined" ) return;
		var node = tree.getActiveNode();
		if ( node && node.key == id ) return;

		if ( typeof tree.getNodeByKey == "undefined" ) return;
		var node = tree.getNodeByKey(id);
		if ( !node ) return;
		node.setActive(true, {
			noEvents: true, noFocus: true
		});
	}
	function renameTreeNode( id, title ) {
        if ( !wikitree ) return;
		var tree = $.ui.fancytree.getTree("#wikitree");
		if ( typeof tree.getNodeByKey == "undefined" ) return;

		var node = tree.getNodeByKey(id);
		if ( !node ) return;
		node.setTitle(title);
	}
	function deleteTreeNode( id ) {
        if ( !wikitree ) return;
		var tree = $.ui.fancytree.getTree("#wikitree");
		if ( typeof tree.getNodeByKey == "undefined" ) return;

		var node = tree.getNodeByKey(id);
		if ( !node ) return;
		node.remove();
	}
	function toggleTreeNodes() {
        if ( !wikitree ) return;
	    var tree = $.ui.fancytree.getTree("#wikitree");
        tree.visit(function(node){
            if ( node.parent.key == 'root_1' ) return;
            node.setExpanded( !wikitree.hasClass('custom-extended') );
        });
        setTimeout(function(){$('#rightTab li:eq(0)').addClass('active');},100);
        wikitree.toggleClass('custom-extended');
    }
</script>