<?php 
$columns_info = array();

foreach( $columns as $key => $attr )
{
	if ( !$list->getColumnVisibility($attr) ) {
		unset($columns[$key]); continue;
	}
	$columns_info[$attr] = array (
        'id' => strtolower($table_col_id.$attr),
        'width' => $list->getColumnWidth( $attr ),
        'align' => $list->getColumnAlignment( $attr ),
        'reference' => $object->IsReference( $attr )
	);
}
$columns_number = count($columns_info);

$title_column_index = 0;
foreach( array_values($columns) as $key => $attr ) {
    if ( $attr == 'Caption' ) {
        $title_column_index = $key;
        break;
    }
}
$title_column_index++; // numbers at first column
$title_column_index++; // selection per row

if ( $message != '' ) {
    echo '<div class="alert alert-hint">' . $message . '</div>';
}
?>
<div class="list-container">
    <table id="<?=$table_id?>" class="table-inner <?=$table_class_name?>" created="<?=$created_datetime?>" uid="<?=$widget_id?>">
        <thead>

		<tr class="header-row">
			<th class="for-num" width="<?=$numbers_column_width?>" uid="numbers">
                <div class="btn-group pull-left">
                    <button id="filter-settings" class="btn dropdown-toggle btn-xs btn-secondary">
                        <i class="icon-cog icon-white"></i>
                    </button>
                </div>
            </th>
			<th class="for-chk visible" width="1%" uid="checkbox">
                <input id="to_delete_all<?=$table_id?>" tabindex="-1" type="checkbox" onclick="checkRows('<?=$table_id?>')" items-hash="<?=$itemsHash?>" >
			</th>

			<?php
			foreach( $columns as $attr )
			{
				$width = $columns_info[$attr]['width'];
				$title = str_replace('"', "'", $it->object->getAttributeDescription($attr));

				$header_attrs = $list->getHeaderAttributes( $attr );
				echo '<th width="'.$width.'" uid="'.strtolower($attr).'" title="'.$title.'" class="'.$header_attrs['class'].'">';
				if ( $header_attrs['script'] != '#' && in_array($attr, $sorts) ) {
					echo '<a class="mode-sort" href="'.$header_attrs['script'].'">';
						echo $header_attrs['name'];
                        if ( $header_attrs['sort'] != '' ) {
                            echo $header_attrs['sort'] == 'up' ? '&#x25B2;' : '&#x25BC;';
                        }
					echo '</a>';
				}
				else {
					echo $header_attrs['name'];
				}
                if ( $attr == 'Caption' ) {
                    echo ' <a class="dashed" id="collapseTree" onclick="">'.translate('свернуть').'</a> ';
                    echo ' <a class="dashed" id="restoreTree" onclick="">'.translate('развернуть').'</a> ';
                }
				echo '</th>';
			}
			$columns_number++;
			$width = $list->getColumnWidth( 'Actions' );
			?>
			<th class="for-operation hidden-print" width="1%">
			</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td uid="checkbox-field"></td>
                <?php
                    foreach( $columns as $attr ) {
                        echo '<td id="'.strtolower($attr).'" uid="'.strtolower($attr).'" class="cell-'.$list->getColumnAlignment($attr).'"></td>';
                    }
                ?>
                <td id="operations" uid="actions" class="hidden-print"></td>
            </tr>
        </tbody>
	</table>
	<?php  echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid, 'open' => $hint_open)); ?>
</div>
	
<script language="javascript">
    $(function(){
        // Attach the fancytree widget to an existing <div id="tree"> element
        // and pass the tree options as an argument to the fancytree() function:
        $("#<?=$table_id?>").fancytree({
            extensions: ["table","persist"],
            checkbox: false,
            icon: false,
            debugLevel: 0,
            dataUrl: "<?=$jsonUrl?>",
            table: {
                indentation: 20,      // indent 20px per node level
                nodeColumnIdx: <?=$title_column_index?>,     // render the node title into the 2nd column
            },
            persist: {
                expandLazy: true,
                store: "local",
                types: "active expanded focus selected"  // which status types to store
            },
            source: {
                url: "<?=$jsonUrl?>&roots=0"
            },
            lazyLoad: function(event, data) {
                var node = data.node;
                data.result = {
                    url: "<?=$jsonUrl?>",
                    data: {
                        mode: "children",
                        roots: node.data.id,
                        rootclass: node.data.class,
                        search: ''
                    },
                    cache: false
                };
            },
            renderColumns: function(event, data) {
                $(data.node.tr).find(">td").each(function() {
                    var attribute = $(this).attr('uid');
                    if ( attribute != 'caption' && data.node.data[attribute] ) {
                        $(this).html(data.node.data[attribute]);
                    }
                    if ( attribute == 'checkbox-field' ) {
                        if ( $(this).parent('tr').prev().find('td>input').is(':checked') ) {
                            $(this).find('input').attr('checked', 'checked');
                        }
                    }
                });

                $(data.node.tr).find(">td").eq(0)
                    .text(data.node.data['section'] ? data.node.data['section'] : data.node.getIndexHier())
                    .addClass("alignRight");

                $(data.node.tr)
                    .attr('object-id', data.node.key)
                    .attr('raw-id', data.node.data.id)
                    .attr('state', data.node.data['object-state'])
                    .attr('project', data.node.data['project'])
                    .attr('modified', data.node.data['modified']);
            },
            strings: {
                loading: "<?=text(1708)?>",
                loadError: "<?=text(677)?>",
                noData: "<?=text(2649)?>"
            },
            init: function(event, data) {
                if ( !data.status ) return;
                if ( data.tree.rootNode.children.length < 1 ) return;
                var item = data.tree.rootNode.children[0];
                $(document).trigger('trackerItemSelected', [item.data.id, false, item.data.class]);
            }
        });
        /* Handle custom checkbox clicks */
        $("#<?=$table_id?>").on("click", "", function(e){
            var node = $.ui.fancytree.getNode(e);
            if ( node && node.tr && $(e.target).is('input,td,.fancytree-title') ) {
                var id = $(node.tr).attr('object-id');
                if ( id != '' ) {
                    $(document).trigger("trackerItemSelected",
                        [node.data['id'], e.ctrlKey || e.metaKey || $(e.target).is('input'),
                            node.data['class']]);
                }
            }
        });
        $("#<?=$table_id?>").on("dblclick", "", function(e){
            var node = $.ui.fancytree.getNode(e);
            if ( node && node.tr ) {
                var ref = $(node.tr).find('td[id=operations] li>a:eq(0)');
                if (ref.is('[onclick]')) {
                    ref.click();
                } else if (ref.is('[href]')) {
                    window.location = ref.attr('href');
                }
            }
        });

        $('#collapseTree').click(function() {
            $.ui.fancytree.getTree("#<?=$table_id?>").expandAll(false);
        });
        $('#restoreTree').click(function() {
            $.ui.fancytree.getTree("#<?=$table_id?>").expandAll();
        });

        initializeTreeGrid('<?=$table_id?>', {});
    });
</script>
