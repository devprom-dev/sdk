<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/WysiwygEmbedImagesPersister.php";

class WysiwygMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
		$skip = !$metadata->hasAttributesOfType('wysiwyg')
			&& !$metadata->getObject() instanceof WikiPage
			&& !$metadata->getObject() instanceof Comment
			&& !$metadata->getObject() instanceof BlogPost
			&& !$metadata->getObject() instanceof TestCaseExecution;
		if ($skip) return;

		$metadata->addPersister(new WysiwygEmbedImagesPersister());
	}
}
