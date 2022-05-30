<ul class="dropdown-menu navbar-menu">
    <li>
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span12">
                <table class="table two-columns">
                    <thead>
                        <tr>
                            <th colspan="2" style="padding-right: 0;">
                                <span class="pull-left" style="margin-top: 3px;">
                                    <?=text('projects.name')?>
                                </span>
                                <span class="pull-right project-search">
                                    <input class="" type="text" placeholder="<?=text(2304)?>" autocomplete="off">
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="100%" style="padding-left: 0;">
                                <div id="projects-tree" data-type="json"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table two-columns">
                    <tbody>
                        <tr>
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
                        </tr>
                   </tbody>
                </table>

                <? if ( count($settings_actions) > 0 ) { ?>
                <?
                    $offset = round(count($settings_actions)/2);
                    $columns = array(
                        array_slice($settings_actions, 0, $offset),
                        array_slice($settings_actions, $offset)
                    );
                ?>
                <table class="table two-columns">
                    <thead>
                        <tr>
                            <th><?=translate('Настройки')?></th>
                            <th></th>
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
        
