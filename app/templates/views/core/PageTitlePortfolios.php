<ul class="dropdown-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <?php
                                if ( count($portfolio_actions) > 0 ) {
                                    echo text('portfolio.name');
                                } else if ( count($program_actions) > 0 ) {
                                    echo translate('Программа');
                                }
                                ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php foreach ( $portfolio_actions as $action ) { ?>
                                    <i class="<?=$action['icon']?>"></i> <a href="<?=$action['url']?>"><?=$action['name']?></a><br/>
                                <?php } ?>
                                <?php foreach ( $program_actions as $action ) { ?>
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
