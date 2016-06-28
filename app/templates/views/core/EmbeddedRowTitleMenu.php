<div class="btn-group btn-menu" style="vertical-align:top;">
    <span class="btn btn-link title" data-toggle="dropdown"><?=$title?></span>
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>