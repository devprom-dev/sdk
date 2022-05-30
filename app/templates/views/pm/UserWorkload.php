<?php

if ( $date > date('Y-m-d') ) {
    $user_title = sprintf(text(2685), $workload, getSession()->getLanguage()->getDateFormattedShort($date));
}
else {
    $user_title = sprintf(text(1857), $workload);
}

if ( $report_url != '' ) $user_title .= ' &nbsp; <a class="dashed" target="_blank" href="'.$report_url.'">'.translate('подробнее').'</a>';

?>

<div class="user-workload">
	<?=$user_title?>
</div>
