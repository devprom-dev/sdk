<?php
if ( $_REQUEST['attributesonly'] == '' ) {
    $view->extend('core/PageBody.php');
}

$linked_attrs = array(
	'SourceCode', 
	'TestExecution', 
	'HelpPage', 
	'TestScenario', 
	'Requirement', 
	'Question',
	'Links',
	'LinksAttachment',
    'ProjectPage'
);

$fields_dont_skip_if_empty = array (
	'Watchers',
	'Fact',
	'Tags',
	'Estimation',
    'Type',
    'Owner',
    'PlannedRelease',
    'Iteration',
    'Deadlines',
    'Severity'
);

$fields_to_be_skiped = $form->getObject()->getAttributesByGroup('form-column-skipped');

// attributes to be displayed in first column
if ( array_key_exists('ResponseSLA', $attributes) ) {
    unset($attributes['PlannedResponse']);
}
if ( array_key_exists('LeadTimeSLA', $attributes) ) {
    unset($attributes['Estimation']);
}

$important_attributes = $form->getObject()->getAttributesByGroup('form-column-first');

$columns = array();
$hidden_class = array();

$recent_column = 0;

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $important_attributes) ) continue;
    if ( in_array($name, $fields_to_be_skiped) ) continue;
	if ( !$attribute['visible'] ) continue;
	if ( $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty) ) continue;

	$columns[$recent_column][$name] = $attribute;
}

// attributes to be displayed in second column
$recent_column++;

foreach( $attributes as $name => $attribute ) 
{
	if ( in_array($name, array_keys($columns[0])) ) continue;
	if ( in_array($name, $linked_attrs) ) continue;
	if ( in_array($name, $fields_to_be_skiped) ) continue;
    if ( is_null($attribute['field']) ) continue;

	if ( !$attribute['visible'] ) continue;
    if ( in_array($attribute['type'], array('wysiwyg')) ) continue;
	if ( in_array($name, array('Attachment', 'Tasks', 'Caption')) ) continue;

	if ( $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty) ) continue;
	
	$columns[$recent_column][$name] = $attribute;
}

// attributes to be displayed in third column
if ( $_REQUEST['attributesonly'] == '' ) {
    $recent_column++;
    if ( is_array($attributes['Attachment']) ) {
        $columns[$recent_column]['Attachment'] = $attributes['Attachment'];
    }
    if ( is_array($attributes['Tasks']) ) {
        $columns[$recent_column]['Tasks'] = $attributes['Tasks'];
    }
    $section_class['Tasks'] = 'hidden-desktop';
    $section_class['Trace'] = 'hidden-desktop';

    $wordyAttributes = array();
    foreach( $attributes as $name => $attribute )
    {
        if ( !in_array($name, $linked_attrs) && $name != 'Attachment' ) continue;
        if ( is_null($attribute['field']) ) continue;
        if ( !$attribute['visible'] ) continue;
        if ( is_null($attribute['field']) ) continue;

        if ( count(preg_split('/,/',$attribute['value'])) > 20 ) {
            $wordyAttributes[$name] =  $attribute;
        }
        else {
            $columns[$recent_column][$name] = $attribute;
        }
    }
}

// other attributes
$trace_attributes = array();

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $linked_attrs) ) continue;
	if ( !$attribute['visible'] ) continue;
	if ( array_key_exists($name, $wordyAttributes) ) continue;

	$trace_attributes[] = $attribute;
}

?>
<div class="history-user">

<div class="actions hidden-print">
	<?php
	if ( count($actions) > 0 && $action != 'show' ) {
		echo $view->render('core/PageFormButtons.php', array(
		    'actions' => $actions,
            'sections' => $sections
        ));
	}
	?>
</div> <!-- end actions -->

<?php
    echo $view->render('core/PageBreadcrumbs.php', array(
        'navigation_url' => $navigation_url,
        'parent_widget_url' => $parent_widget_url,
        'parent_widget_title' => $parent_widget_title,
        'nearest_title' => $nearest_title,
        'has_caption' => $has_caption,
        'caption' => $caption,
        'uid' => $uid,
        'uid_url' => $uid_url,
        'state_name' => $state_name,
        'form' => $form,
        'listWidgetIt' => $listWidgetIt,
        'nextUrl' => $_REQUEST['attributesonly'] == '' ? $nextUrl : "",
        'nextTitle' => $nextTitle,
        'title' => $title
    ));
?>

<div class="form-container">
<h4 class="bs page-form-view" style="width:90%;">
	<? 
	if ( $attributes['Caption']['field'] instanceof FieldEditable )
	{
	    $attributes['Caption']['field']->draw();
	}
	else
	{ 
	    echo $attributes['Caption']['text'];
	}
	?>
</h4>

<?php if ( $warning != '' ) { ?>

<div class="alert alert-error"><?=$warning?></div>

<?php } ?>

<?php if ( $alert != '' ) { ?>

<div class="alert alert-info"><?=$alert?></div>

<?php } ?>

<div class="accordion-wrap">
	<div class="accordion-heading">
	  <a class="to-drop-btn" href="#collapseOne" tabindex="-1">
		<span class="caret"></span>
		<?=translate('Свойства')?>
	  </a>
	</div>
	<div id="collapseOne" class="accordion-body" tabindex="-1">
		<div class="row">
			<div class="properties-cell-1">
				<div style="width:100%;display:table;">
					<?php $column = $columns[0]; ?>
					<div class="properties-column-30">
						<table class="properties-table">
						<?php foreach( $column as $ref_name => $attribute ) { ?>
							<tr name="<?=$ref_name?>">
								<th title="<?=htmlentities(strip_tags($attribute['description']))?>">
									<?=$attribute['name']?>:
								</th>
								<td>
									<?
									if ( is_array($refs_actions[$ref_name]) ) {
										if ( $ref_name == 'BlockReason' ) {
											echo '<div class="alert alert-blocked">';
										}
										echo $this->render('core/EmbeddedRowTitleMenu.php', array (
												'title' => $attribute['text'],
												'items' => $refs_actions[$ref_name]
										));
										if ( $ref_name == 'BlockReason' ) {
											echo '</div>';
										}
									}
									else {
										echo $view->render('core/PageFormViewAttribute.php', $attribute);
									}
									?>
								</td>
							</tr>
						<?php } ?>
						</table>
					</div>
					<?php $column = $columns[1]; ?>
					<div class="properties-column-31">
						<table class="properties-table">
							<?php foreach( $column as $ref_name => $attribute ) { ?>
								<tr name="<?=$ref_name?>">
									<th title="<?=htmlentities(strip_tags($attribute['description']))?>">
                                        <?php if ( $attribute['type'] != 'char' ) { ?>
										    <?=$attribute['name']?>:
                                        <?php } ?>
									</th>
									<td>
										<?
										if ( is_array($refs_actions[$ref_name]) ) {
											echo $this->render('core/EmbeddedRowTitleMenu.php', array (
												'title' => $attribute['text'],
												'items' => $refs_actions[$ref_name]
											));
										}
										else {
											echo $view->render('core/PageFormViewAttribute.php', $attribute);
										}
										?>
									</td>
								</tr>
							<?php } ?>
						</table>
					</div>
				</div>

				<!--  -->
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

			</div>
            <? if ( is_array($columns[2]) ) { ?>
			<div class="properties-cell-2">
				<div class="properties-column">&nbsp;</div>
				<?php $column = $columns[2]; ?>
				<div class="properties-column-32 file-drop-target">
					<table class="properties-table">
						<?php foreach( $column as $ref_name => $attribute ) { ?>
							<tr name="<?=$ref_name?>">
								<th title="<?=htmlentities(strip_tags($attribute['description']))?>">
									<?=$attribute['name']?>:
								</th>
								<td>
									<?
									if ( is_array($refs_actions[$ref_name]) ) {
										echo $this->render('core/EmbeddedRowTitleMenu.php', array (
											'title' => $attribute['text'],
											'items' => $refs_actions[$ref_name]
										));
									}
									else {
										echo $view->render('core/PageFormViewAttribute.php', $attribute);
									}
									?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
            <? } ?>
		</div>
	</div>

    <?php if ( $attributes['Attachment']['visible'] ) { ?>
        <div class="accordion-heading <?=$section_class['Trace']?>">
            <a class="to-drop-btn" data-toggle="collapse" href="#collapseFour" tabindex="-1">
                <span class="caret"></span>
                <?=$attributes['Attachment']['name']?>
            </a>
        </div>
        <div id="collapseFour" class="accordion-body in collapse <?=$section_class['Trace']?>">
            <? echo $view->render('core/PageFormViewAttribute.php', $attributes['Attachment']); ?>
            <div class="clearfix"></div>
            <br/>
        </div>
    <?php } ?>

	<?php if ( $attributes['Tasks']['visible'] ) { ?>

	<div class="accordion-heading <?=$section_class['Tasks']?>">
	  <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseThree" tabindex="-1">
		<span class="caret"></span>
		<?=$attributes['Tasks']['name']?>
	  </a>
	</div>
				
	<div id="collapseThree" class="accordion-body collapse <?=$section_class['Tasks']?>">
	    <? echo $view->render('core/PageFormViewAttribute.php', $attributes['Tasks']); ?>
	    <br/>
	</div>
	
	<?php } ?>

	<?php foreach( $wordyAttributes as $name => $attribute ) { ?>
		<div class="accordion-heading">
			<a class="to-drop-btn" data-toggle="collapse" href="#collapse<?=$name?>" tabindex="-1">
				<span class="caret"></span>
				<?=$attribute['name']?>
			</a>
		</div>
		<div id="collapse<?=$name?>" class="accordion-body">
			<? echo $view->render('core/PageFormViewAttribute.php', $attribute); ?>
			<br/>
		</div>
	<?php }	?>

	<?php if ( count($trace_attributes) > 0 ) { ?>
	<div class="accordion-heading <?=$section_class['Trace']?>">
	  <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseFive" tabindex="-1">
		<span class="caret"></span>
		<?=text(1243)?>
	  </a>
	</div>
	<div id="collapseFive" class="accordion-body collapse <?=$section_class['Trace']?>">
		<table class="properties-table">
		<?php foreach( $trace_attributes as $name => $attribute ) { ?>
			<tr>
				<th>										
					<?=$attribute['name']?>: 
				</th>
				<td>
				    <? echo $view->render('core/PageFormViewAttribute.php', $attribute); ?>
				</td>
			</tr>
		<?php } ?>
		</table>							
	</div>
	<?php }	?>
	
<?php if (!$formonly || $_REQUEST['attributesonly'] != '') { ?>

	<div class="accordion-heading">
	  <a id="comments-section" class="to-drop-btn" tabindex="-1">
		<span class="caret"></span>
		<?=text(1346)?>
		<?=($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
	  </a>
	</div>
	<div id="collapseComments" class="accordion-body in collapse" style="overflow: inherit;">
        <?php 
        	echo $view->render('core/PageSections.php', array(
        		'sections' => $sections,
        		'object_class' => $object_class,
        		'object_id' => $object_id,
				'placement' => 'bottom'
        	));
        ?>
	</div>

<?php } ?>

</div> <!-- end accordion -->
</div>
</div>

<script language="javascript">
	cookies.setOptions({expires:new Date(new Date().getFullYear() + 1, 1, 1)});
	
	$(document).ready(function() {
		$(".accordion-heading > a")
			.slice(1).click(function() {
		      cookies.set('devprom.request.form.section' + $(this).attr('href'), 
		  	      	$(this).hasClass('collapsed') ? '1' : '0');
			});
	});
</script>