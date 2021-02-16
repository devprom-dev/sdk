<?
$has_portfolio_programs = count($portfolios) + count($programs) > 0;
foreach( $programs as $program_id => $item ) {
    unset($projects[$program_id][$program_id]);
}
foreach( $portfolios as $portfolio_id => $item ) {
    unset($projects[$portfolio_id][$portfolio_id]);
}
$projectsData = array();

foreach( $projects as $groupId => $containedProjects ) {
    foreach( $containedProjects as $projectId => $project ) {
        $data = array (
            'groupId' => $groupId,
            'projectId' => $projectId,
            'project' => $project
        );
        $projectsData[$projectId] = $data;
    }
}

$searchMode = !defined('SKIP_PRODUCT_TOUR') && count($projectsData) > 17;
if ( $searchMode ) {
    usort( $projectsData, function($left, $right) use($projectSortData) {
        return $projectSortData[$left['projectId']] > $projectSortData[$right['projectId']];
    });
    $recent = array_slice($projectsData, 0, 6);
}
?>

<ul class="dropdown-menu navbar-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span12">
                <table class="table <?($has_portfolio_programs ? "two-columns" : "")?>">
                    <thead>
                        <tr>
                            <th colspan="<?=($has_portfolio_programs ? 2 : 1)?>" style="padding-right: 0;">
                                <span class="pull-left" style="margin-top: 3px;">
                                    <?=text('projects.name')?>
                                </span>
                                <? if ( $searchMode ) { ?>
                                <span class="pull-right project-search">
                                    <input class="" type="text" placeholder="<?=text(2304)?>">
                                </span>
                                <? } ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="<?=($has_portfolio_programs ? 2 : 1)?>" style="padding-top: 16px; padding-bottom: 16px;">
                                <?php
                                if ( $searchMode ) {
                                    echo '<div class="p-left-recent">';
                                        foreach ($recent as $info) {
                                            $groupId = $info['groupId'];
                                            $project = $info['project'];
                                            if (is_array($programs[$groupId])) {
                                                $item = $programs[$groupId];
                                            }
                                            if (is_array($portfolios[$groupId])) {
                                                $item = $portfolios[$groupId];
                                            }
                                            echo '<div class="p-link p-recent">';
                                                echo '<a href="'.$project['url'].'">'.$project['name'].'</a>';
                                            echo '</div>';
                                        }
                                    echo '</div>';
                                    echo '<div class="p-right-recent">';
                                        foreach( array('all', 'my', 0) as $portfolioKey ) {
                                            if ( is_array($portfolios[$portfolioKey]) ) {
                                                echo '<div class="p-link p-node p-recent">';
                                                    echo '<i class="icon-briefcase"></i>';
                                                    echo '<a href="'.$portfolios[$portfolioKey]['url'].'">'.$portfolios[$portfolioKey]['name'].'</a>';
                                                echo '</div>';
                                            }
                                        }
                                    echo '</div>';
                                    echo '<div class="p-link p-node" style="clear:both;">';
                                        echo '<div style="text-align:center;color:silver;">&bull; &nbsp; &bull; &nbsp; &bull;</div>';
                                    echo '</div>';
                                }
                                ?>

                                <div class="p-total-list">
                                <?php if ( count($programs) > 0 ) {  ?>
                                    <?php foreach( $programs as $program_id => $program ) { ?>
                                        <div class="p-link p-node">
                                            <i class="icon-folder-close"></i>
                                            <a href="<?=$program['url']?>"><?=$program['name']?></a>
                                        </div>

                                        <?php if ( !is_array($projects[$program_id]) ) continue; ?>
                                        <?php foreach( $projects[$program_id] as $project_id => $project ) { ?>
                                            <div class="p-link <?=($has_portfolio_programs ? "p-sub" : "")?>">
                                                <a href="<?=$project['url']?>"><?=$project['name']?></a>
                                            </div>
                                        <?php } ?>
                                        <div class="p-link">
                                            <br/>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <?php foreach( $portfolios as $portfolio_id => $program ) { ?>
                                    <div class="p-link p-node">
                                        <i class="icon-briefcase"></i>
                                        <a href="<?=$program['url']?>"><?=$program['name']?></a>
                                    </div>

                                    <?php if ( count($projects[$portfolio_id]) < 1 ) continue; ?>
                                    <?php foreach( $projects[$portfolio_id] as $project_id => $project ) { ?>
                                        <div class="p-link <?=($has_portfolio_programs ? "p-sub" : "")?>">
                                            <a href="<?=$project['url']?>"><?=$project['name']?></a>
                                        </div>
                                    <?php } ?>
                                    <div class="p-link">
                                        <br/>
                                    </div>
                                <?php } ?>

                                <?php if (is_array($projects['']) ) foreach( $projects[''] as $project_id => $project ) { ?>
                                    <div class="p-link <?=($has_portfolio_programs ? "p-sub" : "")?>">
                                        <a href="<?=$project['url']?>"><?=$project['name']?></a><br/>
                                    </div>
                                <?php }

                                if ( $searchMode ) {
                                    echo '<div style="padding:9px 0 9px 0;">';
                                        echo '<a id="project-list-all" class="dashed embedded-add-button">'.text(2307).'</a>';
                                    echo '</div>';
                                }

                                ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <? if ( !$has_portfolio_programs ) { ?>
                            <td>
                                <?php foreach ( $admin_actions as $action ) { ?>
                                    <i class="<?=$action['icon']?>"></i> <a href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                <?php } ?>
                                <?php foreach ( $company_actions as $action ) { ?>
                                    <i class="<?=$action['icon']?>"></i> <a href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                <?php } ?>
                            </td>
                            <? } else { ?>
                                <td width="50%">
                                    <?php foreach ( $admin_actions as $action ) { ?>
                                        <i class="<?=$action['icon']?>"></i> <a href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php foreach ( $company_actions as $action ) { ?>
                                        <i class="<?=$action['icon']?>"></i> <a href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                   </tbody>
                </table>

                <? if ( count($settings_actions) > 0 ) { ?>
                <?
                    if ( $has_portfolio_programs ) {
                        $offset = round(count($settings_actions)/2);
                        $columns = array(
                            array_slice($settings_actions, 0, $offset),
                            array_slice($settings_actions, $offset)
                        );
                    }
                    else {
                        $columns = array($settings_actions);
                    }
                ?>
                <table class="table <?($has_portfolio_programs ? "two-columns" : "")?>">
                    <thead>
                        <tr>
                            <th><?=translate('Настройки')?></th>
                            <? if ( $has_portfolio_programs ) { ?>
                                <th></th>
                            <? } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <? foreach( $columns as $column_actions ) { ?>
                            <td>
                                <?php foreach ( $column_actions as $action ) { ?>
                                    <? $target = defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : '_blank'; ?>
                                    <i class="<?=$action['icon']?>"></i> <a target="<?=$target?>" href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                <?php } ?>
                            </td>
                        <? } ?>
                        </tr>
                    </tbody>
                </table>
                <? } ?>
            </div>
          </div>
        </div>
    </li>
</ul>    
        
