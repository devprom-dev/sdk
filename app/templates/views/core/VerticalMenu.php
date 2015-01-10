<?php

$parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);

$active_url = $parts[0];

foreach ( $items as $item_key => $item )
{
    foreach( $item['items'] as $child_key => $child ) 
    { 
        if ( $child['url'] == '' || $child['name'] == '' ) continue;
        
        $parts = preg_split('/\?/', $child['url']);

        $child_url = str_replace($application_url, '', $parts[0]);
        
        if ( $child_url == $active_url )
        {
            $items[$item_key]['state'] = 'open';
            $items[$item_key]['items'][$child_key]['state'] = 'active expanded';
        } 
        
        if ( $child['uid'] == 'navigation-settings' )
        {
            $setup_menu_item = $child; 
            
            unset($items[$item_key]['items'][$child_key]);
        }

        $items[$item_key]['count']++;
    }
}

$session = getSession();

if ( is_a($session, 'PMSession') ) 
{
    $project_it = $session->getProjectIt();
    
    $search_url = $application_url.'/pm/'.$project_it->get('CodeName').'/search.php';
}

?>

<ul class="menu vertical-menu" id="menu_<?=$area_id?>" style="margin:0;width:170px;display:<?=($area_id == $active_area_uid ? 'block': 'none')?>;">
    <?php if ( $search_url != '' ) { ?>
    <li class="submenu open <?=( !is_array($items[0]['items']) ? 'search' : '')?>">
        <form class="form-search" action="<?=$search_url?>" style="padding-left:10px;padding-top:16px;padding-bottom:10px;" action="<?=$base_url?>search.php">
            <div class="input-append">
              <input name="quick" type="text" class="search-query" style="width:80px;" placeholder="<?=translate('Поиск')?>">
              <button type="submit" class="btn medium-blue"><i class="icon-search"></i></button>
            </div>
        </form>
    </li>
    <?php } ?>
    
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
	
	<?php if ( is_array($setup_menu_item) ) { ?>
    	    
    <li id="setup" class="setup <?=$setup_menu_item['state']?>">
        <a href="<?=$setup_menu_item['url']?>" style="color:white;padding:20px 0 20px 10px;">
            <span style="display:table-cell;"><i class="icon-wrench icon-white" ></i></span>
            <span style="display:table-cell;padding:0 15px 0 10px;"><?=$setup_menu_item['name']?></span>
        </a>
	</li>
	
	<?php } ?>
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

    function adjustContainerHeight(menu)
    {
    	if ( $('.content-internal').height() < menu.height() )
    	{
            $('.content-internal').css('min-height',menu.height());
    	}
    }
</script>
