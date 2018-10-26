<?php if ( !is_array($sections) ) return; ?>

<?php foreach( $sections as $section ) { ?> 

<?php if ( $section instanceof ButtonInfoSection ) { ?>
	<div class="btn-group pull-left info-action">
		<? $btnState = $_COOKIE[$section->getId().'-'.$table_id]; ?>
		<? $btnState = $btnState == '' ? $section->IsActive() : $btnState == 'true'; ?>
		<a id="<?=$section->getId()?>" class="btn dropdown-toggle btn-sm btn-info <?=($btnState ? 'active' : '')?>" href="#" data-toggle="dropdown" title="<?=$section->getCaption()?>">
			<i class="<?=$section->getIcon()?> icon-white"></i>
		</a>
	</div>
<?php continue; } ?>

<div class="btn-group pull-left info-action last">
	<a class="btn dropdown-toggle btn-sm btn-info" href="#" data-toggle="dropdown" title="<?=$section->getCaption()?>">
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

