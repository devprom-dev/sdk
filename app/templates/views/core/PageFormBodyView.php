<?php

$last_key = key( array_slice( $attributes, -1, 1, TRUE ) );

$colspan_attributes = array();
if ( $attributes['Caption']['visible'] ) {
	$colspan_attributes[] = $attributes['Caption']['id'];
}
if ( $attributes['UID']['visible'] ) {
	$colspan_attributes[] = $attributes['UID']['id'];
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
	$group = array_shift(
		array_intersect($form->getObject()->getAttributeGroups($key),array_keys($form_groups))
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
		array_intersect($form->getObject()->getAttributeGroups($key),array_keys($form_groups))
	);
	$chunked_attributes[$group][$key] = $attribute;
}

$groups = array_keys($chunked_attributes);
$groupKeys = array_filter($form_groups, function($key) use ($groups) {
        return in_array($key, $groups);
    }, ARRAY_FILTER_USE_KEY);

$form_groups = array();
foreach( $groupKeys as $key => $title ) {
    $form_groups[$title][] = $key;
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

<? if ( !$form->getEditMode() ) { ?>
<h4 class="bs page-form-view">
    <?
    if ( $top['Caption']['field'] instanceof FieldEditable ) {
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

<?
    $wasToolbar = false;
    foreach( $attributes as $key => $attribute )
    {
        if ( !$attribute['visible'] ) continue;
        if ( $attribute['type'] != 'wysiwyg' ) continue;
        ?>
        <div class="accordion-heading">
            <a class="to-drop-btn" data-toggle="collapse" tabindex="-1">
                <span class="caret"></span>
                <?=$attribute['name']?>
            </a>
        </div>
        <div class="accordion-body in collapse" style="overflow: hidden;">
            <?
            if ( is_a($attribute['field'], 'Field') ) {
                if ( !$wasToolbar && $attribute['field']->contentEditable() && $attribute['editable'] ) { $wasToolbar = true; ?>
                    <div class="hidden-print documentToolbar sticks-top" style="overflow:hidden;">
                        <div class="sticks-top-body hidden-print" id="documentToolbar" style="z-index:2;"></div>
                    </div>
                <? }
                $attribute['field']->draw($this);
            }
            else {
                echo '<p>'.$attribute['text'].'</p>';
            }
            ?>
            <br/>
        </div>
        <?
    }
?>

<?php

$top = array_filter($top, function($attribute) {
   return $attribute['visible'] && $attribute['type'] != 'wysiwyg';
});

$colspan_visible = array_filter($colspan_visible, function($attribute,$key) {
   return !in_array($key,array('Caption','UID'));
}, ARRAY_FILTER_USE_BOTH);

$propertiesSection = count($form_groups) > 0  && (count($top) > 0 || count($colspan_visible) > 0 || count($shortVisible) > 0);

if ( $propertiesSection ) { ?>
<div class="accordion-heading">
    <a class="to-drop-btn" tabindex="-1" data-toggle="collapse" href="#collapseOne">
        <span class="caret"></span>
        <?=translate('Свойства')?>
    </a>
</div>
<div id="collapseOne" class="accordion-body in" tabindex="-1">
<?php } ?>

    <div class="control-set title-set">
        <? foreach($top as $key => $attribute ) { ?>
            <div class="control-column">
                <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
            </div>
        <? } ?>
    </div>
    <?php foreach( $colspan_visible as $key => $attribute ) { ?>
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
                <? if ( $attribute['type'] == 'wysiwyg' ) continue; ?>
                <? echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute)); ?>
            <?php } ?>
        </div>
    </div>

<?php if ( $propertiesSection ) { ?>
</div>
<?php } ?>

<div class="control-set">
	<div class="control-column" style="<?=$style?>">
			<?php foreach( $form_groups as $title => $groups ) { ?>
			<?php
				$valuesCount = 0;
                foreach( $groups as $group ) {
                    foreach ($chunked_attributes[$group] as $key => $attribute) {
                        if ($attribute['value'] != '') $valuesCount++;
                    }
                }
                $groupSectionId = join('',$groups);
				$stateKey = 'devprom_page_form_section#collapse' . $groupSectionId;
				$openState = array_key_exists($stateKey, $_COOKIE) ? $_COOKIE[$stateKey] > 0 : $valuesCount > 0;
			?>
			<div class="accordion-heading" style="padding-top: 12px;">
				<a class="to-drop-btn <?=($openState ? "" : "collapsed")?>" data-toggle="collapse" href="#collapse<?=$groupSectionId?>" tabindex="-1">
					<span class="caret"></span>
					<?=$title?>
					<?=($valuesCount > 0 ? ' ('.$valuesCount.')': '')?>
				</a>
			</div>
			<div id="collapse<?=$groupSectionId?>" class="accordion-body <?=($openState ? "in" : "collapse")?>">
                <?php
                foreach( $groups as $group ) {
                    foreach( $chunked_attributes[$group] as $key => $attribute ) {
                        if ( $attribute['type'] == 'wysiwyg' ) continue;
                        echo $view->render('core/PageFormBodyAttribute.php', array('key' => $key, 'attribute' => $attribute));
                    }
                }
                ?>
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