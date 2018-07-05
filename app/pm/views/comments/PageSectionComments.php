<?php

include_once 'CommentList.php';

class PageSectionComments extends InfoSection
{
 	var $object_it;
 	
 	private $baseline = '';
	private $title = '';
	private $id = '';
    private $options = array();

 	function __construct( $object_it, $baseline = '' )
 	{
		parent::__construct();

 		$this->object_it = $object_it;
 		$this->baseline = $baseline;
		$this->setCaption(translate('Комментарии'));
		$this->setId( parent::getId() );
		$this->setPlacement('bottom');
 	}

	function setCaption( $title ) {
		$this->title = $title;
	}

 	function getCaption() {
 		return $this->title;
 	}

	function setId( $id ) {
		$this->id = $id;
	}

	function getId() {
		return $this->id;
	}

	function setOptions( $options ) {
	    $this->options = $options;
    }

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}

 	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array (
			'section' => $this
		));
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionComments.php';
	}
 	
 	function render( $view, $parms = array() )
 	{
		$comment_list = new CommentList( $this->object_it, $this->baseline );
        if ( array_key_exists('collapsable', $this->options) ) {
            $comment_list->setCollabseable($this->options['collapsable']);
        }
        if ( array_key_exists('autorefresh', $this->options) ) {
            $comment_list->setAutoRefresh($this->options['autorefresh']);
        }
		$comment_list->render( $view, $parms );
	}

    function modifiable() {
        return getFactory()->getAccessPolicy()->can_modify(getFactory()->getObject('Comment'));
    }
}
