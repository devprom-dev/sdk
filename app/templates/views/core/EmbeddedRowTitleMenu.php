<div class="btn-group <?=$position?>" style="vertical-align:top;">
    <div class="btn dropdown-toggle transparent-btn" data-toggle="dropdown" href="#" style="display:table;width:auto;">
        <span class="title" style="display:table-cell;"><?=$title?></span>
        <span style="display:table-cell;padding-left:6px;"><span class="caret"></span></span>
        </div>
    
    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>