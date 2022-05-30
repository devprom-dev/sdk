<?php

$filterChunks = array_chunk(
        array_filter(
            $filter_items,
            function($filter) {
                return is_array($filter['options'])
                    || is_subclass_of($filter['class'], 'SelectDateRefreshWebMethod');
            }),
        8, true
    );

?>
<div class="row-fluid filter-body" style="padding:6px 10px 0 0;width:99%;opacity:0;">
    <?php foreach( $filterChunks as $chunkKey => $filters ) { ?>
        <div class="fltr-clm">
            <table class="table table-bordered table-striped">
                <tbody>
                <?php foreach ($filters as $filter) { ?>
                    <?php if (is_array($filter['options'])) { ?>
                        <tr>
                            <td width="160"><?=$filter['caption']?></td>
                            <td style="width:280px;">
                                <select class="filter input-block-level" name="<?=$filter['name']?>" <?=$filter['attribute']?> lazyurl="<?=$filter['lazyurl']?>" settings-url="<?=$filter['options']['_options']['href']?>" style="display:none;">

                                    <?php foreach( $filter['options'] as $optKey => $optValue ) { ?>
                                        <?php if (in_array($optKey, array('search','_options'))) continue; ?>
                                        <?php if ($optKey == 'all' && $filter['attribute'] == 'multiple') continue; ?>
                                        <?php if ($optKey == '-') { ?>
                                            <option data-role="divider"></option>
                                        <?php } else { ?>
                                            <?php
                                                $filterValue = \TextUtils::parseItems($filter['value']);
                                                $checked = in_array(trim($optKey),$filterValue) || trim($optKey) == trim($filter['value']);
                                            ?>
                                            <option class="<?=(in_array($optKey,array('any','none')) ? "special" : "")?>" value="<?=$optKey?>" <?=($checked ? 'selected' : '')?> >
                                                <?=\TextUtils::stripAnyTags($optValue)?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    <?php } elseif (is_a($filter['class'], 'FilterDateIntervalWebMethod', true) ) { ?>
                        <tr>
                            <td width="160"><?=$filter['caption']?></td>
                            <td class="filter-pair">
                                <input type="text" class="input-block-level datepicker-filter" value="<?=$filter['value']?>" name="<?=$filter['name']?>" autocomplete="off">
                                <input type="text" class="input-block-level datepicker-filter" value="<?=$filter['valueRight']?>" name="<?=$filter['nameRight']?>" autocomplete="off">
                            </td>
                        </tr>
                    <?php } elseif (is_subclass_of($filter['class'], 'SelectDateRefreshWebMethod')) { ?>
                        <tr>
                            <td width="160"><?=$filter['caption']?></td>
                            <td>
                                <input type="text" class="input-block-level datepicker-filter" value="<?=$filter['value']?>" name="<?=$filter['name']?>" autocomplete="off">
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>

<div class="row-fluid text-right" style="padding:0 10px 0 10px;width:99%">
    <div class="btn-group">
        <button id="btnSave" class="btn btn-primary"><?=translate('Применить')?></button>
    </div>
    <div class="btn-group">
        <button id="btnClose" class="btn btn-default"><?=translate('Закрыть')?></button>
    </div>
</div>

