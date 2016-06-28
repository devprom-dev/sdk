<?php if ( !is_array($sections) ) return; ?>

<?php foreach( $sections as $section ) { ?> 

<?php if ( $section instanceof ButtonInfoSection ) { ?>
<div class="btn-group pull-left">
	<a id="<?=$section->getId()?>" class="btn dropdown-toggle btn-small btn-info <?=($_COOKIE[$section->getId().'-'.$table_id] == 'true' ? 'active' : '')?>" href="#" data-toggle="dropdown" title="<?=$section->getCaption()?>">
   		<i class="<?=$section->getIcon()?> icon-white"></i>
	</a>
</div>
<?php continue; } ?>

<div class="btn-group pull-left last">
	<a class="btn dropdown-toggle btn-small btn-info" href="#" data-toggle="dropdown" title="<?=$section->getCaption()?>">
    	<i class="<?=$section->getIcon()?> icon-white"></i>
    	<span class="caret"></span>
	</a>
    	
	<ul class="dropdown-menu">
		<li>
			<div class="container-fluid">
				<div class="row-fluid">
                    <div id="<?=$section->getId()?>">
                        <img src="/images/ajax-loader.gif">
                    </div>
                </div>
            </div>
  	    </li>
   	</ul>
</div>

<?php } ?>


<script type="text/javascript">
	$(function() {
		<?php foreach ( $sections as $key => $section ) { ?>
			<?php if ( $section instanceof ButtonInfoSection ) continue; ?>
			<?php 
				$parms = $section->getParameters();
		
				if ( !array_key_exists('class', $parms) ) $parms['class'] = $object_class;
				if ( !array_key_exists('id', $parms) ) $parms['id'] = join(',',$iterator->idsToArray());
			?>
			
			$.ajax({
				url: '?export=section',
				type: 'GET',
				dataType: 'html',
				async: true,
				data: { 
					<?php foreach ( $parms as $parm_key => $parm_value ) { ?>
					'<?=$parm_key?>' : '<?=$parm_value?>',
					<?php } ?>
					'section': '<?=get_class($section)?>'
				},
				error: function( xhr ) { 
					reportAjaxError( xhr ); 
				},
				success: function( result ) {
		 			if ( (new RegExp('Internal Server Error')).exec( result ) != null )
	 				{
		 				window.location = '/500';
	 				}
					 
					$('#<?=$section->getId()?>').html(result);
	
					completeChartsUI($('#<?=$section->getId()?>'));
				}
			});				
		
		<?php } ?>
	});
</script>
