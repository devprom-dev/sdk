<?php

$view->extend('pm/RequestPageBody.php'); 

$view['slots']->output('_content');

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
	'Estimation'
);

$fields_to_be_skiped = array (
	'FinishDate',
    'State'
);

// attributes to be displayed in first column

$important_attributes = array( 
	'Type', 
	'Priority', 
	'Author',
    'ExternalAuthor',
	'Estimation',
	'Deadlines',
	'OrderNum',
    'PlannedRelease',
	'ClosedInVersion',
	'SubmittedVersion',
	'Iteration',
	'Owner',
	'TestFound'
);

$columns = array();
$hidden_class = array();

$recent_column = 0;

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $important_attributes) && !$attribute['custom'] ) continue;
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
	if ( in_array($name, array('Attachment', 'Tasks', 'Caption', 'Description')) ) continue;

	if ( $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty) ) continue;
	
	$columns[$recent_column][$name] = $attribute;
}

// attributes to be displayed in fourth column
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

	if ( count(preg_split('/,/',$attribute['value'])) > 6 ) {
		$wordyAttributes[$name] =  $attribute;
	}
	else {
		$columns[$recent_column][$name] = $attribute;
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

<div class="actions hidden-print">
	<?php
	if ( count($actions) > 0 && $action != 'show' ) {
		echo $view->render('core/PageFormButtons.php', array(
		    'actions' => $actions,
            'sections' => $bottom_sections
        ));
	}
	?>
</div> <!-- end actions -->

<ul class="breadcrumb hidden-print">
<?php
	if ( $uid != '' ) {
		if ( $navigation_url != '' ) {
            if ( $parent_widget_url != '' ) {
                echo '<li><a href="'.$parent_widget_url.'">'.$parent_widget_title.'</a><span class="divider">/</span></li>';
            }
			echo '<li><a href="'.$navigation_url.'">'.$navigation_title.'</a><span class="divider">/</span></li>';
		}
		else if ( $caption != '' ) {
			echo '<li>'.$caption.'<span class="divider">/</span></li>';
		}
		echo '<li>'.$view->render('core/Clipboard.php', array ('url' => $uid_url, 'uid' => $uid)).'</li>';

        if ( $state_name != '' ) {
            echo '<li class="clip" style="margin-left:8px;">'.$view->render('pm/StateColumn.php', array (
                    'color' => $form->getObjectIt()->get('StateColor'),
                    'name' => $form->getObjectIt()->get('StateName'),
                    'terminal' => $form->getObjectIt()->get('StateTerminal') == 'Y',
                    'id' => 'state-label'
                )).'</li>';
        }

		if ( $nextUrl != '' ) {
            echo '<li class="hidden-phone next-item">&#10140; <a class="btn btn-link" title="'.text(2333).'" href="'.$nextUrl.'">'.$nextTitle.'</a></li>';
        }
	}
	else {
		echo '<li><a href="'.$navigation_url.'">'.$navigation_title.'</a></li>';
	}
?>
</ul> <!-- end breadcrumb -->

<h4 class="bs" style="width:90%;">
	<? 
	if ( $attributes['Caption']['field'] instanceof FieldWYSIWYG )
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
										$title = IteratorBase::getHtmlValue($attribute['text']);
										if ( $ref_name == 'BlockReason' ) {
											echo '<div class="alert alert-blocked">';
										}
										echo $this->render('core/EmbeddedRowTitleMenu.php', array (
												'title' => $title,
												'items' => $refs_actions[$ref_name]
										));
										if ( $ref_name == 'BlockReason' ) {
											echo '</div>';
										}
									}
									else {
										echo $view->render('pm/PageFormAttribute.php', $attribute);
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
										<?=$attribute['name']?>:
									</th>
									<td>
										<?
										if ( is_array($refs_actions[$ref_name]) ) {
											echo $this->render('core/EmbeddedRowTitleMenu.php', array (
												'title' => IteratorBase::getHtmlValue($attribute['text']),
												'items' => $refs_actions[$ref_name]
											));
										}
										else {
											echo $view->render('pm/PageFormAttribute.php', $attribute);
										}
										?>
									</td>
								</tr>
							<?php } ?>
						</table>
					</div>
				</div>

				<!--  -->
                <? if ( is_array($attributes['Description']) ) { ?>
				<div class="accordion-heading">
					<a class="to-drop-btn <?=($_COOKIE['devprom_request_form_section#collapseTwo']=='0'?'collapsed':'')?>" data-toggle="collapse" href="#collapseTwo" tabindex="-1">
						<span class="caret"></span>
						<?=$attributes['Description']['name']?>
					</a>
				</div>
				<div id="collapseTwo" class="accordion-body <?=($_COOKIE['devprom_request_form_section#collapseTwo']=='0'?'':'in')?> collapse">
					<?
					if ( is_a($attributes['Description']['field'], 'Field') )
					{
						$attributes['Description']['field']->draw($this);
					}
					else
					{
						echo '<p>'.$attributes['Description']['text'].'</p>';
					}
					?>
					<br/>
				</div>
                <? } ?>

			</div>
			<div class="properties-cell-2">
				<div class="properties-column">&nbsp;</div>
				<?php $column = $columns[2]; ?>
				<div class="properties-column-32">
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
											'title' => IteratorBase::getHtmlValue($attribute['text']),
											'items' => $refs_actions[$ref_name]
										));
									}
									else {
										echo $view->render('pm/PageFormAttribute.php', $attribute);
									}
									?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<?php if ( $attributes['Tasks']['visible'] ) { ?>

	<div class="accordion-heading <?=$section_class['Tasks']?>">
	  <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseThree" tabindex="-1">
		<span class="caret"></span>
		<?=$attributes['Tasks']['name']?>
	  </a>
	</div>
				
	<div id="collapseThree" class="accordion-body collapse <?=$section_class['Tasks']?>">
	    <? echo $view->render('pm/PageFormAttribute.php', $attributes['Tasks']); ?>
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
			<? echo $view->render('pm/PageFormAttribute.php', $attribute); ?>
			<br/>
		</div>
	<?php }	?>


	<?php if ( $attributes['Attachment']['visible'] ) { ?>
	
	<div class="accordion-heading <?=$section_class['Trace']?>">
	  <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseFour" tabindex="-1">				
		<span class="caret"></span>
		<?=$attributes['Attachment']['name']?> 
	  </a>
	</div>
	<div id="collapseFour" class="accordion-body collapse <?=$section_class['Trace']?>">
    	<? echo $view->render('pm/PageFormAttribute.php', $attributes['Attachment']); ?>
    	<br/>
	</div>
	
	<?php } ?>

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
				    <? echo $view->render('pm/PageFormAttribute.php', $attribute); ?>
				</td>
			</tr>
		<?php } ?>
		</table>							
	</div>
	<?php }	?>
	
<?php if (!$formonly && $draw_sections) { ?>

	<div class="accordion-heading">
	  <a id="comments-section" class="to-drop-btn <?=($_COOKIE['devprom_request_form_section#collapseComments']=='0'?'collapsed':'')?>" data-toggle="collapse" href="#collapseComments" tabindex="-1">
		<span class="caret"></span>
		<?=text(1346)?>
		<?=($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
	  </a>
	</div>
	<div id="collapseComments" class="accordion-body <?=($_COOKIE['devprom_request_form_section#collapseComments']=='0'?'':'in')?> collapse" style="overflow: inherit;">
        <?php 
        	echo $view->render('core/PageSections.php', array(
        		'sections' => array_merge($bottom_sections, $sections),
        		'object_class' => $object_class,
        		'object_id' => $object_id,
				'placement' => 'bottom'
        	));
        ?>
	</div>

<?php } ?>

</div> <!-- end accordion -->

<script language="javascript">
	cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
	
	$(document).ready(function() {
		$(".accordion-heading > a")
			.slice(1).click(function() {
		      cookies.set('devprom.request.form.section' + $(this).attr('href'), 
		  	      	$(this).hasClass('collapsed') ? '1' : '0');
			});
		
		var locstr = String(window.location);
		
		if ( locstr.indexOf('#comment') > 0 && $('#comments-section').hasClass('collapsed') )
		{
			$("#comments-section").click();
		}
	});
</script>