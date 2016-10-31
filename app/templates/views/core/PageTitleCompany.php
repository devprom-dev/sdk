<?
$has_portfolio_programs = count($portfolios) + count($programs) > 0;
foreach( $programs as $program_id => $item ) {
    unset($projects[$program_id][$program_id]);
}
foreach( $portfolios as $portfolio_id => $item ) {
    unset($projects[$portfolio_id][$portfolio_id]);
}
?>

<ul class="dropdown-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span12">
                <table class="table <?($has_portfolio_programs ? "two-columns" : "")?>">
                    <thead>
                        <tr>
                            <? if ( $has_portfolio_programs ) { ?>
                            <th><?=(text('portfolios.name').(count($programs) > 0 ? ' / '.translate('Программы') : ''))?></th>
                            <? } ?>
                            <th><?=text('projects.name')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <? if ( $has_portfolio_programs ) { ?>
                            <td>
                                <?php if ( count($programs) > 0 ) { ?>
                                    <?php foreach( $programs as $program_id => $item ) { ?>
                                        <a href="<?=$item['url']?>"><?=$item['name']?></a>
                                        <?php if ( count($projects[$program_id]) > 0 ) { echo str_repeat('<br/>', count($projects[$program_id])); } ?>
                                        <?php if ( count($projects[$program_id]) < 1 ) { ?> <br/> <?php } ?>
                                        <br/>
                                    <?php } ?>
                                <?php } ?>
                                    
                                <?php foreach( $portfolios as $portfolio_id => $item ) { ?>
                                    <a href="<?=$item['url']?>"><?=$item['name']?></a><br/>
                                    <?php if ( count($projects[$portfolio_id]) > 0 ) { echo str_repeat('<br/>', count($projects[$portfolio_id])); } ?>
                                    <?php if ( count($projects[$portfolio_id]) < 1 ) { ?> <br/> <?php } ?>
                                <?php } ?>
                            </td>
                            <? } ?>
                            <td>
                                <?php if ( count($programs) > 0 ) { ?>
                                    <?php foreach( $programs as $program_id => $program ) { ?>
                                        <?php if ( !is_array($projects[$program_id]) ) continue; ?>
                                        <?php foreach( $projects[$program_id] as $project_id => $project ) { ?>
                                            <a href="<?=$project['url']?>"><?=$project['name']?></a><br/>
                                        <?php } ?>
                                        <br/>
                                    <?php } ?>
                                <?php } ?>
                                
                                <?php foreach( $portfolios as $portfolio_id => $program ) { ?>
                                    <?php if ( !is_array($projects[$portfolio_id]) ) continue; ?>
                                    <?php foreach( $projects[$portfolio_id] as $project_id => $project ) { ?>
                                        <a href="<?=$project['url']?>"><?=$project['name']?></a><br/>
                                    <?php } ?>
                                    <br/>
                                <?php } ?>
                                <?php if (is_array($projects['']) ) foreach( $projects[''] as $project_id => $project ) { ?>
                                    <a href="<?=$project['url']?>"><?=$project['name']?></a><br/>
                                <?php } ?>
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
                                <td>
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
        
