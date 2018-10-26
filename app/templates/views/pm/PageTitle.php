<?php

$session = getSession();
if ( is_array($project_navigation_parms) ) {
    $project_navigation_parms['projectSortData'] = $projectSortData;
}
$project_it = $session->getProjectIt();
?>

<div class="btn-toolbar title-toolbar">
    <div class="btn-group navbar-company">
        <a id="navbar-project" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="icon-white icon-align-justify"></i>&nbsp;
            <?php
                if ( $project_navigation_parms['current_portfolio'] != $project_navigation_parms['current_project'] ) {
                    echo $project_navigation_parms['current_project_title'];
                }
                else {
                    echo $project_navigation_parms['current_portfolio_title'];
                }
                echo PHP_EOL . '<span class="caret"></span>';
            ?>
        </a>
        <?php
        if ( is_array($project_navigation_parms) ) {
            echo $view->render('core/PageTitleCompany.php', $project_navigation_parms);
        }
        ?>
    </div>

    <?php
    if ( is_array($quickMenu) && count($quickMenu['items']) > 0 ) {
    ?>
    <div class="btn-group quick-btn">
        <a id="<?=$quickMenu['id']?>" class="btn dropdown-toggle btn-sm <?=$quickMenu['button_class']?>" data-toggle="dropdown" href="#" title="<?=$quickMenu['description']?>">
            <i class="<?=$quickMenu['icon']?>"></i>
        </a>
        <?php
            echo $view->render('core/PopupMenu.php', array (
                'class' => $quickMenu['class'],
                'title' => $quickMenu['title'],
                'items' => $quickMenu['items']
            ));
        ?>
    </div>	<!-- end btn-group -->
    <?php
    }
    ?>

</div> <!-- end btn-toolbar -->

