<?php 

if ( array_key_exists($current_portfolio,$portfolios) )
{
	$portfolio = $portfolios[$current_portfolio];
	unset($portfolios[$current_portfolio]);
}
else
{
	$portfolio = $programs[$current_portfolio];
}

$portfolios = array_merge ( 
		array_pad( array( $current_portfolio => $portfolio ), count($projects[$current_portfolio]) + 1, array() ),  
		$portfolios
);  

?>

<ul class="dropdown-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span6">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?=$portfolio_title?></th>
                            <th><?=$subprojects_title?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php foreach( $portfolios as $item ) { ?>
                                <a href="<?=$item['url']?>"><?=$item['name']?></a><br/>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ( is_array($projects[$current_portfolio]) ) { foreach( $projects[$current_portfolio] as $item ) { ?>
                                <a href="<?=$item['url']?>"><?=$item['name']?></a><br/>
                                <?php }} ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
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
