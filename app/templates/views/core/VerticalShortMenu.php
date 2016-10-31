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

        $items[$item_key]['count']++;
    }
}
?>

<ul class="menu vertical-menu-short" id="menu_<?=$area_id?>">
    <?php
        foreach ( $items as $item_key => $item ) {
            if ( $item['name'] == '' && count($item['items']) > 0 ) {
                foreach( $item['items'] as $child_key => $child )
                {
                    if ( $child['name'] == '' ) continue;
                    $child['name'] = str_replace(' ', '&nbsp;', $child['name']);
                    $child['icon'] = $child['icon'] == '' ? 'icon-align-justify' : $child['icon'];
                    ?>
                    <li id="<?=$item_key.'-'.$child_key?>" class="<?='root '.$child['state']?>">
                        <a class="btn btn-link" uid="<?=$child['uid']?>" module="<?=$child['module']?>" href="<?=$child['url']?>" title="<?=$child['name']?>"><i class="icon-white <?=$child['icon']?>"></i></a>
                    </li>
                    <?php
                }
            }
            else {
                if ( $item['count'] < 1 ) continue;
                if ( count($item['items']) < 1 ) continue;

                $dataContent = '<ul>';
                foreach( $item['items'] as $child_key => $child )
                {
                    if ( $child['name'] == '' ) continue;
                    $dataContent .= '<li><a href="'.$child['url'].'">'.$child['name'].'</a></li>';
                }
                $dataContent .= '</ul>'

	            ?>
                <li id="menu-folder-<?=$item['uid']?>" class="root <?=$item['state']?>">
                    <a class="btn btn-link" id="menu-group-<?=$item['uid']?>" href="javascript:void(0)" title="<?=trim($item['name'],'.')?>" data-content="<?=htmlentities($dataContent)?>">
                        <i class="icon-white icon-folder-close"></i>
                    </a>
                </li>
	            <?php
            }
        }
        if ( is_array($setup_menu_item) ) {
            ?>
            <li id="setup" class="setup <?=$setup_menu_item['state']?>">
                <a class="btn btn-link" module="" href="<?=$setup_menu_item['url']?>" style="padding-left:10px;" title="<?=str_replace(' ','&nbsp;',$setup_menu_item['name'])?>">
                    <i class="icon-wrench icon-white" ></i>
                </a>
            </li>
            <li class="setup">
                <a class="btn btn-link" module="" onclick="switchMenuState('normal');" style="padding-left:10px;" title="<?=str_replace(' ','&nbsp;',text(2192))?>">
                    <i class="icon-arrow-right icon-white" ></i>
                </a>
            </li>
	        <?php
        }
    ?>
</ul>
<script type="text/javascript">
    $(document).ready(function() {
        $('#quick-search').popover({
            placement: 'bottom',
            container: 'body',
            html: true,
            trigger: 'focus'
        });
        $('.vertical-menu-short a.btn[module]').tooltip({
            placement: 'right'
        });
        $('.vertical-menu-short a.btn:not([module])').popover({
            placement: 'right',
            container: 'body',
            html: true
        });
        $('body, .content-internal')
            .on('click.dropdown.data-api', function(e) {
                $('.vertical-menu-short a.btn:not([module])').each(function() {
                    if ( !$(this).is($(e.target).closest('a')) ) $(this).popover('hide');
                });
            });
    });
</script>