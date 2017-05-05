<?php if ( $attribute['type'] == 'char' ) { ?>
    <div class="control-group" id="fieldRow<?=$key?>">
        <label class="control-label" for="<?=$attribute['id']?>"></label>
        <div class="controls">
            <? echo $view->render('core/PageFormAttribute.php', $attribute); ?>

            <?php if ( $attribute['description'] != '' ) { ?>
                <span class="help-block"><?=$attribute['description']?></span>
            <?php } ?>
        </div>
    </div>
<?php } else if ( is_object($attribute['field']) || $attribute['html'] != '' ) { ?>
    <div class="control-group row-fluid" id="fieldRow<?=$key?>">
        <label class="control-label <?=(count(explode(' ', $attribute['name']))>1?'label-long':'')?>" for="<?=$attribute['id']?>"><?=$attribute['name']?></label>
        <div class="controls">
            <? echo $view->render('core/PageFormAttribute.php', $attribute); ?>

            <?php if ( $attribute['description'] != '' ) { ?>
                <span class="help-block"><?=$attribute['description']?></span>
            <?php } ?>
        </div>
    </div>
<?php } ?>