<?php

$last_key = key( array_slice( $attributes, -1, 1, TRUE ) );

$colspan_attributes = array();

if ( $form->getObject() instanceof Task )
{
	$colspan_attributes = array (
			$attributes['Caption']['id']
	);
}

if ( $form->getObject() instanceof Request )
{
	$colspan_attributes = array (
			$attributes['Caption']['id'],
			$attributes['Description']['id']
	);
}

$invisible = array_filter( $attributes, function(&$value) {
		return !$value['visible']; 
});

$colspan_visible = array_filter( $attributes, function(&$value) use($colspan_attributes)
{
		return $value['visible'] && in_array($value['id'], $colspan_attributes); 
});

$visible = array_filter( $attributes, function(&$value) use($colspan_attributes)
{
		return $value['visible'] && !in_array($value['id'], $colspan_attributes); 
});

$attributes_per_column = count($visible) > 12 ? max(9, ceil(count($visible) / 2)) : count($visible);

$chunked_attributes = array_chunk($visible, $attributes_per_column, true);

?>

<?php if ( $warning != '' ) { ?>

<div class="alert alert-error form_warning"><?=$warning?></div>

<?php } ?>

<?php if ( $alert != '' ) { ?>

<div class="alert alert-info"><?=$alert?></div>

<?php } ?>

<?php foreach( $colspan_visible as $key => $attribute ) { ?>

	  <div class="control-group row-fluid" id="fieldRow<?=$key?>">
	    <label class="control-label" for="<?=$attribute['id']?>"><?=$attribute['name']?></label>
	    <div class="controls">
			<? echo $view->render('core/PageFormAttribute.php', $attribute); ?>
	      
	        <?php if ( $attribute['description'] != '' ) { ?>
				<span class="help-block"><?=$attribute['description']?></span>
			<?php } ?>
	    </div>
	  </div>
	
<?php } ?>

<div class="control-set">

<?php foreach( $chunked_attributes as $index => $attributes ) { ?>

	<?php $style = ($formonly ? "width: ".ceil(100/count($chunked_attributes))."%;padding-left: ".($index > 0 ? '20px;' : 0).";" : ""); ?>
	
	<div class="control-column" style="<?=$style?>">
	
	<?php foreach( $attributes as $key => $attribute ) { ?>
	
		<?php if ( $attribute['type'] == 'char' ) { ?>
	
			  <div class="control-group" id="fieldRow<?=$key?>">
			    <label class="control-label" for="<?=$attribute['id']?>"></label>
			    <div class="controls">
					<? echo $view->render('core/PageFormAttribute.php', $attribute); ?>
					
					<?php if ( $attribute['description'] != '' ) { ?>
						<span class="help-block"><?=$attribute['description']?></span>
					<?php } ?>
			    </div>
			  </div>
		
		<?php } else { ?>
		    
			  <div class="control-group row-fluid" id="fieldRow<?=$key?>">
			    <label class="control-label" for="<?=$attribute['id']?>"><?=$attribute['name']?></label>
			    <div class="controls">
					<? echo $view->render('core/PageFormAttribute.php', $attribute); ?>
			      
			        <?php if ( $attribute['description'] != '' ) { ?>
						<span class="help-block"><?=$attribute['description']?></span>
					<?php } ?>
			    </div>
			  </div>
	  	    
		<?php } ?>
		
	<?php } ?>
	
	</div>
	
<?php } ?>	

</div>

<?php foreach( $invisible as $key => $attribute ) { ?>

	<?php if ( !$attribute['visible'] ) { ?>
		<input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>">
	<?php continue; } ?>
	
<?php } ?> 

<?php if ( !$formonly) { ?>

  <div class="control-group">
    <label class="control-label" for="buttons"></label>
    <div class="controls">
        <?php $form->drawButtons(); ?>
    </div>
  </div>

<?php } ?>
