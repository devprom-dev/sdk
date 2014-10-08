<?php $view->extend('core/Page.php'); ?>

<div class="row-fluid">
    <div class="span3"></div>
    <div class="span6">
        <br/><br/><br/><br/>
        <section class="content">
            <div class="container-fluid">
                <div class="row-fluid">
                    <?php echo $view->render('core/FormAsyncBody.php', $parms); ?>
                    
                    <br/>
                    <?php echo text('saasassist21'); ?>
					<div class="clearfix"></div>
					<br/>
                </div>
            </div>
        </section>
    </div>
    <div class="span3"></div>
</div>

<div class="clearfix"></div>

