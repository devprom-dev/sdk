<ul class="dropdown-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span6">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?=$subprojects_title?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php if ( is_array($projects[$current_portfolio]) ) { foreach( $projects[$current_portfolio] as $item ) { ?>
                                <a href="<?=$item['url']?>"><?=$item['name']?></a><br/>
                                <?php }} ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php foreach ( $portfolio_actions as $action ) { ?>
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
