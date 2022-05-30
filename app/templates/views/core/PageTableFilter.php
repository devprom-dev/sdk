<?php foreach( $filter_settings as $filter ) { ?>
    <div class="btn-group filter" style="width: 1%;">
        <?php echo $filter['html']; ?>
    </div>
<?php } ?>

<div id="page-filter" class="well well-filter hidden-print filter-cell <?=($filter_modified ? 'modified' : '')?>">
    <div class="filter ">
        <table>
            <tr>
                <?php foreach( $filter_buttons as $button ) { ?>
                    <td width="1%">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-info btn-filter" uid="<?=$button['name']?>" href="javascript: filterLocation.showPopover();" title="<?=htmlentities($button['caption'].': '.$button['value'])?>">
                                <?=$button['caption'].':'.trim($button['value'], ' ,')?>
                            </a>
                            <a class="btn btn-sm btn-info btn-close" aria-hidden="true" parm-name="<?=$button['name']?>">&times;</a>
                        </div>
                    </td>
                <?php } ?>
                <td id="search-area">
                    <?php if ( $filter_search['searchable'] ) { ?>
                        <input type="text" class="search" placeholder="<?=text(2908)?>" value="<?=htmlentities($filter_search['value'])?>" autocomplete="off">
                    <?php } ?>
                </td>
                <td width="1%">
                    <?php
                    if ( $filter_modified ) {
                        $persistItem = $filterMoreActions['personal-persist'];
                        unset($filterMoreActions['personal-persist']);
                        if (!is_array($persistItem)) {
                            $persistItem = $filterMoreActions['common-persist'];
                            unset($filterMoreActions['common-persist']);
                        }

                        $html = $view->render('core/LinkMenu.php', array(
                            'title' => '<a id="'.$persistItem['uid'].'" href="' . $persistItem['url'] . '">' . text(1285) . '</a> ',
                            'items' => array(),
                            'id' => 'settings-persist-alert'
                        ));
                        echo $html;
                    }
                    ?>
                </td>
                <td class="text-right" width="1">
                </td>
                <td class="text-right" width="1">
                </td>
            </tr>
        </table>
    </div>
    <div id="page-filter-btns">
        <div class="btn-group">
            <a class="btn btn-xs btn-cell dropdown-toggle transparent-btn btn-filter-more" uid="filter-more-actions" href=""
               data-toggle="dropdown">
                <span class="label">...</span>
            </a>
            <? echo $view->render('core/PopupMenu.php', array('items' => $filterMoreActions, 'uid' => 'filter-more-actions')); ?>
        </div>
        <div class="btn-group">
            <a uid="reset" class="btn btn-xs btn-cell transparent-btn close" aria-label="<?=text(2088)?>" onclick="filterLocation.resetFilter()">
                <span aria-hidden="true">&times;</span>
            </a>
        </div>
    </div>
</div>