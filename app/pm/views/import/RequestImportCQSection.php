<?php

class RequestImportCQSection extends InfoSection
{
 	function getCaption() {
 		return translate('Описание');
 	}
 	
 	function drawBody()
 	{
 		global $project_it;

 		echo '<div class="line">';
 		echo '1. '.text(428);
 		echo '</div>';

 		echo '<div class="line">';
 		echo '2. '.text(429);
 		echo '</div>';

 		echo '<div class="line">';
 		echo '3. '.text(430);
 		echo '</div>';

 		echo '<div class="line">';
 		echo '4. '.text(431);
 		echo '</div>';
 	}
}