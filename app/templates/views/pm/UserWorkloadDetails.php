<?php
$user_title = str_replace('%1', $measure->getDimensionText(round($data['Planned'],1)),
					str_replace('%2', $measure->getDimensionText(round($data['LeftWork'],1)),
						str_replace('%3', round($data['Fact'],1), text(2168)) ));

$iterations = array();
if ( is_array($data['Iterations']) ) {
	foreach( $data['Iterations'] as $iteration_data )
	{
		$full_volume = $iteration_data['capacity'];
		$used_volume = $iteration_data['leftwork'];
		$left_volume = $full_volume - $used_volume;
		
		if ( $full_volume > 0.0 ) {
			$filled_volume = round(($used_volume / $full_volume) * 100, 0);
		}
		
		$overload = false;
		if($left_volume < 0) {
			$overload = true;
			if ( $filled_volume > 0.0 ) {
				$filled_volume = round((100 / $filled_volume) * 100, 0);
			}
			else {
				$filled_volume = 0;
			}
		}
		$title =
			preg_replace(
				array('/%0/', '/%1/', '/%2/', '/%3/'),
				array(
                    $iteration_data['title'],
                    $measure->getDimensionText(round($used_volume, 1)),
                    $measure->getDimensionText(round($full_volume, 1)),
                    $measure->getDimensionText(abs(round($left_volume,2)))
                ),
				$overload ? text(2170) : text(2169)
			);

		$iterations[] = array (
			'name' => mb_substr($iteration_data['number'], 0, 20),
			'progress' => $overload
								? '<div class="progress progress-text"><div class="bar bar-text bar-danger" style="width: 100%;"></div><span>'.$title.'</span></div>'
								: '<div class="progress progress-text progress-success"><div class="bar bar-text bar-success" style="width:'.$filled_volume.'%;"></div><span>'.$title.'</span></div>',
			'url' => $iteration_data['url']
		);
	}
}
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
<div class="planning-details">
	<?php foreach( $iterations as $cell ) { ?>
		<div class="progress-cell" title="<?=$cell['title']?>">
			<? if ( $cell['url'] != '' ) { ?>
				<a href="javascript:<?=$cell['url']?>">
					<?=$cell['progress']?>
				</a>
			<? } else { ?>
				<?=$cell['progress']?>
			<? } ?>
		</div>
	<?php } ?>
</div>