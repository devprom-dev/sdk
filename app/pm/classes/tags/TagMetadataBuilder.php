<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/TagKnowledgeBasePersister.php";
include "persisters/TagFeaturePersister.php";
include "persisters/TagQuestionPersister.php";
include "persisters/TagRequestPersister.php";
include "persisters/TagParentPersister.php";

class TagMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Tag) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        $metadata->addAttribute('Requests', 'REF_pm_ChangeRequestId',
            getSession()->IsRDD() ? text(2032) : text(808), true);
        $metadata->addPersister( new TagRequestPersister() );

        if ( $methodology_it->HasFeatures() )
        {
            $metadata->addAttribute('Features', 'REF_pm_FunctionId', translate('Функции'), true);
            $metadata->addPersister( new TagFeaturePersister() );
        }

        $metadata->addAttribute('Questions', 'REF_pm_QuestionId', text(980), true);
        $metadata->addPersister( new TagQuestionPersister() );

        $metadata->addAttribute('KnowledgeBase', 'REF_ProjectPageId', translate('База знаний'), true);
        $metadata->addPersister( new TagKnowledgeBasePersister() );
    }
}