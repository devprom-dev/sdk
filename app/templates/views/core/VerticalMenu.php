<?php

foreach ( $items as $item_key => $item )
{
    foreach( $item['items'] as $child_key => $child ) 
    { 
        if ( $child['url'] == '' || $child['name'] == '' ) continue;

        if ( in_array($child['uid'], array('navigation-settings','charts')) ) {
            unset($items[$item_key]['items'][$child_key]);
            continue;
        }

        $child_url = str_replace($application_url, '', array_shift(preg_split('/\?/', $child['url'])));

        if ( $child_url == $active_url ) {
            $items[$item_key]['state'] = 'open';
            $items[$item_key]['items'][$child_key]['state'] = 'active expanded';
        }

        $items[$item_key]['count']++;
    }
}

?>

<ul class="menu vertical-menu" id="menu_<?=$area_id?>" style="display:<?=($area_id == $active_area_uid ? 'block': 'none')?>;">
    <?php foreach ( $items as $item_key => $item ) { ?>
    
    <?php if ( $item['name'] == '' && count($item['items']) > 0 ) { ?>
    	<?php 
        foreach( $item['items'] as $child_key => $child ) 
        { 
            if ( $child['name'] == '' ) continue;
            ?>
            
    	    <li id="<?=$item_key.'-'.$child_key?>" class="<?='root '.$child['state']?>">
			    <a uid="<?=$child['uid']?>" module="<?=$child['module']?>" href="<?=$child['url']?>"><?=$child['name']?></a>
    		</li>
    
    	<?php } ?>
	
	<?php } else { ?>
	
	<?php if ( $item['count'] < 1 ) continue; ?>
	<?php if ( count($item['items']) < 1 ) continue; ?>
	
	<li id="menu-folder-<?=$item['uid']?>" class="submenu <?=$item['state']?>">
		<a id="menu-group-<?=$item['uid']?>" href="javascript:void(0)" class="head">
		    <span class="head-caret"></span> <?=trim($item['name'],'.')?>  
		</a>
		<ul style="display:<?=($item['state'] == 'open' ? 'display' : 'none')?>;">
	    <?php 
	    foreach( $item['items'] as $child_key => $child ) 
        { 
            if ( $child['name'] == '' ) continue;
            ?>
            
		    <li id="<?=$item_key.'-'.$child_key?>" class="closed <?=$child['state']?>">
				<a uid="<?=$child['uid']?>" module="<?=$child['module']?>" href="<?=$child['url']?>"><?=$child['name']?></a>
			</li>

		<?php } ?>
		</ul>
	</li>
	<?php } ?>
	<?php } ?>
</ul>

