<div class="treeview sticks-top" heightStyle="window">
</div>

<div style="clear:both;"></div>

<div id="treeview-hints">
	<?php
	if ( $document_hint != '' ) {
		echo $view->render('core/Hint.php', array('title' => text(1322), 'name' => $page_uid));
	}
	?>
</div>

<script type="text/javascript" src="/scripts/jquery/jquery-sortable.js"></script>
<script type="text/javascript" src="/scripts/pm/treeview.sortable.js"></script>
<script type="text/javascript">

    var maxWidth = $('#wikitree').parent().width() - 5,
        treeData = <?=$data?>;

	$(function() {
		$('div.treeview').html('<ul id="wikitree" class="filetree sticks-top-body" style="width:100%;"></ul>');
		loadContentTree();
	});

	function loadContentTree( callback )
	{
		$('<ul id="wikitreecache" class="filetree sticks-top-body" style="width:100%;"></ul>').insertAfter($('#wikitree'));
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
            }
		});
		$('li[uid=open-new]>a').click(function() {
			$(this).attr('target', '_blank'); 
		});
	}
</script>
