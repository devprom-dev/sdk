<div class="treeview">
</div>

<div style="clear:both;"></div>

<div id="treeview-hints">
	<?php if ( $hint_display ) { ?>
		<? echo $view->render('core/HintLight.php', array('title' => text(1322), 'name' => $hint_name)); ?>
	<?php } ?>
</div>

<script type="text/javascript" src="/scripts/jquery/jquery-sortable.js"></script>
<script type="text/javascript" src="/scripts/pm/treeview.sortable.js"></script>
<script type="text/javascript">

    var maxWidth = $('#wikitree').parent().width() - 5,
        treeData = <?=$data?>;

	$(function() {
		loadContentTree();
	});

	function loadContentTree()
	{
		$('div.treeview').html('<div id="treeview-placeholder" class="placeholder">&nbsp;</div><ul id="wikitree" class="filetree" style="width:100%;"></ul>');
		
        var tree = $('#wikitree');

        tree.treeview({
    		root: '<?=$root_id?>',
    		async: true,
    		cache: false,
			collapsed: false,
			url: '<?=$url?>',
			treeData: treeData,
			asyncCallback: function() {
    			$('#treeview-placeholder').remove();
    			treeData = [];

                sortableTree.init(
                    tree.find('ul.treeview:first'),
                    {
                        applicationUrl: '<?= $base_app_url ?>',
                        objectClass: '<?= $object_class ?>'
			        }
                );
            }
		});



		$('li[uid=open-new]>a').click(function() {
			$(this).attr('target', '_blank'); 
		});
	}
</script>
