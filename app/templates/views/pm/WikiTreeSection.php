<div class="treeview sticks-top" heightStyle="window">
	<div class="treeview-container sticks-top-body" style="position:relative;overflow:hidden;">
	</div>
</div>

<div style="clear:both;"></div>

<script type="text/javascript" src="/scripts/jquery/jquery-sortable.js"></script>
<script type="text/javascript" src="/scripts/pm/treeview.sortable.js"></script>
<script type="text/javascript">

    var maxWidth = $('#wikitree').parent().width() - 5,
        treeData = <?=$data?>;

	$(function() {
		$('div.treeview-container').html('<ul id="wikitree" class="filetree" style="width:100%;"></ul><div class="clear-fix">&nbsp;</div>');
		loadContentTree();
	});

	function loadContentTree( callback )
	{
		$('<ul id="wikitreecache" class="filetree" style="width:100%;"></ul>').insertAfter($('#wikitree'));
        var tree = $('#wikitreecache');
        tree.treeview({
    		root: '<?=$root_id?>',
    		async: true,
    		cache: false,
			collapsed: false,
			url: '<?=$url?>',
			treeData: treeData,
			asyncCallback: function() {
    			treeData = [];
                sortableTree.init(
                    tree.find('ul.treeview:first'), {
                        applicationUrl: '<?= $base_app_url ?>',
                        objectClass: '<?= $object_class ?>'
			        }
                );
				var style = $('#wikitree').attr('style');
				$('#wikitree').replaceWith($('#wikitreecache').attr('id', 'wikitree').attr('style',style));
				if ( typeof callback != 'undefined' ) callback();
				$('div.treeview-container').each(function() {
					$(this).perfectScrollbar({wheelPropagation:true});
					$(this).height(
						Math.min($('.wiki-page-tree').height(), $(window).height() - $(this).position().top)
					);
				});
            }
		});
		$('li[uid=open-new]>a').click(function() {
			$(this).attr('target', '_blank'); 
		});
	}
</script>
