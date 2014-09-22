<?php if ( count($sections) > 0 ) { ?>

<div class="tabs-block">
	<ul class="nav nav-tabs" id="rightTab">
		<?php foreach ( $sections as $key => $section ) { ?>
		
		<li class="<?=($key == array_shift(array_keys($sections)) ? 'active' : '')?>"><a href="#<?=$section->getId().$object_id?>"><?=$section->getCaption()?></a></li>
		
		<?php } ?>
	</ul>

	<div class="tab-content right-side-tab">
		<?php foreach ( $sections as $key => $section ) { ?>
		
		<div class="tab-pane <?=($key == array_shift(array_keys($sections)) ? 'active' : '')?> <?=$section->getId()?>" id="<?=$section->getId().$object_id?>">

		<?php 

        $section->render( $this, array() );
		
		?>
		
		</div>
		
		<?php } ?>
	</div>
</div>

<?php } ?>
