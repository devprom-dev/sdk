<?php

class IssueCompoundSection extends InfoSection
{
 	var $object_it, $left_sections, $right_sections;
 	
 	function IssueCompoundSection( $object_it )
 	{
 		$this->object_it = $object_it;
 		$this->left_sections = array();
 		$this->right_sections = array();
 		$this->left_style = 'width:49%';
 		$this->right_style = 'width:49%';
 		
 		parent::InfoSection();
 	}
 	
 	function addLeft( $section )
 	{
 		array_push( $this->left_sections, $section );
 	}
 	
 	function addRight( $section )
 	{
 		array_push( $this->right_sections, $section );
 	}

	function setLeftStyle( $style )
	{
		$this->left_style = $style;
	}
	
	function setRightStyle( $style )
	{
		$this->right_style = $style;
	}

 	function draw()
 	{
 		echo '<div style="width:100%;">';
 			echo '<div style="float:left;'.$this->left_style.';">';
		 		foreach ( $this->left_sections as $section )
		 		{
		 			if ( !$section->IsActive() ) continue;
		 			
		 			$section->draw();
		 		}
 			echo '</div>';
 			echo '<div style="float:right;'.$this->right_style.';">';
		 		foreach ( $this->right_sections as $section )
		 		{
		 			if ( !$section->IsActive() ) continue;

		 			$section->draw();
		 		}
 			echo '</div>';
 		echo '</div>';
	}
}  