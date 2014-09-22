<div class="btn-group">

     <button type="submit" class="btn btn-primary" onclick="<?=$url?>" >
         &nbsp; <?=$title?> &nbsp;
     </button>
      
     <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
         <span class="caret"></span>
     </button>

    <?php echo $view->render('core/PopupMenu.php', array ( 'items' => $items) ); ?>
</div>