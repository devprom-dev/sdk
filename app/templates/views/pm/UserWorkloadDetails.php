<?php
$user_title =
    str_replace('%1', $measure->getDimensionText(round(0,1)),
        str_replace('%2', $measure->getDimensionText(round($leftWork,1)),
            str_replace('%3', $url, text(2168)) ));
?>
<? if ( $user_id != '' ) { ?>
<div>
	<ul class="nav">
        <?php if ( $skipPhoto == '' ) { ?>
            <li class="nav-cell nav-left">
                <?php
                echo $view->render('core/UserPicture.php', array (
                    'id' => $user_id,
                    'class' => 'user-avatar',
                    'image' => 'userpics',
                    'title' => $user_name
                ));
                ?>
            </li>
        <?php } ?>
		<li class="nav-cell nav-text">
			<?=$user_title?>
		</li>
	</ul>
</div>
<? } ?>