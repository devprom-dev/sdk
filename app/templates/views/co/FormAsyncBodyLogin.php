<?php
$form_id = 'myForm'; 
?>

<script language="javascript">
    var originalFormState = '';

    $(document).ready(function() 
	{
		var formOptions = makeAsyncForm('<?=$form_id?>', '<?=$url?>', '');

	    formOptions.beforeSubmit = function() {};
	});
</script>

<form id="<?=$form_id?>" class="form-horizontal" action="<?=$view['router']->generate('login')?>" method="post" enctype="application/x-www-form-urlencoded">
	<input type="hidden" id="action<?=$form_id?>" name="action" value="<?=$form_action?>">
	<input type="hidden" name="redirect" value="<?=$redirect_url?>">
	
    <fieldset>
        <legend><?=text(1307)?></legend>

	    <label>&nbsp;</label>
        
        <div class="input-prepend input-block-level">
          <span class="add-on"><i class="icon-user"></i></span>			    
	      <input type="text" class="span11" id="login" name="login" placeholder="<?=translate('Логин')?>">
	    </div>

	    <label>&nbsp;</label>
	    
        <div class="input-prepend input-block-level">
          <span class="add-on"><i class="icon-lock"></i></span>			    
		  <input type="password" class="span11" id="pass" name="pass" placeholder="<?=translate('Пароль')?>">
        </div>
        
	    <label>&nbsp;</label>
        
        <div class="clearfix"></div>

        <div id="result<?=$form_id?>"></div>
        
	    <label>&nbsp;</label>
        
      <div class="pull-left">
        <div>
            <button type="submit" class="btn btn-primary" style="padding-left:30px;padding-right:30px;" onclick="<?="javascript: $('#action').val('".$form_action."');"?>" ><?=translate('Войти')?></button>
        </div>
      </div>
      <div class="pull-left" style="padding:6px 0 0 20px;">
        <div> 
            <label class="checkbox">
              <input name="remember" type="checkbox"> <?=text(1308)?>
            </label>
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