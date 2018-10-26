<?php $view->extend('core/Page.php'); ?>

<?php $parms['buttons_template'] = 'core/WizardFormButtons.php'; ?>

<div class="row-fluid">
    <div class="span3"></div>
    <div class="span6">
        <br/><br/><br/><br/>
        <section class="content">
            <div class="container-fluid">
                <div class="row-fluid">
                    <?php 
                    
                    	echo $view->render('core/FormAsyncBody.php', array_merge($parms, array('buttons_template' => '')) ); 
                    	
                    ?>
                    <div class="clearfix"></div>
                    <br/>
                </div>
            </div>
        </section>
    </div>
    <div class="span3"></div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#action<?=$parms['form_id']?>').val(<?=$parms['form_action']?>);
		$('#<?=$parms['form_id']?>').submit();
	});
</script>