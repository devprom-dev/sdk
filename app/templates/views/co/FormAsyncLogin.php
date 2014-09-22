<?php $view->extend('core/Page.php'); ?>

<div class="row-fluid">
    <div class="span4"></div>
    <div class="span4">
        <br/><br/><br/><br/>
        <section class="content login">
            <div class="container-fluid">
                <div class="row-fluid">
                    <?php echo $view->render('co/FormAsyncBodyLogin.php', $parms); ?>
                </div>
            </div>
        </section>
      </div>
    <div class="span4"></div>
</div>

<div class="clearfix"></div>
