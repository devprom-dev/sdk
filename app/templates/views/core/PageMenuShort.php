<?php
$index = 0;
?>
<div class="short-menu-popover">
    <? foreach( $areas as $key => $area ) { ?>
        <? if ( $key == 'stg2' ) continue; ?>
        <div class="short-menu-area">
            <p><?=$area['name']?></p>
            <? foreach( $area['menus'] as $menu ) { ?>
                <? if ( $menu['name'] != '' && !in_array($menu['uid'],array('plan','settings')) ) continue; ?>
                <? foreach( $menu['items'] as $item ) { ?>
                    <? if ( $item['uid'] == 'navigation-settings' ) continue; ?>
                    <? if ( $item['name'] == '' ) continue; ?>
                    <a href="<?=$item['url']?>"><?=$item['name']?></a>
                    <br/>
                <? } ?>
            <? } ?>
        </div>
        <? if ( ++$index % 3 == 0 ) { ?>
            <br/>
        <? } ?>
    <? } ?>
</div>
