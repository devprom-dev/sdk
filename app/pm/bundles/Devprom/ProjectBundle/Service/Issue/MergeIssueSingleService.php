<?php
namespace Devprom\ProjectBundle\Service\Issue;
use Devprom\ProjectBundle\Service\Tooltip\TooltipProjectService;

class MergeIssueSingleService extends MergeIssueService
{
    function run( $targetIssueIt, $duplicateIt )
    {
        $commentRegistry = getFactory()->getObject('Comment')->getRegistry();
        $commentRegistry->getObject()->setNotificationEnabled(false);

        $commentRoots = array();
        $duplicateIt->moveFirst();
        while( !$duplicateIt->end() ) {
            $commentRoots[$duplicateIt->getId()] = $commentRegistry->Create(
                    array(
                        'ObjectId' => $targetIssueIt->getId(),
                        'ObjectClass' => get_class($targetIssueIt->object),
                        'AuthorId' => getSession()->getUserIt()->getId(),
                        'Caption' => $this->transformToHtml($duplicateIt)
                    )
                )->getId();
            $duplicateIt->moveNext();
        }

        $references = getFactory()->getModelReferenceRegistry()
            ->getBackwardReferences($targetIssueIt->object);

        $references['Attachment::ObjectId'] = 'RequestAttachment';
        $references['Comment::ObjectId'] = 'RequestComment';
        $references['Watcher::ObjectId'] = 'RequestWatcher';

        $dupIds = $duplicateIt->idsToArray();
        foreach ( $references as $attribute_path => $class_name )
        {
            $parts = preg_split('/::/', $attribute_path);
            $requestAttribute = $parts[1];

            $referenceObject = getFactory()->getObject($class_name);
            if ( !$referenceObject->IsPersistable() ) continue;
            if ( !$referenceObject->IsAttributeStored($requestAttribute) ) continue;

            \Logger::getLogger('Commands')->error(var_export($class_name . ',' . $requestAttribute,true));

            $referenceRegistry = $referenceObject->getRegistryBase();
            $refIt = $referenceRegistry->Query(
                array (
                    new \FilterAttributePredicate($requestAttribute, $dupIds)
                )
            );

            while( !$refIt->end() ) {
                if ( $refIt->get($requestAttribute) != $targetIssueIt->getId() ) {
                    $parms = array(
                        $requestAttribute => $targetIssueIt->getId()
                    );
                    if ( $refIt->object instanceof \RequestComment && $refIt->get('PrevComment') == '' ) {
                        $parms['PrevComment'] = $commentRoots[$refIt->get($requestAttribute)];
                    }
                    $referenceRegistry->Store( $refIt, $parms);
                }
                $refIt->moveNext();
            }
        }

        $duplicateIt->moveFirst();
        while( !$duplicateIt->end() ) {
            $duplicateIt->object->delete($duplicateIt->getId());
            $duplicateIt->moveNext();
        }
    }

    protected function transformToHtml( $objectIt )
    {
        $service = new TooltipProjectService( get_class($objectIt->object), $objectIt->getId(), false );

        $data = $service->getData();
        $html = '<b>UID</b>: ' . $data['type']['uid'];

        foreach( $data as $key => $section ) {
            switch( $key ) {
                case 'attributes':
                    foreach( $section as $attribute ) {
                        $html .= '<br/><b>'.$attribute['title'].'</b>: ';
                        switch( $attribute['type'] ) {
                            case 'wysiwyg':
                                $html .= $attribute['text'];
                                $html .= '<br/>';
                                break;
                            default:
                                $html .= $attribute['text'];
                        }
                    }
                    break;
                case 'lifecycle':
                    $html .= '<br/><b>'.$section['name'].'</b>: ' . $section['data']['state'];
                    break;
                case 'type':
                    $html .= '<br/><b>'.translate('Тип').'</b>: ' . $section['name'];
                    break;
            }
        }
        return $html;
    }
}