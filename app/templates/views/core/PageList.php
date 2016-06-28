<?php 

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

$columns_number = count($columns_info);
$display_operations = $list->IsNeedToDisplayOperations();
if ( $show_header && $display_numbers ) $columns_number++;
if ( $show_header ) $columns_number++;

?>
<? if ( $message != '' ) { ?>
	<div class="alert">
		<?=$message?>
	</div>
<? } ?>

<a name="top<? echo $offset_name ?>"></a>

<? if ( $toolbar ) { ?>
			<div class="documentToolbar sticks-top" style="height:auto;overflow:hidden;">
				<div class="sticks-top-body hidden-print" id="documentToolbar" style="z-index:2;"></div>
			</div>
<? } ?>


<div>
	<table cellspacing="0" cellpadding="0" border="0" style="width:100%;">
		<tbody>
        <tr>
        <td>
        <div class="<?=($list_mode == 'infinite' ? 'table-inner-div' : 'wishes')?>">
        <table id="<?=$table_id?>" class="table-inner <?=$table_class_name?>" created="<?=$created_datetime?>" uid="<?=$widget_id?>">


			<?php if ( $show_header ) { ?>
		<tr class="header-row">
			<?php if ( $display_numbers ) { ?>
			<th class="for-num" width="<?=$numbers_column_width?>" uid="numbers">
				<?=translate('№')?>
			</th>
			<?php } ?>

			<th class="for-chk <?=($need_to_select ? 'visible' : 'hidden')?>" width="1%" uid="checkbox">
				<?php if ( $need_to_select ) { ?>
					<input id="to_delete_all<?=$table_id?>" tabindex="-1" type="checkbox" onclick="checkRows('<?=$table_id?>')">
				<?php } ?>
			</th>

				<?php
			foreach( $columns as $attr ) 
			{
				$width = $columns_info[$attr]['width'];
				$title = str_replace('"', "'", $it->object->getAttributeDescription($attr));
				
				$header_attrs = $list->getHeaderAttributes( $attr );

				echo '<th width="'.$width.'" uid="'.strtolower($attr).'" title="'.$title.'">';
				if ( $header_attrs['script'] != '#' ) {
					echo '<a class="mode-sort" href="'.$header_attrs['script'].'" style="display:table-cell;">';
						echo $header_attrs['name'];
					echo '</a>';
					if ( $header_attrs['class'] != '' ) {
						echo '<div class="header-caret '.$header_attrs['class'].'"><span class="caret" style="margin-top:8px;"></span></div>';
					}
				}
				else {
					echo $header_attrs['name'];
				}
				echo '</th>';
			}

			if ( $list->IsNeedToDisplayOperations() )
			{
				$columns_number++;
			$width = $list->getColumnWidth( 'Actions' );
			?>
			<th class="for-operation hidden-print" width="1%">
			</th>
			<?php } ?>
			
        </tr>
        <?php } ?>

			<?

			if ( $rows_num < 1 )
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

			for( $i = 0; $i < $rows_num; $i++)
			{
				if ( $group_field != '' )
				{
					$group_field_value = $it->get($group_field);

					if( $group_field_value != $group_field_prev_value ) 
					{
					?>
					<tr id="<?=($table_row_id.'g_'.$group_field_value)?>" class="info" group-id="<?=$group_field_value?>">
						<?php $list->drawGroupRow($group_field, $it, $columns_number); ?>
					</tr>
					<? 
					}
					$group_field_prev_value = $group_field_value;
				}

				if ( !$list->IsNeedToDisplayRow($it) )
				{
					$it->moveNext();
					continue;
				}
				
				?>
				
				<tr id="<?=($table_row_id.($offset + $i + 1))?>" class="<?=$list->getRowClassName($it)?>" object-id="<?=$it->getId()?>" state="<?=$it->get('State')?>" project="<?=ObjectUID::getProject($it)?>" group-id="<?=$group_field_value?>" modified="<?=$it->get('AffectedDate')?>" sort-value="<?=htmlspecialchars($it->get_native($sort_field))?>" sort-type="<?=$sort_type?>">
				
					<? if ( $display_numbers ) { ?>
					<td name="row-num">
						<? $list->drawNumberColumn( $offset + $i + 1 ); ?>
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
						
						if( $columns_info[$attr]['reference'] ) 
						{
							echo '<td id="'.$cell_id.'" '.($width != '' ? 'width="'.$width.'"' : '').' title="'.$comment.'" style="text-align:'.$align.';color:'.$color.'">';
							
								$list->drawRefCell( $list->getFilteredReferenceIt($attr, $it->get($attr)), $it, $attr);
								
       						echo '</td>';
						} 
						else 
						{
							echo '<td id="'.$cell_id.'" '.($width != '' ? 'width="'.$width.'"' : '').' title="'.$comment.'" style="text-align:'.$align.';color:'.$color.'">';
								$list->drawCell( $it, $attr );
							echo '</td>';
						}
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
							?>
							<div class="btn-group operation last">
								<a id="<?=$action['uid']?>" class="btn btn-info btn-mini dropdown-toggle actions-button" href="#" onclick="<?=(!in_array($action['url'],array('','#')) ? $action['url'] : $action['click'])?>"><?=$action['name']?></a>
							</div>
							<?
						} else if ( count($actions) > 0 )
						{
						?>
						<div class="btn-group btn-group-actions operation">
							<a class="btn btn-mini dropdown-toggle actions-button" data-toggle="dropdown" href="#" data-target="#actions<?=$it->getId()?>">
								<i class="icon-asterisk icon-gray"></i>
								<span class="caret"></span>
							</a>
						</div>
						<div class="btn-group dropdown-fixed last" id="actions<?=$it->getId()?>">
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
	
<?php if ( !$tableonly && $autorefresh ) { ?>

<script language="javascript">
	$(document).ready(function() 
	{
		var options = {
			className: "<?=strtolower($object_class)?>",
			scrollable: <?=var_export($scrollable, true)?>,
			reorder: <?=var_export($reorder, true)?>,
			visiblePages: <?=($visible_pages < 1 ? 999 : $visible_pages)?>,
			pageOpen: <?=(is_numeric($offset) ? $offset : 0)?>,
			totalPages: <?=max($pages,1)?>
		};

		initializeDocument(<?=($object_id != '' ? $object_id : "''")?>, options);
	});
</script>

<?php } ?>
	