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

if ( $message != '' ) {
    echo '<div class="alert alert-hint">' . $message . '</div>';
}
?>
<div>
    <table id="<?=$table_id?>" class="table-inner <?=$table_class_name?>" created="<?=$created_datetime?>" uid="<?=$widget_id?>">
        <thead>

		<tr class="header-row">
			<th class="for-num" width="<?=$numbers_column_width?>" uid="numbers">
                <div class="btn-group pull-left">
                    <div id="filter-settings" class="btn dropdown-toggle btn-sm btn-secondary" data-toggle="dropdown" href="#" data-target="#listmenu<?=$table_id?>">
                        <i class="icon-cog icon-white"></i>
                    </div>
                </div>
                <div class="btn-group dropdown-fixed" id="listmenu<?=$table_id?>">
                <?php
                    echo $view->render('core/PopupMenu.php', array(
                        'items' => $filter_actions
                    ));
                ?>
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
				if ( $header_attrs['script'] != '#' ) {
					echo '<a class="mode-sort" href="'.$header_attrs['script'].'">';
						echo $header_attrs['name'];
                        if ( $header_attrs['sort'] != '' ) {
                            echo $header_attrs['sort'] == 'up' ? '&#x25B2;' : '&#x25BC;';
                        }
					echo '</a>';
                    if ( $attr == 'Caption' ) {
                        echo ' <a class="dashed" id="collapseTree" onclick="">'.translate('свернуть').'</a> ';
                        echo ' <a class="dashed" id="restoreTree" onclick="">'.translate('развернуть').'</a> ';
                    }
				}
				else {
					echo $header_attrs['name'];
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
                        echo '<td id="'.strtolower($attr).'" uid="'.strtolower($attr).'"></td>';
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
            table: {
                indentation: 20,      // indent 20px per node level
                nodeColumnIdx: 3,     // render the node title into the 2nd column
            },
            source: {
                url: "<?=$jsonUrl?>"
            },
            lazyLoad: function(event, data) {
                data.result = {url: "ajax-sub2.json"}
            },
            renderColumns: function(event, data) {
                $(data.node.tr).find(">td").each(function() {
                    var attribute = $(this).attr('uid');
                    if ( attribute != 'caption' && data.node.data[attribute] ) {
                        $(this).html(data.node.data[attribute]);
                    }
                });

                $(data.node.tr).find(">td").eq(0)
                    .text(data.node.data['uid'] ? data.node.getIndexHier() : '')
                    .addClass("alignRight");

                $(data.node.tr)
                    .attr('object-id', data.node.key)
                    .attr('modified', data.node.data['modified']);
            },
            strings: {
                loading: "<?=text(1708)?>",
                loadError: "<?=text(677)?>",
                noData: "<?=text(2649)?>"
            }
        });
        /* Handle custom checkbox clicks */
        $("#<?=$table_id?>").on("click", "", function(e){
            var node = $.ui.fancytree.getNode(e);
            if ( node && node.tr ) {
                var id = $(node.tr).attr('object-id');
                if ( id != '' ) {
                    $(document).trigger("trackerItemSelected", [node.data['id'], e.ctrlKey || e.metaKey, node.data['class']]);
                }
            }
        });

        $('#collapseTree').click(function() {
            $("#<?=$table_id?>").fancytree("getTree").expandAll(false);
        });
        $('#restoreTree').click(function() {
            $("#<?=$table_id?>").fancytree("getTree").expandAll();
        });

        initializeTreeGrid('<?=$table_id?>', {});
    });
</script>
