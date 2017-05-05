<?php

foreach ( $items as $item_key => $item )
{
    foreach( $item['items'] as $child_key => $child ) 
    { 
        if ( $child['url'] == '' || $child['name'] == '' ) continue;
        
        $child_url = str_replace($application_url, '', array_shift(preg_split('/\?/', $child['url'])));

        if ( $child_url == $active_url ) {
            $items[$item_key]['state'] = 'open';
            $items[$item_key]['items'][$child_key]['state'] = 'active expanded';
        } 
        
        if ( $child['uid'] == 'navigation-settings' ) {
            $setup_menu_item = $child; 
            unset($items[$item_key]['items'][$child_key]);
        }
        if ( $child['uid'] == 'charts' ) {
            $charts_menu_item = $child;
            unset($items[$item_key]['items'][$child_key]);
        }

        $items[$item_key]['count']++;
    }
}

?>

<ul class="menu vertical-menu" id="menu_<?=$area_id?>" style="display:<?=($area_id == $active_area_uid ? 'block': 'none')?>;">
    <div style="margin-top: 11px;""></div>

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
    <li class="setup" style="padding-top: 10px;"></li>
	<?php if ( is_array($charts_menu_item) ) {  $collapse = true; ?>
        <li class="setup <?=$charts_menu_item['state']?>">
            <a href="<?=$charts_menu_item['url']?>" style="color:white;padding:10px 0 10px 10px;">
                <span style="display:table-cell;"><i class="icon-signal icon-white" ></i></span>
                <span style="display:table-cell;padding:0 15px 0 10px;"><?=$charts_menu_item['name']?></span>
            </a>
        </li>
	<?php } ?>
    <?php if ( is_array($setup_menu_item) ) { $collapse = true; ?>
        <li id="setup" class="setup <?=$setup_menu_item['state']?>">
            <a href="<?=$setup_menu_item['url']?>" style="color:white;padding:10px 0 10px 10px;">
                <span style="display:table-cell;"><i class="icon-wrench icon-white" ></i></span>
                <span style="display:table-cell;padding:0 15px 0 10px;"><?=$setup_menu_item['name']?></span>
            </a>
        </li>
    <?php } ?>
    <? if ( $collapse ) { ?>
    <li class="setup">
        <a onclick="switchMenuState('minimized');" style="color:white;padding:10px 0 10px 10px;">
            <span style="display:table-cell;"><i class="icon-arrow-left icon-white" ></i></span>
            <span style="display:table-cell;padding:0 15px 0 10px;"><?=text(2193)?></span>
        </a>
    </li>
    <? } ?>
</ul>

<script type="text/javascript">
    $("#menu_<?=$area_id?>").find('>li>a').on('click', function(e) {
    	e.stopImmediatePropagation();
        var menu = $(this).parent();
        
        if ( menu.hasClass('closed') ) {
        	menu.removeClass('closed');
            menu.addClass('open');
        	$(this).parent().find('>ul').show();

        	adjustContainerHeight(menu.parent());
        	return;
        }
        if ( menu.hasClass('open') ) {
        	menu.addClass('closed');
        	menu.removeClass('open');
        	$(this).parent().find('>ul').hide();
        	return;
        }
        $(this).parent().find('>ul').show();
        menu.addClass('open');
        adjustContainerHeight(menu.parent());
    });
</script>
