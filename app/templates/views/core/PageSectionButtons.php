<?php if ( !is_array($sections) ) return; ?>

<?php foreach( $sections as $section ) { ?> 

<?php if ( is_a($section, 'FullScreenSection') ) { ?>

<div class="btn-group pull-left">
	<a id="toggle-fullscreen" class="btn dropdown-toggle btn-small btn-info" href="#" data-toggle="dropdown" title="<?=$section->getCaption()?>">
   		<i class="icon-fullscreen icon-white"></i>
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
			<?php if ( is_a($section, 'FullScreenSection') ) continue; ?>
			<?php 
				$parms = $section->getParameters();
		
				if ( !array_key_exists('class', $parms) ) $parms['class'] = $object_class;
				if ( !array_key_exists('id', $parms) ) $parms['id'] = join(',',$iterator->idsToArray());
			?>
			
			$.ajax({
				url: '?export=section',
				type: 'POST',
				dataType: 'html',
				async: <?=($section->getAsyncLoad() ? 'true' : 'false' )?>,
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
	
					completeChartsUI(); 
				}
			});				
		
		<?php } ?>
	});
</script>
