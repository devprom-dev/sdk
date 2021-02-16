<div style="display:table;">
    <div style="display:table-cell;padding-right:8px;">
        <?=$title?>
    </div>
    <div style="display:table-cell;width:1%;">
        <div class="btn-group operation last">
              <a tabindex="-1" class="btn btn-sm dropdown-toggle btn-light actions-button" data-toggle="dropdown">
                <i class="icon-asterisk icon-gray"></i>
                <span class="caret"></span>
              </a>
              <?php
                    echo $view->render('core/PopupMenu.php', array (
                        'items' => $actions
                    ));
              ?>
        </div>
    </div>
</div>
