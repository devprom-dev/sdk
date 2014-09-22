<?php

class TerminologyList extends PMStaticPageList
{
 	var $term;
 	
 	function getPredicates( $values )
 	{
 		$this->term = $values['searchsystem'];
 		
 		return array (
 			new LangResourceTermPredicate( $this->term ),
 			new LangResourceOverridenPredicate( $values['overriden'] )
 		);
 	}
 	
	function getColumns()
	{
		$this->object->addAttribute('CustomValue', '', translate('Проектное значение'), true);
		
		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'ResourceKey':
 				return false;
				
			default:
 				return parent::IsNeedToDisplay( $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'ResourceValue':
			    
				$value = $object_it->get('OriginalValue');

				if ( $this->term != '' )
				{
					$value = IteratorBase::utf8towin(
					        preg_replace('#'.IteratorBase::wintoutf8($this->term).'#iu', '<span class="label">\\0</span>', 
					                IteratorBase::wintoutf8($value)));
				}
				
				echo $value;
				
				break;

			case 'CustomValue':
				
			    $method = new AutoSaveFieldWebMethod( $object_it, 'ResourceValue' );
				$method->setRows( 2 );
				$method->draw();

				break;
		}
	}
	
	function getColumnWidth( $attr ) 
	{
		if( $attr == 'ResourceValue' ) 
		{
			return '50%';
		}
		else
		{
			return '50%';
		}
	}

	function IsNeedToDisplayNumber( ) { return true; }
	
	function getGroupFields()
	{
		return array();
	}

	function getColumnFields()
	{
		return array();
	}
	
	function getItemActions( $dummy, $object_it )
	{
	    return array();
	}
}