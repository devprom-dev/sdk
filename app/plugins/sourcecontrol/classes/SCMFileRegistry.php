<?php

include_once "SCMDataRegistry.php";

class SCMFileRegistry extends SCMDataRegistry
{
	public function addFile( $type, $modified, $path, $status, $name, $length, $creator, $content_type )
	{
		$content_type = $content_type != '' ? IteratorBase::utf8towin($content_type) : $this->getContentTypeByFileName($name);
 		
		$this->addData( array (
				'Type' => $type,
				'RecordModified' => $modified,
				'Path' => $path,
				'Status' => $status,
				'Name' => $name,
				'Length' => $length,
				'Creator' => IteratorBase::utf8towin($creator),
				'ContentType' => $content_type
		));
	}
	
	public function getAll()
	{
		$data = $this->getData();
		
 		$types = array();
 		$names = array();
 		
 		// sort files in alphabetical order
		foreach ( $data as $key => $row) 
		{
		    $names[$key]  = $row['Name'];
		    $types[$key]  = $row['Type'];
		}
		
		array_multisort($types, SORT_ASC, $names, SORT_ASC, $data);
		
		return $this->createIterator( $data );
	}
}