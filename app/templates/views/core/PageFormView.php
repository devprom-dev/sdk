<?php

$last_key = key( array_slice( $attributes, -1, 1, TRUE ) );

$colspan_attributes = array();
if ( $attributes['Caption']['visible'] ) {
	$colspan_attributes[] = $attributes['Caption']['id'];
}
if ( $attributes['UID']['visible'] ) {
	$colspan_attributes[] = $attributes['UID']['id'];
}

if ( array_key_exists('Description', $attributes) && count($shortAttributes) > 0 ) {
	$colspan_attributes[] = $attributes['Description']['id'];
}

$invisible = array_filter( $attributes, function(&$value) {
		return !$value['visible']; 
});

$colspan_visible = array_filter( $attributes, function(&$value) use($colspan_attributes) {
	return $value['visible'] && in_array($value['id'], $colspan_attributes);
});

$formGroups = array(
	'deadlines' => translate('Сроки'),
	'hierarchy' => translate('Декомпозиция'),
	'source-issue' => translate('Пожелание'),
	'additional' => translate('Дополнительно'),
	'trace' => translate('Трассировка')
);

$shortVisible = array();
foreach( $attributes as $key => $attribute ) {
	if ( !in_array($key, $shortAttributes) ) continue;
	if ( !$attribute['visible'] ) continue;
	$group = array_shift(
		array_intersect($form->getObject()->getAttributeGroups($key),array_keys($formGroups))
	);
	if ( $group != '' ) continue;
	$shortVisible[$key] = $attribute;
	unset($attributes[$key]);
}
$shortVisible = array_chunk(
    $shortVisible,
    ceil(count($shortVisible) / ($_REQUEST['screenWidth'] >= 1400 && count($source_parms) < 1 ? 4 : 2)),
    true
);

$visible = array_filter( $attributes, function(&$value) use($colspan_attributes) {
	return $value['visible'] && !in_array($value['id'], $colspan_attributes);
});

$top = array();
foreach( $attributes as $key => $attribute ) {
	if ( in_array($key, array('Caption','UID')) && in_array($attribute['id'], $colspan_attributes) ) {
		$top[$key] = $attribute;
	}
}

foreach( $visible as $key => $attribute ) {
	$group = array_shift(
		array_intersect($form->getObject()->getAttributeGroups($key),array_keys($formGroups))
	);
	if ( $key == 'Description' ) $group = '';
	$chunked_attributes[$group][$key] = $attribute;
}

$groups = array_keys($chunked_attributes);
$formGroups = array_filter($formGroups, function($key) use ($groups) {
        return in_array($key, $groups);
    }, ARRAY_FILTER_USE_KEY);

?>

<?php if ( $warning != '' ) { ?>

<div class="alert alert-error form_warning"><?=$warning?></div>

<?php } ?>

<?php if ( $alert != '' ) { ?>

<div class="alert alert-info"><?=$alert?></div>

<?php } ?>

<?php
?>

<? if ( !$form->getEditMode() ) { ?>
<h4 class="bs page-form-view">
    <?
    if ( $top['Caption']['field'] instanceof FieldWYSIWYG ) {
        $top['Caption']['field']->draw();
    }
    else {
        echo $top['Caption']['text'];
    }
    unset($top['Caption']);
    ?>
</h4>
<? } ?>

<div class="accordion-wrap">
<?php if ( count($formGroups) > 0 ) { ?>
<div class="accordion-heading">
    <a class="to-drop-btn" href="#collapseOne" tabindex="-1">
        <span class="caret"></span>
        <?=translate('Свойства')?>
    </a>
</div>
<div id="collapseOne" class="accordion-body" tabindex="-1" style="padding-top: 12px;">
<?php } ?>

    <div class="control-set title-set">
        <? foreach($top as $key => $attribute ) { ?>
            <? if ( !$attribute['visible'] ) continue; ?>
            <div class="control-column">
                <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
            </div>
        <? } ?>
    </div>
    <?php foreach( $colspan_visible as $key => $attribute ) { ?>
        <? if ( $key == 'Caption' ) continue; ?>
        <? if ( $key == 'UID' ) continue; ?>
        <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
    <?php } ?>

    <div class="control-set">
        <?php foreach( $shortVisible as $index => $attributes ) { ?>
            <div class="control-column">
                <?php foreach( $attributes as $key => $attribute ) { ?>
                    <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="control-set">
        <div class="control-column">
            <?php foreach( $chunked_attributes[''] as $key => $attribute ) { ?>
                <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
            <?php } ?>
        </div>
    </div>

    <?php if ( count($formGroups) > 0 ) { ?>
</div>
<?php } ?>

<div class="control-set">
	<div class="control-column" style="<?=$style?>">
			<?php foreach( $formGroups as $group => $title ) { ?>
			<?php
				$valuesCount = 0;
				foreach( $chunked_attributes[$group] as $key => $attribute ) {
					if ( $attribute['value'] != '' ) $valuesCount++;
				}
				$stateKey = 'devprom_page_form_section#collapse' . $group;
				$openState = array_key_exists($stateKey, $_COOKIE) ? $_COOKIE[$stateKey] && $valuesCount > 0 : true;
			?>
			<div class="accordion-heading">
				<a class="to-drop-btn <?=($openState ? "" : "collapsed")?>" data-toggle="collapse" href="#collapse<?=$group?>" tabindex="-1">
					<span class="caret"></span>
					<?=$title?>
					<?=($valuesCount > 0 ? ' ('.$valuesCount.')': '')?>
				</a>
			</div>
			<div id="collapse<?=$group?>" class="accordion-body <?=($openState ? "in" : "out")?> collapse" style="padding-top: 12px;">
				<?php foreach( $chunked_attributes[$group] as $key => $attribute ) { ?>
					<? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
				<?php } ?>
				</div>
			<?php } ?>
	</div>
</div>

</div>

<?php foreach( $invisible as $key => $attribute ) { ?>

	<?php if ( !$attribute['visible'] ) { ?>
		<input id="<?=$attribute['id']?>" type="hidden" name="<?=$key?>" value="<?=$attribute['value']?>" referenceName="<?=$attribute['referenceName']?>">
	<?php continue; } ?>
	
<?php } ?> 

<?php if ( !$formonly && $form->getEditMode() ) { ?>
<div class="control-group">
    <label class="control-label" for="buttons"></label>
    <div class="controls">
        <?php $form->drawButtons(); ?>
    </div>
</div>
<?php } ?>

<script language="javascript">
	cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
	$(document).ready(function() {
		$(".accordion-heading > a").click(function() {
			cookies.set('devprom.page.form.section' + $(this).attr('href'),
				$(this).hasClass('collapsed') ? '1' : '0');
		});
	});
</script>