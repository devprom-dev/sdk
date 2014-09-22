<?php

include "ProjectTemplateSectionsRegistry.php";

class ProjectTemplateSections extends Metaobject
{
 	public function __construct()
 	{
 		parent::__construct( 'entity', new ProjectTemplateSectionsRegistry($this) );
 	}
}
