<?php $view->extend('core/Page.php'); ?>

<div class="row-fluid">
    <div class="row-fluid img-cont">
        <img src="/images/login/img.png" srcset="/images/login/img.png 800w, /images/login/img@2x.png 1399w, /images/login/img@3x.png 1500w" class="login-img">
        <img src="/images/login/logo.png" class="logo-img">
    </div>
    <section class="content login">
        <div class="container-fluid">
            <div class="row-fluid">
                <?php echo $view->render('core/FormAsyncBody.php', array_merge($parms, array( 'actions_on_top' => false))); ?>
            </div>
        </div>
    </section>
</div>

<div class="clearfix"></div>