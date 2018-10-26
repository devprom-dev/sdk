<?php
$index = 0;
$recent = 0;
?>
<div class="short-menu-popover">
    <? foreach( $areas as $key => $area ) { ?>
        <? if ( $key == 'stg2' ) continue; ?>
        <div class="short-menu-area">
            <p><?=$area['name']?></p>
            <? foreach( $area['menus'] as $menu ) { ?>
                <? if ( $menu['name'] != '' && !in_array($menu['uid'],array('plan')) ) continue; ?>
                <? foreach( $menu['items'] as $item ) { ?>
                    <? if ( $item['uid'] == 'navigation-settings' ) continue; ?>
                    <? if ( $item['name'] == '' ) continue; ?>
                    <a uid="vms-<?=$item['uid']?>" module="vms-<?=$item['module']?>" href="<?=$item['url']?>"><?=$item['name']?></a>
                    <br/>
                <? } ?>
            <? } ?>
        </div>
        <? $recent++; ?>
        <? if ( ++$index % 4 == 0 ) { $recent = 0; ?>
            <br/>
        <? } ?>
    <? } ?>
    <? for( $i = 0; $index > 4 && $i < 4 - $recent; $i++ ) { ?>
        <div class="short-menu-area">&nbsp;</div>
    <? } ?>
</div>
