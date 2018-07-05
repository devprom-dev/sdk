<?php

class FieldListOfProjectRoles extends Field
{
	private $object_it = null;

	function __construct( $object_it ) {
		$this->object_it = $object_it;
	}

	function getText()
    {
        $roleIt = getFactory()->getObject('ParticipantRole')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('Participant', $this->object_it->getId())
            )
        );

        $roles = $this->object_it->getRoles();
        if ( count($roles) < 1 ) $roles = array(0);
        $roleIt = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
            array(
                new FilterInPredicate($roles)
            )
        );

        $html = join('<br/>', $roleIt->fieldToArray('Caption'));

        $permissionsUrl = getFactory()->getObject('Module')->getExact('permissions/settings')->getUrl(
            'user='.$this->object_it->get('SystemUser')
        );
        $html .= '<br/><br/><a class="dashed" target="_blank" href="'.$permissionsUrl.'">'.text(2454).'</a>';

        return $html;
    }

	function render( $view )
	{
		echo '<div class="input-block-level well well-text">';
            echo $this->getText();
		echo '</div>';
	}
}