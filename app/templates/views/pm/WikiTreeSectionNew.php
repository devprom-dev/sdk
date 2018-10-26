<div class="treeview sticks-top" heightStyle="window">
	<div id="treeview-container" class="treeview-container sticks-top-body" style="position:relative;overflow:hidden;">
		<div id="wikitree" data-type="json" style="display:none;">
			<?=$data?>
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
						href = href.replace(/\%id\%/gi, node.key);
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

	$(function() {
		var tree = $('#wikitree');
        tree.fancytree({
			extensions: ["dnd","contextMenu"],
			quicksearch: true,
			debugLevel: 0,
			activate: function(event, data){
			    setTimeout(function() {
                    gotoRandomPage(data.node.key, 4, true);
                }, 100);
			},
			create: function() {
				$('#wikitree').show();
                ps = new PerfectScrollbar('#treeview-container', {wheelPropagation:true});
                document.querySelector('#treeview-container').addEventListener('ps-scroll-y', function() {
                    $('span.fancytree-node:hover span.fancytree-title').css({
                        'top': '',
                        'position': ''
                    });
                });
			},
			init: function() {
				if ( activeKey != "" ) {
					var nodeObj = $("#wikitree").fancytree("getTree").getNodeByKey(activeKey);
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
			dnd: {
				autoExpandMS: 400,
				focusOnClick: true,
				preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
				preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
				dragStart: function(node, data) {
					/** This function MUST be defined to enable dragging for the tree.
					 *  Return false to cancel dragging of node.
					 */
                    $('span.fancytree-title').css({
                        'top': '',
                        'position': ''
                    });
					return true;
				},
				dragEnter: function(node, data) {
					/** data.otherNode may be null for non-fancytree droppables.
					 *  Return false to disallow dropping on node. In this case
					 *  dragOver and dragLeave are not called.
					 *  Return 'over', 'before, or 'after' to force a hitMode.
					 *  Return ['before', 'after'] to restrict available hitModes.
					 *  Any other return value will calc the hitMode from the cursor position.
					 */
					// Prevent dropping a parent below another parent (only sort
					// nodes under the same parent)
					/*           if(node.parent !== data.otherNode.parent){
					 return false;
					 }
					 // Don't allow dropping *over* a node (would create a child)
					 return ["before", "after"];
					 */
					return true;
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

					$.ajax({ url: url, dataType: 'json', type: 'POST'})
						.success(function (data) {
							$.each(data, function(index, value) {
								var row = $('tr[object-id='+value.id+'][sort-value]');
								row.attr('sort-value', value.si);
								row.find('.sec-num').html(value.sn);
							});
							setTimeout(function() {
                                gotoRandomPage(item, 0, false);
                            }, 100);
						})
						.error(function() {
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
	});
	function loadContentTree( callback )
	{
		$("#wikitree").fancytree("getTree")
			.reload({
				url: "<?=$url?>"
			})
			.done(function() {
				if ( typeof callback != 'undefined' ) callback();
			});
	}
	function activateTreeNode( id ) {
		activeKey = id;
		var tree = $("#wikitree").fancytree("getTree");

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
		var tree = $("#wikitree").fancytree("getTree");
		if ( typeof tree.getNodeByKey == "undefined" ) return;

		var node = tree.getNodeByKey(id);
		if ( !node ) return;
		node.setTitle(title);
	}
	function deleteTreeNode( id ) {
		var tree = $("#wikitree").fancytree("getTree");
		if ( typeof tree.getNodeByKey == "undefined" ) return;

		var node = tree.getNodeByKey(id);
		if ( !node ) return;
		node.remove();
	}
	function toggleTreeNodes( id ) {
	    var tree = $("#wikitree");
        tree.fancytree("getTree").visit(function(node){
            if ( node.parent.key == 'root_1' ) return;
            node.setExpanded( !tree.hasClass('custom-extended') );
        });
        setTimeout(function(){$('#rightTab li:eq(0)').addClass('active');},100);
        tree.hasClass('custom-extended') ? tree.removeClass('custom-extended') : tree.addClass('custom-extended');
    }
    function extendTreeArea( id ) {
        var tree = $(".wiki-page-tree");
        if ( tree.hasClass('custom-width') ) {
            tree.css({
                'width': ''
            });
            tree.removeClass('custom-width');
        }
        else {
            tree.css({
                'width': '50%'
            });
            $('#documentToolbar').width('auto');
            tree.addClass('custom-width');
        }
        setTimeout(function(){$('#rightTab li:eq(0)').addClass('active');},100);
    }
</script>