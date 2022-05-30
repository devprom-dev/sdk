<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include "persisters/QuestionRequestPersister.php";
include SERVER_ROOT_PATH."pm/classes/tags/persisters/QuestionTagPersister.php";

class QuestionModelExtendedBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
{
    if ( !$object instanceof Question ) return;

    $object->addPersister( new QuestionRequestPersister() );

    $object->addAttribute( 'Tags', 'REF_TagId', translate('Тэги'), false, false, '', 40 );
    $object->addPersister( new QuestionTagPersister() );

    $object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
    $object->addAttributeGroup('RecentComment', 'non-form');
    $object->addPersister( new CommentRecentPersister() );

    $object->addPersister( new AttachmentsPersister() );
}
}