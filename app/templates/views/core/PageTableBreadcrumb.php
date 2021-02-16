<ul class="breadcrumb hidden-print">
    <?php
    if ( $navigation_url != '' && $nearest_title != '' ) {
        echo '<li><a href="'.$navigation_url.'">'.$nearest_title.'</a><span class="divider">/</span></li>';
    }
    ?>
    <li class="page-title">
        <div class="btn-group">
            <div class="btn transparent-btn">
                <span class="title"><?=$title?></span>
            </div>
        </div>
    </li>
    <?php
    $family = $filter_actions['modules'];
    if ( is_array($family) ) {
        echo '<li>';
        echo $view->render('core/TextMenu.php',
            array(
                'title' => $family['name'],
                'items' => $family['items']
            )
        );
        echo '</li>';
    }

    $charts = $filter_actions['charts'];
    if ( is_array($charts) ) {
        echo '<li>';
        echo $view->render('core/TextMenu.php',
            array(
                'title' => $charts['name'],
                'items' => $charts['items']
            )
        );
        echo '</li>';
    }

    $openProjects = $filter_actions['projects'];
    if ( is_array($openProjects) && count($openProjects['items']) > 1 ) {
        echo '<li>';
        echo $view->render('core/TextMenu.php',
            array(
                'title' => $openProjects['name'],
                'items' => $openProjects['items']
            )
        );
        echo '</li>';
    }

    $moduleActions = $filter_actions['actions'];
    if ( count($moduleActions) > 0 ) {
        echo '<li>';
        ?>

        <div class="btn-group">
            <a class="btn btn-cell dropdown-toggle transparent-btn" uid="widget-more-actions" href="" data-toggle="dropdown">
                <span class="label">...</span>
            </a>
            <? echo $view->render('core/PopupMenu.php', array('items' => $moduleActions, 'uid' => 'widget-more-actions')); ?>
        </div>

        <?php
        echo '</li>';
    }
    ?>
</ul> <!-- end breadcrumb -->
