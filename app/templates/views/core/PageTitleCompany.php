<ul class="dropdown-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span6">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?=(translate('Портфели').(count($programs) > 0 ? ' / '.translate('Программы') : ''))?></th>
                            <th><?=translate('Проекты')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
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
                            </td>
                        </tr>
                        <tr>
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
                        </tr>
                   </tbody>
                </table>
            </div>
          </div>
        </div>
    </li>
</ul>    
        
