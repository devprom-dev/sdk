<?php 

while ( !$project_it->end() )
{
    $method = new UserExcludeWebMethod( $user_it, $project_it );
    
    $action = array( 
        'click' => $method->getJSCall(),
        'name' => $method->getCaption() 
    );
    
    echo $this->render('core/TextMenu.php', array (
            'title' => $project_it->getDisplayName().' ['.$project_it->get('CodeName').']',
            'items' => array( array(), $action )
    ));
    
    echo '<div class="clearfix"></div>';
    
	$project_it->moveNext();
}
