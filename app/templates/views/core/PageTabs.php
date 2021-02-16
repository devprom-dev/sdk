<ul class="modules nav">
    <li></li>

<?php foreach( $pages as $key => $page ) { ?>
    <?php

    if ( $page['type'] == 'hidden' ) continue;
    if ( $key == 'stg' ) {
        unset($pages[$key]);
        continue;
    }

    if ( $page['icon'] == '' ) {
        switch( $key ) {
            case 'favs':
                $page['icon'] = 'icon-favorites';
                break;
            case 'reqs':
                $page['icon'] = 'icon-reqs';
                break;
            case 'docs':
                $page['icon'] = 'icon-docs';
                break;
            case 'dev':
                $page['icon'] = 'icon-code';
                break;
            case 'qa':
                $page['icon'] = 'icon-testing';
                break;
            case 'mgmt':
                $page['icon'] = 'icon-management';
                break;
        }
    }

    ?>

    <li id="tab_<?=$page['uid']?>" area="<?=$page['uid']?>" class="dropdown <?=($page['uid'] == $active_area_uid ? 'active open' : '')?>" style="<?=$style?>">
        <a data-toggle="dropdown" class="dropdown-toggle transparent-btn" href="<?=$page['url']?>" title="<?=trim($page['name'], '.')?>">
            <?php if ( $page['icon'] != '' ) { ?> <i class="<?=$page['icon']?> icon-sidebar"></i> <?php } ?>
        </a>
        <?php

        if ( !is_array($page['items']) ) continue;

        echo $view->render('core/PopupMenu.php', array (
            'class' => "menuitem_popup ".($page['active'] ? 'active' : ''),
            'title' => trim($page['name'], '.'),
            'items' => $page['items'],
            'url' => $page['url']
        ));

        ?>
    </li>

    <?php
    }

    if ( $settings_menu['url'] != '' ) {
        ?>
        <li id="settings" class="setup">
            <a class="btn btn-link" uid="settings-4-project" module="" href="<?=$settings_menu['url']?>" title="<?=$settings_menu['name']?>">
                <span style="display:table-cell;"><i class="icon-cog icon-white"></i></span>
            </a>
        </li>
        <?php
    }
    if ( $adjust_menu['url'] != '' ) {
        ?>
        <li id="setup" class="setup">
            <a href="<?= $adjust_menu['url'] ?>" title="<?= $adjust_menu['name'] ?>">
                <span style="display:table-cell;"><i class="icon-wrench icon-white"></i></span>
            </a>
        </li>
        <?php
        }
    ?>
        <li id="minimize" class="setup">
            <a onclick="switchMenuState('minimized');" title="<?= text(2193) ?>">
                <span style="display:table-cell;"><i class="icon-arrow-left icon-white"></i></span>
            </a>
        </li>
</ul>
