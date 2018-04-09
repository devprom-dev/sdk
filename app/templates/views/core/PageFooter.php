<footer class="<?=($inside ? 'internal' : '')?> hidden-print">
	<ul>
		<li><? echo '<a tabindex="-1" target="_blank" href="http://devprom.ru">'.$license_name.'</a> '.$current_version; ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
		<li><a tabindex="-1" target="_blank" href="http://support.devprom.ru/issue/new"><?=translate('Сообщить о проблеме')?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
		<li><a tabindex="-1" target="_blank" href="http://devprom.ru/docs"><?=translate('Документация')?></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;</li>
		<li><a tabindex="-1" target="_blank" href="http://devprom.ru/news"><?=translate('Новости')?></a></li>
	</ul>
	<?php 
	 		
		 	if ( defined('METRICS_VISIBLE') && METRICS_VISIBLE ) 
		 	{
		 	 	$metrics_text = str_replace('%1', MetricsServer::Instance()->getDuration(), text(1067));
			 	$metrics_text = str_replace('%2', MetricsClient::Instance()->getDuration('clscript'), $metrics_text);
		 	}
			
			?>
	<ul>
		<li><?=$metrics_text?></li>
	</ul>
</footer>