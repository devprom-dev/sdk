<?php 

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

echo '<div>';
echo '<div style="float:left">';
echo '<img class="photo" src="'.$user_it->getFileUrl().'" style="width:60px;height:60px;">';
echo '</div>';
echo '<div style="float:left;padding-left:20px;">';
echo '<h3 class="title">'.$user_it->getDisplayName().'</h3>';
echo '<div>email: '.$user_it->get('Email').'</div>';
echo '</div>';
echo '</div>';

echo '<div style="clear:both"/>';
echo '<br/>';

echo '<div>';

echo $view->render('core/PageTableBody.php', $parms);

echo '</div>';

?>