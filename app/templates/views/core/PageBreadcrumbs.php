<ul class="breadcrumb hidden-print" style="margin-right: 0px;">
    <?php
    if ( $navigation_url != '' ) {
        if ( $parent_widget_url != '' ) {
            echo '<li><a href="'.$parent_widget_url.'">'.$parent_widget_title.'</a><span class="divider">/</span></li>';
        }
        if ( $nearest_title != '' ) {
            echo '<li><a href="'.$navigation_url.'">'.$nearest_title.'</a><span class="divider">/</span></li>';
        }
    }
    else if ( $has_caption ) {
        echo '<li>'.$caption.'<span class="divider">/</span></li>';
    }

    if ( $uid != '' ) {
        echo '<li>'.$view->render('core/Clipboard.php', array ('url' => $uid_url, 'uid' => $uid)).'</li>';

        if ( $state_name != '' ) {
            echo '<li class="clip" style="margin-left:8px;">'.$view->render('pm/StateColumn.php', array (
                    'stateIt' => $form->getObjectIt()->getStateIt(),
                    'id' => 'state-label',
                    'listWidgetIt' => $listWidgetIt
                )).'</li>';
        }
    }
    else {
        ?>
        <li class="page-title">
            <div class="btn-group">
                <div class="btn transparent-btn">
                    <span class="title"><?=$title?></span>
                </div>
            </div>
        </li>
        <?
    }
    ?>
</ul> <!-- end breadcrumb -->
