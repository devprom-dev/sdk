<?php
global $tabindex;

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

$conditionsRowsNum = defined('AUTOACTION_CONDITIONS_ROWSNUM') ? AUTOACTION_CONDITIONS_ROWSNUM : 5;

$default_conditions = array (
    array (
        'Condition' => 'Description',
        'Operator' => 'contains',
        'Value' => ''
    )
);
$default_conditions = array_pad(
    $default_conditions,
    $conditionsRowsNum,
    array (
        'Condition' => '',
        'Operator' => '',
        'Value' => ''
    )
);

if ( !is_array($conditions['items']) ) $conditions['items'] = array();
if ( count($conditions['items']) < count($default_conditions) )
{
	$append_items = count($default_conditions) - count($conditions['items']); 
	$conditions['items'] = array_merge(
			$conditions['items'], 
			array_slice($default_conditions, max(0,count($default_conditions) - $append_items), $append_items)
		);
}

?>
<span class="input-block-level well well-text">
	<div style="padding-left:8px;padding-top:6px;">
		<label class="radio pull-left" style="padding-right:24px;">
  			<input type="radio" name="ConditionsMode" value="all" <?=(in_array($conditions['mode'],array('','all')) ? 'checked' : '')?>>
  			<?=text(2441)?>
		</label>
		<label class="radio pull-left">
  			<input type="radio" name="ConditionsMode" value="any" <?=($conditions['mode']=='any' ? 'checked' : '')?>>
  			<?=text(2442)?>
		</label>
	</div>
	<div class="clearfix" ></div>
	<div style="padding-top:12px;padding-left:8px;padding-right:8px;">
	<?php foreach( $conditions['items'] as $key => $condition ) { ?>
		<div style="display:table;width:100%;padding-bottom: 12px;">
			<div style="display:table-cell;width:25%;padding-right:12px;">
			<?php 
				$field_attributes->setName('Condition'.$key);
				$field_attributes->setValue($condition['Condition']);
				$field_attributes->draw($view);
			?>
			</div>
			<div style="display:table-cell;width:20%;padding-right:12px;">
			<?php 
				$field_operators->setName('Operator'.$key);
				$field_operators->setValue($condition['Operator']);
				$field_operators->draw($view);
			?>
			</div>
			<div style="display:table-cell;width:55%;vertical-align:top;">
				<input class="input-block-level" tabindex="<?=($tabindex++)?>" type="text" value="<?=IteratorBase::utf8towin(urldecode($condition['Value']))?>" name="Value<?=$key?>">
			</div>
		</div>
	<?php } ?>
	</div>
</span>