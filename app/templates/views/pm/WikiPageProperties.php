<?php 

$left_hand_fields = array( 
	'Attachments', 
    'Author',
	'PageType',
);

$fields_to_be_skiped = array_merge( $trace_attributes, array (
	'Caption',
	'Content',
	'TransitionComment',
	'UID'
));

$fields_dont_skip_if_empty = array (
	'Attachments',
	'Watchers',
	'Tags'
);

$properties = array();

foreach( $attributes as $name => $attribute ) 
{
	$skip_field = !in_array($name, $left_hand_fields) 
		|| in_array($name, $fields_to_be_skiped)
		|| $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty);

	if ( $skip_field ) continue;
	
	$properties[$name] = $attribute;
}

$properties_on_right = array();

$trace_properties = array();

foreach( $attributes as $name => $attribute ) 
{
	if ( $attribute['visible'] && in_array($name, $trace_attributes) )
	{
		$trace_properties[] = $name;
	}
	
	$skip_field = in_array($name, $left_hand_fields) 
    	|| in_array($name, $fields_to_be_skiped)
    	|| !$attribute['visible'] 
    	|| $attribute['value'] == '' && !in_array($name, $fields_dont_skip_if_empty);
    			
	if ( $skip_field ) continue;
	
	$properties_on_right[] = $attribute;
} 
						    			
$buttons = array();

?>

<div class="container-fluid">
	<div class="row-fluid">
	
	    <?php if ( count($properties) > 0 || count($properties_on_right) > 0 ) { ?>
	    
		<table class="table">
			<tbody>
				<tr>
					<td style="border-top: none;">
		    			<table class="properties-table">
		    			
		    			<?php foreach( $properties as $name => $attribute ) { ?>
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
		    		</td>
		    		<td style="border-top: none;">
		    			<table class="properties-table">
		    
		    			<?php foreach( $properties_on_right as $name => $attribute ) { ?>
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
		    		</td>
				</tr>
			</tbody>
		</table>
			
		<?php } // count($properties) ?>
		
		<?php if ( count($trace_properties) > 0 ) { ?>
		
		<table class="table">
			<thead>
				<tr>
					<th><?=text(1243)?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<table class="properties-table properties-table-trace">
							<?php foreach( $trace_properties as $ref_name ) { ?>
								<?php $attribute = $attributes[$ref_name]; ?>
								<tr>
									<th attribute="<?=strtolower($ref_name)?>" title="<?=$attribute['description']?>">										
										<?=$attribute['name']?>: 
									</th>
									<td>
										<div class="controls">
						                <? echo $view->render('pm/PageFormAttribute.php', $attribute); ?>
										</div>
					                </td>
								</tr>
							<?php } ?>
						</table>							
					</td>
				</tr>
			</tbody>
		</table>
			
		<?php } // if ( count($trace_properties) > 0 ) ?>
	</div>
</div>
