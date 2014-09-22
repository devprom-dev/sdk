<?php 

$columns_number = count($columns);	
$table_row_id = $table_id.'_row_';

$columns_info = array();

foreach( $columns as $key => $attr )
{
	if ( !$list->getColumnVisibility($attr) )
	{
		unset($columns[$key]); continue;
	}
	
	$columns_info[$attr] = array (
			'id' => strtolower($table_col_id.$attr),
			'width' => $list->getColumnWidth( $attr ),
			'align' => $list->getColumnAlignment( $attr ),
			'reference' => $object->IsReference( $attr ) 
	);
}

$display_operations = $list->IsNeedToDisplayOperations();

?>

<a name="top<? echo $offset_name ?>"></a>

<div>
	<table cellspacing="0" cellpadding="0" border="0" style="width:100%;">
		<tbody>
        <tr>
        <td>
        <div class="<?=($list_mode == 'infinite' ? 'table-inner-div' : 'wishes')?>">
        <table id="<?=$table_id?>" class="table-inner <?=$table_class_name?>" created="<?=$created_datetime?>">
	
		<tr class="header-row">
			<?php if ( $display_numbers ) { ?>
			<th class="for-num" width="<?=$numbers_column_width?>" uid="numbers">
				<?=translate('¹')?>
			</th>
			<?php $columns_number++; } ?>

			<th class="for-chk <?=($need_to_select ? 'visible' : 'hidden')?>" width="1%" uid="checkbox">
				<?php if ( $need_to_select ) { ?>
					<input id="to_delete_all<?=$table_id?>" type="checkbox" onclick="checkRows('<?=$table_id?>')">
				<?php } ?>
			</th>
			<?php $columns_number++; ?>
			
			<?php 
			foreach( $columns as $attr ) 
			{
				$align = $columns_info[$attr]['align'];
				$width = $columns_info[$attr]['width'];
				
				$title = str_replace('"', "'", $it->object->getAttributeDescription($attr));
				
				$header_attrs = $list->getHeaderAttributes( $attr );
				
				?>
				<th width="<?=$width?>" uid="<?=strtolower($attr)?>" title="<?=$title?>">
				    <?php if ( $header_attrs['script'] != '#' ) { ?>
				    	<a class="mode-sort" href="<?=$header_attrs['script']?>" style="display:table-cell;">
    						<?=$header_attrs['name']?>
    					</a>
    					<?php if ( $header_attrs['class'] != '' ) { ?>
    					<div class="header-caret <?=$header_attrs['class']?>"><span class="caret" style="margin-top:8px;"></span></div>
    					<?php } ?>
					<?php } else { ?>
					    <?=$header_attrs['name']?>
					<?php } ?>
				</th>
				<?
			}

			if ( $list->IsNeedToDisplayOperations() )
			{
			$width = $list->getColumnWidth( 'Actions' );
			?>
			<th class="for-operation" width="1%">
			</th>
			<?php } ?>
			
        </tr>
			
			<?
			$columns_number++;

			if ( $rows_num < 1 )
			{
			?>
			<tr id="no-elements-row">
				<td colspan="<?=$columns_number?>"><?=$no_items_message?></td>
			</tr>
			<?php 
			}
			
			// get an array of group fields
			if ( !in_array($group_field, $groups) ) $group_field = '';
		
			$group_field_prev_value = '{83C23330-E68F-4852-83D7-6BE4E49FF985}';

			for( $i = 0; $i < $rows_num; $i++)
			{
				if ( !$list->IsNeedToDisplayRow($it) )
				{
					$it->moveNext();
					continue;
				}
				
				if ( $group_field != '' )
				{
					$group_field_value = $it->get($group_field);
					
					if( $group_field_value != $group_field_prev_value ) 
					{
					?>
					<tr class="info" group-id="<?=$group_field_value?>">
						<td colspan="<?=$columns_number?>">
							<? $list->drawGroup($group_field, $it); ?>
						</td>
					</tr>
					<? 
					}
					$group_field_prev_value = $group_field_value;
				}
				?>
				
				<tr id="<?=($table_row_id.($offset + $i + 1))?>" class="<?=$list->getRowClassName($it)?>" object-id="<?=$it->getId()?>" group-id="<?=$group_field_value?>" modified="<?=$it->get('AffectedDate')?>" sort-value="<?=htmlspecialchars($it->get_native($sort_field))?>" sort-type="<?=$sort_type?>">
				
					<? if ( $display_numbers ) { ?>
					<td name="row-num">
						<? $list->drawNumberColumn( $offset + $i + 1 ); ?>
					</td>
					<? } ?>

					<td class="<?=($need_to_select ? 'visible' : 'hidden')?>" uid="checkbox">
						<? if ( $need_to_select && $list->IsNeedToSelectRow( $it ) ) { ?>
							<input class=checkbox type="checkbox" name="to_delete_<? echo $it->getId(); ?>">
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
						
						if( $columns_info[$attr]['reference'] ) 
						{
							echo '<td id="'.$cell_id.'" '.($width != '' ? 'width="'.$width.'"' : '').' title="'.$comment.'" style="text-align:'.$align.';color:'.$color.'">';
							
								$ref_it = $list->getReferenceIt($attr);
				
								if ( $ref_it->count() > 0 )
								{
									$ref_key = $ref_it->getIdAttribute();
					
									$ref_ids = preg_split('/,/', $it->get($attr));
					
									$ref_it = $ref_it->object->createCachedIterator(
											array_values(array_filter($ref_it->getRowset(), function($value) use ($ref_key, $ref_ids)
											{
												return in_array($value[$ref_key], $ref_ids);
											}))
										);
								}

								$list->drawRefCell( $ref_it, $it, $attr);
								
       						echo '</td>';
						} 
						else 
						{
							echo '<td id="'.$cell_id.'" '.($width != '' ? 'width="'.$width.'"' : '').' title="'.$comment.'" style="text-align:'.$align.';color:'.$color.'">';
								$list->drawCell( $it, $attr );
							echo '</td>';
						}
					}

					$actions = $list->getActions($it->getCurrentIt());
					
					if ( $display_operations ) 
                    {
						?>
						<td id="operations">
						<?php 
					if ( count($actions) > 0 ) 
					{
						?>
							<div class="btn-group operation last">
							  <a class="btn btn-small dropdown-toggle actions-button" data-toggle="dropdown" href="#">
								<i class="icon-asterisk icon-gray"></i>
								<span class="caret"></span>
							  </a>
							  <?php
								echo $view->render('core/PopupMenu.php', array (
									'items' => $actions
								));
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
	?>
	</tbody>
	</table>
	</div>
	</td>
	</tr>
	
	</tbody>
	</table>
</div> <!-- end wrapper-scroll -->
	
<div id="documentCache" style="overflow:hidden;height:1px;width:1px;"></div>

<?php if ( !$tableonly ) { ?> 

<script language="javascript">
	$(document).ready(function() 
	{
		var options = {
			className: "<?=strtolower($object_class)?>",
			scrollable: <?=var_export($scrollable, true)?>,
			reorder: <?=var_export($reorder, true)?>
		};

		initializeDocument(<?=($object_id != '' ? $object_id : "''")?>, options);
	});
</script>

<?php } ?>
	