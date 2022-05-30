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
        
        $items[$item_key]['count']++;
    }
}
?>

<ul class="menu vertical-menu-short" id="menu_<?=$area_id?>">
    <?php
        foreach ( $items as $item_key => $item ) {
            if ( $item['name'] == '' && count($item['items']) > 0 || count($item['items']) == 1 ) {
                foreach( $item['items'] as $child_key => $child )
                {
                    if ( $child['name'] == '' ) continue;
                    $child['name'] = str_replace(' ', '&nbsp;', $child['name']);
                    $child['icon'] = $child['icon'] == '' ? 'icon-align-justify' : $child['icon'];
                    ?>
                    <li id="<?=$item_key.'-'.$child_key?>" class="<?='root '.$child['state']?>">
                        <a class="" uid="<?=$child['uid']?>" module="<?=$child['module']?>" href="<?=$child['url']?>" title="<?=$child['name']?>"><i class="icon-white <?=$child['icon']?>"></i></a>
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
                    <a class="" id="menu-group-<?=$item['uid']?>" href="javascript:void(0)" title="<?=trim($item['name'],'.')?>" data-content="<?=htmlentities($dataContent)?>">
                        <i class="icon-white icon-folder-close"></i>
                    </a>
                </li>
	            <?php
            }
        }
        if ( $settings_menu['url'] != '' ) {
        ?>
        <li id="settings" class="setup">
            <a class="" uid="settings-4-project" module="" href="<?=$settings_menu['url']?>" title="<?=str_replace(' ','&nbsp;',$settings_menu['name'])?>">
                <i class="icon-cog icon-white" ></i>
            </a>
        </li>
        <?php
        }
        if ( $adjust_menu['url'] != '' ) {
        ?>
        <li id="setup" class="setup">
            <a class="" module="" href="<?=$adjust_menu['url']?>" title="<?=str_replace(' ','&nbsp;',$adjust_menu['name'])?>">
                <i class="icon-wrench icon-white" ></i>
            </a>
        </li>
        <?php
        }
        ?>
        <li id="setup-vmenu" class="setup">
            <a class="" module="" onclick="switchMenuState();" title="<?=str_replace(' ','&nbsp;',text(2192))?>">
                <i class="icon-arrow-right icon-white" ></i>
            </a>
        </li>
</ul>
