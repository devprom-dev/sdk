<?php

class SearchList extends PMStaticPageList
{
    function getColumns() {
        return array('UID', 'Caption', 'ReferenceName');
    }

    function getGroupFields() {
		return array('ReferenceName');
	}

	function drawGroup( $group_field, $object_it ) 
	{
	    echo '<b>'.$object_it->get($group_field).'</b>';
        if ( $object_it->get('entityUrl') != '' ) {
            echo ' &nbsp; <a href="'.$object_it->get('entityUrl').'" target="_blank">'.text(2034).'</a>';
        }
	}
	
	function getItemActions( $column_name, $object_it ) {
		return array();
	}

	function drawCell( $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'Caption':
                foreach( $object_it->get('Content') as $value ) {
                    echo '<p>'.$value.'</p>';
                }
			    break;
            case 'UID':
                echo $object_it->get('UID');
                break;
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getRenderParms()
    {
        $parms = parent::getRenderParms();

        if ( $this->getIteratorRef()->count() == 1 ) {
            exit(header('Location: '.$this->getIteratorRef()->get('Url')));
        }

        return $parms;
    }

    function getNoItemsMessage()
    {
        if ( !getSession()->getProjectIt()->IsPortfolio() ) {
            $portfolios = getFactory()->getObject('Portfolio')->getAll()->fieldToArray('CodeName');
            $searchUrl = 'search.php?search-keywords=' . SanitizeUrl::parseUrl($_REQUEST['search-keywords']);
            if (in_array('all', $portfolios)) {
                return str_replace('%1', '/pm/all/' . $searchUrl, text(2308));
            }
            if (in_array('my', $portfolios)) {
                return str_replace('%1', '/pm/my/' . $searchUrl, text(2308));
            }
        }
        return text(2247);
    }
}