<?=text(2328)?>
<? if ( count($rows) > 10 ) { ?>
    <input class="users-search" type="text" placeholder="<?=text(2329)?>">
<? } ?>
<div class="users-container">
    <table class="table users-table">
        <tbody>
        <? foreach( $rows as $row ) { ?>
            <tr>
                <td>
                    <label for="user-<?=$row['id']?>">
                        <input type="checkbox" id="user-<?=$row['id']?>" name="_user_<?=$row['id']?>"/> &nbsp;
                        <?php
                        echo $view->render('core/UserPictureMini.php', array (
                            'id' => $row['id'],
                            'image' => 'userpics-mini',
                            'class' => 'user-mini'
                        ));
                        ?>
                        <span class="user-title">
                            <?=$row['name']?>
                        </span>
                    </label>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('input.users-search')
            .on('keyup', function(e) {
                var items = $('.users-table').find('tr');
                items.hide();
                var text = $(this).val();
                var visibleItems = items.filter(function(i, el) {
                    return $(el).text().match(new RegExp(text, "ig"));
                });
                visibleItems.show();
            });
    });
</script>