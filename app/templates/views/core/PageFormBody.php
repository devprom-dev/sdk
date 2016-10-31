<?php

$last_key = key( array_slice( $attributes, -1, 1, TRUE ) );

$colspan_attributes = array();
if ( $attributes['Caption']['visible'] ) {
	$colspan_attributes[] = $attributes['Caption']['id'];
}
if ( $attributes['UID']['visible'] ) {
	$colspan_attributes[] = $attributes['UID']['id'];
}

if ( $form->getObject() instanceof Request ) {
	$colspan_attributes[] = $attributes['Description']['id'];
}

$invisible = array_filter( $attributes, function(&$value) {
		return !$value['visible']; 
});

$colspan_visible = array_filter( $attributes, function(&$value) use($colspan_attributes) {
	return $value['visible'] && in_array($value['id'], $colspan_attributes);
});

$shortVisible = array();
foreach( $attributes as $key => $attribute ) {
	if ( !in_array($key, $shortAttributes) ) continue;
	if ( !$attribute['visible'] ) continue;
	$shortVisible[$key] = $attribute;
	unset($attributes[$key]);
}
$shortVisible = array_chunk($shortVisible, ceil(count($shortVisible) / ($_REQUEST['screenWidth'] >= 1400 ? 4 : 2)), true);

$visible = array_filter( $attributes, function(&$value) use($colspan_attributes) {
	return $value['visible'] && !in_array($value['id'], $colspan_attributes);
});

$attributes_per_column = count($visible) > 12 && $formonly ? max(9, ceil(count($visible) / 2)) : count($visible);
$chunked_attributes = array_chunk($visible, $attributes_per_column, true);

$top = array();
foreach( $attributes as $key => $attribute ) {
	if ( in_array($key, array('Caption','UID')) && in_array($attribute['id'], $colspan_attributes) ) {
		$top[$key] = $attribute;
	}
}
?>

<?php if ( $warning != '' ) { ?>

<div class="alert alert-error form_warning"><?=$warning?></div>

<?php } ?>

<?php if ( $alert != '' ) { ?>

<div class="alert alert-info"><?=$alert?></div>

<?php } ?>

<?php
?>

<div class="control-set title-set">
	<? foreach($top as $key => $attribute ) { ?>
	<div class="control-column">
		<div class="control-group row-fluid" id="fieldRow<?=$key?>">
			<label class="control-label" for="<?=$attribute['id']?>"><?=$attribute['name']?></label>
			<div class="controls">
				<? echo $view->render('core/PageFormAttribute.php', $attribute); ?>
			</div>
		</div>
	</div>
	<? } ?>
</div>

<?php foreach( $colspan_visible as $key => $attribute ) { ?>
	<? if ( $key == 'Caption' ) continue; ?>
	<? if ( $key == 'UID' ) continue; ?>
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
	<?php foreach( $shortVisible as $index => $attributes ) { ?>
		<div class="control-column">
			<?php foreach( $attributes as $key => $attribute ) { ?>
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
		</div>
	<?php } ?>
</div>
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
		
		<?php } else if ( is_object($attribute['field']) || $attribute['html'] != '' ) { ?>
		    
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
		<input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>" referenceName="<?=$attribute['referenceName']?>">
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
