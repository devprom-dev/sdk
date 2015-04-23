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
	'Links' );

$fields_dont_skip_if_empty = array (
	'Watchers',
	'Deadlines',
	'Fact',
	'Tags'
);

$fields_dont_skip_if_hidden = array (
	'State'
);

$fields_to_be_skiped = array_merge( $linked_attrs, array (
	'Attachment',
	'Tasks',
	'Caption',
	'Description'
));

// attributes to be displayed in first column

$important_attributes = array( 
	'Type', 
	'Priority', 
	'Author',
    'ExternalAuthor',
	'Function', 
	'Estimation', 
	'Deadlines',
	'OrderNum',
    'PlannedRelease'
);

$columns = array();
$hidden_class = array();

$recent_column = 0;

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $important_attributes) ) continue;

	if ( !$attribute['visible'] && !in_array($name, $fields_dont_skip_if_hidden) ) continue;
	
	if ( $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty) ) continue;
	
	$columns[$recent_column][$name] = $attribute;
}

// attributes to be displayed in second column
$recent_column++;

foreach( $attributes as $name => $attribute ) 
{
	if ( in_array($name, array_keys($columns[0])) ) continue;
	if ( in_array($name, $linked_attrs) ) continue;
	
	if ( !$attribute['visible'] && !in_array($name, $fields_dont_skip_if_hidden) ) continue;
	if ( in_array($name, array('Attachment', 'Tasks', 'Caption', 'Description')) ) continue;

	if ( $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty) ) continue;
	
	$columns[$recent_column][$name] = $attribute;
}

// attributes to be displayed in third column

if ( $attributes['Tasks']['visible'] )
{
	$recent_column++;
	
	$columns[$recent_column]['Tasks'] = $attributes['Tasks'];
	
	$section_class['Tasks'] = 'hidden-tv';
	$section_class['Trace'] = 'hidden-desktop';
}
else
{
	$section_class['Trace'] = 'hidden-desktop';
}

// attributes to be displayed in fourth column
$recent_column++;

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $linked_attrs) && $name != 'Attachment' ) continue;
	if ( !$attribute['visible'] && !in_array($name, $fields_dont_skip_if_hidden) ) continue;
	
	$columns[$recent_column][$name] = $attribute;
}

// other attributes

$trace_attributes = array();

foreach( $attributes as $name => $attribute ) 
{
	if ( !in_array($name, $linked_attrs) ) continue;
	if ( !$attribute['visible'] && !in_array($name, $fields_dont_skip_if_hidden) ) continue;
	
	$trace_attributes[] = $attribute;
}

?>

<div class="actions">
	<div class="btn-group last">
		<a class="btn btn-small dropdown-toggle btn-inverse" href="#" data-toggle="dropdown">
			<?=translate('Действия')?>
			<span class="caret"></span>
		</a>
		<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
	</div>
</div> <!-- end actions -->

<ul class="breadcrumb">
    <?php if ( $navigation_url != '' ) { ?>
	<li><a href="<?=$navigation_url?>"><?=($navigation_title == '' ? $title : $navigation_title)?></a></li>
	<?php } ?>
	
	<?php if ( $uid_icon != '' ) { ?>
	<li>
	    <?php if ( $navigation_url != '' ) { ?> <span class="divider">/</span> <?php } ?>
	    <a href="#"><?=$uid_icon?></a>
	</li>
	<?php } ?>
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
		<div class="row" style="display:table;width:100%;">
			<?php foreach( $columns as $column_index => $column ) { ?>
		    <div class="properties-column-<?=count($columns).$column_index?>">
				<table class="properties-table">
				<?php foreach( $column as $ref_name => $attribute ) { ?>
					<tr name="<?=$ref_name?>">
						<th>										
							<?=$attribute['name']?>:
						</th>
						<td>
							<?
								if ( is_array($refs_actions[$ref_name]) )
								{
									echo $this->render('core/EmbeddedRowTitleMenu.php', array (
											'title' => IteratorBase::getHtmlValue($attribute['text']),
											'items' => array( $refs_actions[$ref_name] )
									));
								}
								else
								{
									echo $view->render('pm/PageFormAttribute.php', $attribute);
								}
							?>
						</td>
					</tr>	
				<?php } ?>
				</table>
			</div>
			<?php if ( $column_index < count($columns) - 1 ) { ?>
		    <div class="properties-column">&nbsp;</div>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	
	<!--  -->
	<div class="accordion-heading">
	  <a class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseTwo" tabindex="-1">
		<span class="caret"></span>
		<?=$attributes['Description']['name']?>
	  </a>
	</div>
	<div id="collapseTwo" class="accordion-body collapse">
		<? 
		if ( is_a($attributes['Description']['field'], 'FieldWYSIWYG') )
		{
		    $attributes['Description']['field']->draw();
		}
		else
		{ 
		    echo '<p>'.$attributes['Description']['text'].'</p>';
		}
		?>
		<br/>
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
	  <a id="comments-section" class="to-drop-btn collapsed" data-toggle="collapse" href="#collapseComments" tabindex="-1">
		<span class="caret"></span>
		<?=text(1346)?>
		<?=($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
	  </a>
	</div>
				
	<div id="collapseComments" class="accordion-body collapse">
        <?php 
        	echo $view->render('core/PageSections.php', array(
        		'sections' => array_merge($bottom_sections, $sections),
        		'object_class' => $object_class,
        		'object_id' => $object_id 
        	));
        ?>
	</div>

<?php } ?>

</div> <!-- end accordion -->

<script language="javascript">
	$.cookies.setOptions({expiresAt:new Date(new Date().getFullYear() + 1, 1, 1)});
	
	$(document).ready(function() {
		$(".accordion-heading > a")
			.slice(1).click(function() {
		      $.cookies.set('devprom.request.form.section' + $(this).attr('href'), 
		  	      	$(this).hasClass('collapsed') ? '1' : '0');
			});
		
		$(".accordion-heading > a")
			.slice(1).each(function( index ) {
				if($.cookies.get('devprom.request.form.section' + $(this).attr('href')) == '1') {
					if ( $(this).hasClass('collapsed') ) $(this).click();
				}
			});

		var locstr = new String(window.location);
		
		if ( locstr.indexOf('#comment') > 0 && $('#comments-section').hasClass('collapsed') )
		{
			$("#comments-section").click();
		}
	});
</script>