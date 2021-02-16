<?php

class TagIterator extends OrderedIterator
{
    function get( $attr )
    {
        switch( $attr )
        {
            case 'Caption':
                $caption = parent::get( $attr );
                if( $caption != '' ) return $caption;

                $tag = getFactory()->getObject('Tag');

                $tag_name = parent::get( 'Tag' );
                $tag_it = is_numeric($tag_name) && $tag_name > 0
                    ? $tag->getByRef('TagId', $tag_name) : $tag->getEmptyIterator();

                return $tag_it->getId() > 0 ? $tag_it->get('Caption') : $tag_name;

            default:
                return parent::get( $attr );
        }
    }

	function getViewUrl() {
		return $this->object->getPageNameViewMode($this->get('TagId'));
	}
}