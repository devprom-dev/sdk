<?php
$form_id = 'myForm';
$restoreAction = array_shift($actions);
?>

<script language="javascript">
    $(document).ready(function()
	{
		var formOptions = makeAsyncForm('<?=$form_id?>', '<?=$url?>', '');

	    formOptions.beforeSubmit = function() {};
	});
</script>

<form id="<?=$form_id?>" class="form-horizontal" action="<?=$view['router']->generate('login_process_auth')?>" method="post" enctype="application/x-www-form-urlencoded">
	<input type="hidden" id="action<?=$form_id?>" name="action" value="<?=$form_action?>">
	<input type="hidden" name="redirect" value="<?=$redirect_url?>">

    <fieldset>
        <legend><?=text(1307)?></legend>

	    <label><?=translate('Логин')?></label>
        
        <div class="input-prepend input-block-level input-login">
          <input type="text" class="" id="login" name="login" placeholder="<?=text(2629)?>">
	    </div>

	    <label><?=translate('Пароль')?></label>
	    
        <div class="input-prepend input-block-level">
          <input type="password" class="" id="pass" name="pass" placeholder="<?=text(2630)?>">
        </div>

        <div>
            <div class="pull-left remember-field">
                <label class="checkbox">
                    <input name="remember" type="checkbox" checked > <?=text(1308)?>
                </label>
            </div>
            <div class="pull-right remember-field">
                <label>
                    <?php echo str_replace('%1', $restoreAction['url'], $restoreAction['name']) ?>
                </label>
            </div>
        </div>
        
        <div class="clearfix"></div>

        <div id="result<?=$form_id?>"></div>
        
      <div class="enter">
        <div>
            <button type="submit" class="btn btn-lg btn-primary" onclick="<?="javascript: $('#action').val('".$form_action."');"?>" ><?=translate('Войти')?></button>
        </div>
      </div>

        <div class="clearfix"></div>
      
      <label>&nbsp;</label>

      <?php foreach( $actions as $action ) { ?>
        <div>
            <?php echo str_replace('%1', $action['url'], $action['name']) ?>
        </div>
      <?php } ?>
	    
	    <label>&nbsp;</label>
      
      </fieldset>
</form>