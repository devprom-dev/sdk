<?php

$session = getSession();

$project_it = $session->getProjectIt();

?>

<div class="btn-toolbar title-toolbar">
    <div class="btn-group navbar-company">
        <a id="navbar-company-name" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="icon-th-list icon-white"></i>&nbsp;
            <?=$company_name?>
            <span class="caret"></span>
        </a>
        <?php
        if ( is_array($project_navigation_parms) ) {
            echo $view->render('core/PageTitleCompany.php', $project_navigation_parms);
        }
        ?>
    </div>
    <div class="btn-group">
    </div>
    <?php $subprojects_count = count($project_navigation_parms['projects'][$project_navigation_parms['current_portfolio']]); ?>
    <?php if( is_array($project_navigation_parms) ) { ?>
        <?
        if ( $project_navigation_parms['current_portfolio'] == '' ) { ?>
            <div class="btn-group">
                <a id="navbar-project" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
                    <?=$project_navigation_parms['current_project_title']?>
                    <span class="caret"></span>
                </a>
                <? echo $view->render('core/PageTitleProjects.php', $project_navigation_parms); ?>
            </div>
        <?
        }
        else if ( $project_navigation_parms['current_project'] == $project_navigation_parms['current_portfolio'] ) { ?>
            <?php $hasActions = count($project_navigation_parms['portfolio_actions']) > 0 || count($project_navigation_parms['program_actions']) > 0; ?>
            <div class="btn-group">
                <a id="navbar-portfolio" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
                    <?=$project_navigation_parms['current_portfolio_title']?>
                    <?php if ( $hasActions ) { ?>
                    <span class="caret"></span>
                    <?php } ?>
                </a>

                <?php if ( $hasActions ) { echo $view->render('core/PageTitlePortfolios.php', $project_navigation_parms); } ?>
            </div>

        <? } else { ?>

            <div class="btn-group">
                <a class="btn btn-link btn-navbar" href="/pm/<?=$project_navigation_parms['current_portfolio']?>">
                    <?=$project_navigation_parms['current_portfolio_title']?>
                </a>
            </div>

            <div class="btn-group">
            </div>

            <div class="btn-group">
                <a id="navbar-project" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
                    <?=$project_navigation_parms['current_project_title']?>
                    <span class="caret"></span>
                </a>
                <?php echo $view->render('core/PageTitleProjects.php', $project_navigation_parms); ?>
            </div>
        <?php } ?>

    <?php } ?>

    <?php if ( $project_it->IsPortfolio() && in_array($project_it->get('LinkedProject'), array('','0')) && getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) ) { ?>

        <a id="navbar-create-project" class="btn btn-warning" href="/projects/new">
            <i class="icon-plus icon-white"></i> <?=text('project.new')?>
        </a>

    <?php } ?>

    <?php if ( is_array($quickMenu) ) { ?>
    <div class="btn-group quick-btn">
        <a id="<?=$quickMenu['id']?>" class="btn dropdown-toggle btn-small <?=$quickMenu['button_class']?>" data-toggle="dropdown" href="#" title="<?=$quickMenu['description']?>">
            <i class="<?=$quickMenu['icon']?>"></i>
            <span class="caret"></span>
        </a>
        <?php
            echo $view->render('core/PopupMenu.php', array (
                'class' => $quickMenu['class'],
                'title' => $quickMenu['title'],
                'items' => $quickMenu['items']
            ));
        ?>
    </div>	<!-- end btn-group -->
    <?php } ?>

</div> <!-- end btn-toolbar -->

