<?php 

$table_row_id = $table_id.'_row_';
$uid = new ObjectUID();
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
$display_operations = $list->IsNeedToDisplayOperations();
if ( $show_header && $display_numbers ) $columns_number++;
if ( $show_header && $need_to_select ) $columns_number++;

?>
<? if ( $message != '' ) { ?>
	<div class="alert alert-hint">
		<?=$message?>
	</div>
<? } ?>

<a name="top<? echo $offset_name ?>"></a>

<? if ( $toolbar ) { ?>
			<div class="hidden-print documentToolbar" style="overflow:hidden;">
				<div class="hidden-print" id="documentToolbar" style="z-index:2;"></div>
			</div>
<? } ?>


        <div class="list-container <?=($list_mode == 'infinite' ? 'table-inner-div' : 'wishes')?>">
        <table id="<?=$table_id?>" class="table-inner <?=$table_class_name?>" created="<?=$created_datetime?>" uid="<?=$widget_id?>">


			<?php if ( $show_header ) { ?>
                <thead>
		<tr class="header-row">
			<?php if ( $display_numbers ) { ?>
			<th class="for-num" width="<?=$numbers_column_width?>" uid="numbers">
                <? if ( !$tableonly ) { ?>
                    <div class="btn-group pull-left">
                        <button id="filter-settings" class="btn dropdown-toggle btn-xs btn-secondary">
                            <i class="icon-cog icon-white"></i>
                        </button>
                    </div>
                <?php } ?>
            </th>
			<?php } ?>

			<th class="for-chk <?=($need_to_select ? 'visible' : 'hidden')?>" width="1%" uid="checkbox">
				<?php if ( $need_to_select ) { ?>
					<input id="to_delete_all<?=$table_id?>" tabindex="-1" type="checkbox" onclick="checkRows('<?=$table_id?>')" items-hash="<?=$itemsHash?>">
				<?php } ?>
			</th>

			<?php
			$numericFields = array();
			foreach( $columns as $attr ) 
			{
				$width = $columns_info[$attr]['width'];
				$title = str_replace('"', "'", $it->object->getAttributeDescription($attr));

				if ( in_array($it->object->getAttributeType($attr), array('integer', 'float')) && !in_array($attr, array('UID','OrderNum')) ) {
					$numericFields[] = $attr;
				}

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
                if ( in_array($attr, $collapseAttributes) && $group_field != '' ) {
                    echo ' <a class="dashed" onclick="collapseGroups()" style="padding-left: 8px;"> '.translate('свернуть').'</a> ';
                    echo ' <a class="dashed" onclick="restoreGroups()" style="padding-left: 8px;"> '.translate('развернуть').'</a> ';
                }
                echo '</th>';
			}
			$numericFields = array_diff(
				$numericFields,
				$object->getAttributesByGroup('skip-total')
			);

			if ( $list->IsNeedToDisplayOperations() )
			{
				$columns_number++;
			$width = $list->getColumnWidth( 'Actions' );
			?>
			<th class="for-operation hidden-print" width="1%">
			</th>
			<?php } ?>
			
        </tr>
                </thead>
        <?php } ?>

			<?

			if ( $rows_num < 1 && $no_items_message != '' )
			{
			?>
			<tr id="no-elements-row">
				<td colspan="<?=$columns_number?>"><?=$no_items_message?></td>
			</tr>
			<?php 
			}
			
			// get an array of group fields
			if ( !in_array($group_field, array_keys($it->object->getAttributes())) ) $group_field = '';

			$group_field_prev_value = '{83C23330-E68F-4852-83D7-6BE4E49FF985}';
			$rowNumber = intval($offset);

			echo '<tbody>';
			for( $i = 0; $i < $rows_num; $i++) {
				if ( $group_field != '' ) {
					if ( in_array($it->object->getAttributeType($group_field), array('date','datetime')) ) {
						$group_field_value = array_shift(preg_split('/\s+/',$it->get($group_field)));
					}
					else {
						$group_field_value = $it->get($group_field);
					}
                    $group_field_value = str_replace(',', '', $group_field_value);

					if( $group_field_value != $group_field_prev_value )
					{
					    if ( $i > 0 ) echo '</tbody>';
                        $guid = md5($group_field_value . $it->get('VPD'));
					?>
                    <tr id="<?=($table_row_id.'g_'.$group_field_value)?>" class="info" group-id="<?=$group_field_value?>" >
                            <?php $list->drawGroupRow($group_field, $group_field_value, $it, $columns_number, $guid); ?>
                    </tr>
                    </tbody>
                    <tbody id="gor<?=$guid?>" class="in collapse">
					<? 
					}
					$group_field_prev_value = $group_field_value;
				}

				if ( !$list->IsNeedToDisplayRow($it) )
				{
					$it->moveNext();
					continue;
				}

                $rowNumber++;
				?>
				<tr id="<?=($table_row_id.($offset + $i + 1))?>" class="<?=$list->getRowClassName($it)?>" object-class="<?=$list->getItemClass($it)?>" object-id="<?=$it->getId()?>" state="<?=$it->get('State')?>" project="<?=$it->get('ProjectCodeName')?>" group-id="<?=$group_field_value?>" order="<?=$it->get('OrderNum')?>" modified="<?=$it->get('AffectedDate')?>" modifier="<?=$it->get('Modifier')?>" sort-value="<?=htmlspecialchars($it->get_native($sort_field))?>" sort-type="<?=$sort_type?>">
				
					<? if ( $display_numbers ) { ?>
					<td name="row-num">
						<? $list->drawNumberColumn( $it, $rowNumber ); ?>
					</td>
					<? } ?>

					<td class="<?=($need_to_select ? 'visible' : 'hidden')?>" uid="checkbox">
						<? if ( $need_to_select && $list->IsNeedToSelectRow( $it ) ) { ?>
							<input class=checkbox tabindex="-1" type="checkbox" name="to_delete_<? echo $it->getId(); ?>">
						<? } ?>
					</td>

					<?php
					foreach( $columns as $attr )
					{
						$comment = $list->getCellComment( $it, $attr );

						$cell_id = $columns_info[$attr]['id'];
						$align = $columns_info[$attr]['align'];
						$width = $columns_info[$attr]['width'];
					
						$color = $list->getRowColor( $it, $attr );
						
                        echo '<td id="'.$cell_id.'" '.($width != '' ? 'width="'.$width.'"' : '').' title="'.$comment.'" style="text-align:'.$align.';color:'.$color.'">';

                        $script = '';
                        if ( in_array($attr, $bulkAttributes) ) {
                            $script = "javascript:processBulk(
                                    '{$it->object->getAttributeUserName($attr)}',
                                    '{$it->getEditUrl()}&formonly=true&operation=Attribute{$attr}',
                                    '{$it->getId()}', 
                                    devpromOpts.updateUI);";
                        }

                        if( $columns_info[$attr]['reference'] ) {
                            $refIt = $list->getFilteredReferenceIt($attr, $it->get($attr));
                            if ( $script != '' ) {
                                if ( !$uid->hasUid($refIt) ) {
                                    echo '<a class="btn btn-xs btn-light" href="'.$script.'">';
                                        if ( $it->get($attr) == '' ) {
                                            echo '...';
                                        }
                                        $list->drawRefCell( $refIt, $it, $attr);
                                    echo '</a>';
                                }
                                else {
                                    $list->drawRefCell( $refIt, $it, $attr);
                                    echo ' <a class="btn btn-xs btn-light" href="'.$script.'">...</a>';
                                }
                            }
                            else {
                                $list->drawRefCell( $refIt, $it, $attr);
                            }
                        }
                        else {
                            if ( $script != '' ) {
                                echo '<a class="btn btn-xs btn-light" href="'.$script.'">';
                                    if ( $it->get($attr) == '' ) {
                                        echo '...';
                                    }
                                    $list->drawCell( $it, $attr );
                                echo '</a>';
                            }
                            else {
                                $list->drawCell( $it, $attr );
                            }
                        }

                        if ( in_array($attr, $bulkAttributes) ) {
                            echo '</a>';
                        }
                        echo '</td>';
					}

					if ( $display_operations ) 
                    {
						$actions = $list->getActions($it->getCurrentIt());
                    	?>
						<td id="operations" class="hidden-print">
						<?php
						if ( count($actions) == 1 )
						{
							$action = array_shift($actions);
							if ( $action['url'] != '' || $action['click'] != '' ) {
                                ?>
                                <div class="btn-group visible-operations last">
                                    <a id="<?=$action['uid']?>" class="btn btn-info btn-xs dropdown-toggle actions-button" onclick="<?=(!in_array($action['url'],array('','#')) ? $action['url'] : $action['click'])?>"><?=$action['name']?></a>
                                </div>
                                <?
                            }
						} else if ( count($actions) > 0 )
						{
						?>
						<div class="btn-group btn-group-actions operation last">
							<a class="btn btn-xs dropdown-toggle actions-button btn-secondary" data-toggle="dropdown" data-target="#actions<?=$it->getId()?>">
								<i class="icon-pencil icon-white"></i>
								<span class="caret"></span>
							</a>
                            <?php
                                echo $view->render('core/PopupMenu.php', array('items' => $actions));
                            ?>
						</div>
						<?
						}
						?>
					</td>
					<?php } ?>
				</tr>
			<?
			$it->moveNext();
		}
			echo '</tbody>';
	?>
			<?php
			if ( count($numericFields) > 0 && $list->getItemsCount($it) > 0 ) {
				$totalIt = $list->getTotalIt($numericFields);
				if ( $totalIt->count() > 0 ) {
					echo '<tr class="total">';
					if ( $display_numbers ) echo '<td class="total-empty"></td>';
					if ( $need_to_select ) echo '<td class="total-empty"></td>';
					foreach( $columns as $field ) {
						if ( in_array($field, $numericFields) ) {
							echo '<td style="text-align:'.$list->getColumnAlignment( $field ).'">';
							$list->drawTotal( $totalIt, $field );
							echo '</td>';
						}
						else {
							echo '<td class="total-empty"></td>';
						}
					}
					if ( $display_operations )  echo '<td class="total-empty"></td>';
					echo '</tr>';
				}
			}
			?>
	</tbody>
	</table>
            <?php
            $list->drawFooter();
            ?>
	<?php
	    echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid, 'open' => $hint_open));
	?>
</div> <!-- end wrapper-scroll -->
	
<?php if ( !$tableonly && $autorefresh ) { ?>

<script language="javascript">
	$(document).ready(function() 
	{
		var options = {
			className: "<?=strtolower($object_class)?>",
			scrollable: <?=var_export($scrollable, true)?>,
            draggable: <?=var_export($draggable, true)?>,
			reorder: <?=var_export($reorder, true)?>,
			visiblePages: <?=($visible_pages < 1 ? 999 : $visible_pages)?>,
			pageOpen: <?=(is_numeric($offset) ? $offset : 0)?>,
			totalPages: <?=max($pages,1)?>,
			modifier: '<?=getSession()->getUserIt()->getId()?>',
            groupAttribute: '<?=$groupAttribute?>'
		};

		initializeDocument(<?=($object_id != '' ? $object_id : "''")?>, options);
	});
</script>

<?php } ?>
	