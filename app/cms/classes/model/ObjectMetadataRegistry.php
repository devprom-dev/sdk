<?php

class ObjectMetadataRegistry
{
	public function getMetadata( Metaobject & $object, $cache_category = '' )
	{
	    return $this->buildMetadata($object, $cache_category);
	}
	
	protected function buildMetadata( Metaobject & $object, $cache_category = '' )
	{ 
	    // check the cache
	    $session_key = 'entity-metadata-'.md5($object->getEntityRefName().get_class($object));
		$metadata = getFactory()->getCacheService()->get( $session_key, $cache_category );

		if ( !is_object($metadata) )
		{
			getFactory()->debug( 'Missed metadata cache "'.get_class($object).'" on '.$session_key.' at '.$cache_category );
			
	    	$metadata = new ObjectMetadata($object);
			$metadata->build();

			getFactory()->getCacheService()->set( $session_key, $metadata, $cache_category );
		}
		else
		{
			getFactory()->debug( 'Hit metadata cache "'.get_class($object).'" ' );
		}
		
		return $metadata;
	}
}