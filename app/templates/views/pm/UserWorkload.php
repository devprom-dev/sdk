<?php

$data['LeftWork'] = round($data['LeftWork'],1);
if ( $capacity > 0 ) {
    if ($data['LeftWork'] > $capacity) {
        $data['LeftWork'] = '<span class="label label-important">' . $data['LeftWork'] . '</span>';
    } else {
        $data['LeftWork'] = '<span class="label label-success">' . $data['LeftWork'] . '</span>';
    }
}

$user_title =
    str_replace('%1',
        round($data['Planned'],1),
        str_replace('%2',
            $data['LeftWork'],
            str_replace('%3',
                round($data['Fact'],1),
                str_replace('%4',
                    round($capacity,1),
                    $capacity > 0 ? text(2685) : text(1857)) )));

if ( $report_url != '' ) $user_title .= ' &nbsp; <a class="dashed" target="_blank" href="'.$report_url.'">'.translate('подробнее').'</a>';
if ( $user == '' ) $user = text(1901);

$iterations = array();

if ( is_array($data['Iterations']) ) {
	foreach( $data['Iterations'] as $iteration_data ) {
		$full_volume = $iteration_data['capacity'];
		$used_volume = $iteration_data['leftwork'];

		$left_volume = $full_volume - $used_volume;
		
		if ( $full_volume > 0.0 )
		{
			$filled_volume = round(($used_volume / $full_volume) * 100, 0);
		}
		
		$overload = false;

		if($left_volume < 0)
		{
			$overload = true;
			if ( $filled_volume > 0.0 )
			{
				$filled_volume = round((100 / $filled_volume) * 100, 0);
			}
			else
			{
				$filled_volume = 0;
			}
		}

		$iterations[] = array (
			'title' => $iteration_data['title'].' - '.preg_replace(
							array('/%1/', '/%2/', '/%3/'),
							array(round($used_volume, 1), round($full_volume, 1), abs(round($left_volume,2))),
							$left_volume < 0 ? text(1900) : text(1899)
					   ),
			'name' => mb_substr($iteration_data['number'], 0, 20),
			'progress' => $overload
								? '<div class="progress"><div class="bar bar-danger" style="width: 100%;"></div></div>'
								: '<div class="progress"><div class="bar bar-success" style="width:'.$filled_volume.'%;"></div></div>',
			'url' => $iteration_data['url']
		);
	} 
}

?>

<div class="user-workload">
	<?=$user_title?>
	<?php foreach( $iterations as $cell ) { ?>
		<?=$cell['name']?>
		<span class="progress-cell" title="<?=$cell['title']?>"><a href="javascript:<?=$cell['url']?>"><?=$cell['progress']?></a></span>
	<?php } ?>
</div>
