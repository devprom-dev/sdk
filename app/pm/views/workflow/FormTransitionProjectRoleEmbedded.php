<?php

class FormTransitionProjectRoleEmbedded extends PMFormEmbedded
{
    private $transitionIt = null;

    function setTransitionIt( $it ) {
        $this->transitionIt = $it;
    }

 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ProjectRole':
 				return true;

 			default:
 				return false;
 		}
 	}

    function drawAddButton($view, $tabindex)
    {
        parent::drawAddButton($view, $tabindex);

        echo '<br/>';
        echo '<br/>';
        echo $view->render('pm/ConditionsLogic.php', array (
            'field' => 'ProjectRolesLogic',
            'value' => is_object($this->transitionIt) ? $this->transitionIt->get('ProjectRolesLogic') : '',
            'default' => 'any'
        ));
    }
}
