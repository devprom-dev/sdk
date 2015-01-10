<?php $view->extend('core/Page.php'); ?>

<div class="row-fluid">
        <section class="content login">
            <div class="container-fluid">
                <div class="row-fluid">
                    <?php echo $view->render('co/FormAsyncBodyLogin.php', $parms); ?>
                </div>
            </div>
        </section>
</div>

<div class="clearfix"></div>
