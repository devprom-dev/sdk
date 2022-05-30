<div class="row-fluid" style="padding:6px 10px 0 0;width:99%;">
    <div class="fltr-clm">
        <table class="table table-bordered table-striped">
            <tbody>
                <?php foreach( $parms as $parmKey => $parameter ) { ?>
                    <tr>
                        <td width="160"><?=$parameter['name']?></td>
                        <td style="min-width: 230px;">
                            <div style="display: table;width:100%;">
                                <div style="display: table-cell;vertical-align: top;">
                                    <select class="filter input-block-level" name="<?=$parmKey?>" <?=$parameter['attribute']?> >
                                        <?php foreach( $parameter['options'] as $optKey => $optValue ) { ?>
                                            <option title="<?=htmlentities(join(' - ', array_filter(array($optValue, $parameter['titles'][$optKey]), 'strlen')))?>" value="<?=$optKey?>"
                                                <?=(in_array(trim($optKey),\TextUtils::parseFilterItems($parameter['value'])) ? 'selected' : '')?> ><?=\TextUtils::getWords($optValue,3)?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if (strpos($parameter['attribute'], 'sort-order') !== false) { ?>
                                    <div class="sort-radio">
                                        <label class="radio pull-left">
                                            <input type="radio" value="A" name="group<?=$parmKey?>" <?=($parameter['sort-value']=='A'?'checked':'')?> >
                                                <?=translate('По возрастанию')?>
                                        </label>
                                    </div>
                                    <div class="sort-radio">
                                        <label class="radio pull-right">
                                            <input type="radio" value="D" name="group<?=$parmKey?>" <?=($parameter['sort-value']=='D'?'checked':'')?> >
                                                <?=translate('По убыванию')?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
