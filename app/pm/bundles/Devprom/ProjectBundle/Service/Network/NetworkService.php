<?php
namespace Devprom\ProjectBundle\Service\Network;

class NetworkService
{
    const MAX_ITEMS = 100;
    private $object_it = null;
    private $allowedClasses = array();
    private $uidService = null;
    private $referencesVisited = array();
    private $modelBuilders = array();
    private $session = null;
    private $usedItems = 0;
    private $visitedNodes = array();
    private $visitedEdges = array();

    public function __construct( \SessionBase $session, $objectClass, $objectId )
    {
        $this->session = $session;
        foreach( $session->getBuilders('Devprom\ProjectBundle\Service\Network\NetworkServiceModelBuilder') as $builder ) {
            $builder->build($session, $this);
        }

        $objectClass = getFactory()->getClass($objectClass);
        if ( !class_exists($objectClass, false) ) {
            $this->object_it = getFactory()->getObject('entity')->getEmptyIterator();
        }
        else {
            $this->object_it = $this->extendModel(getFactory()->getObject($objectClass))->getExact($objectId);
        }

        $this->uidService = new \ObjectUID();
        $this->allowedClasses = array (
            'Requirement', 'TestScenario', 'HelpPage', 'WikiPage',
            'Request', 'Task', 'TestExecution', 'Commit', 'Question', 'Feature',
            'Issue', 'Increment', 'Component'
        );
    }

    public function addBuilder( $builder ) {
        $this->modelBuilders[] = $builder;
    }

    public function extendModel( $object ) {
        foreach( $this->modelBuilders as $builder ) {
            $builder->build($object);
        }
        return $object;
    }

    public function getVisData()
    {
        $nodes = array();
        $edges = array();
        $this->usedItems = 0;

        $id = get_class($this->object_it->object).$this->object_it->getId();
        $this->buildVisData($this->object_it, $id, 1, $nodes, $edges );

        $nodes[0]['shape'] = 'dot';

        return array (
            'nodes' => $nodes,
            'edges' => $edges
        );
    }

    protected function buildVisData( $object_it, $sourceId, $level, & $nodes, & $edges )
    {
        if ( in_array($sourceId, $this->visitedNodes) ) return false;
        $this->visitedNodes[] = $sourceId;

        $uidInfo = $this->uidService->getUidInfo($object_it);
        $label = $uidInfo['uid'].' '.$object_it->getHtmlDecoded('Caption');
        if ( $object_it->object instanceof \WikiPage && $object_it->get('ParentPage') != '' ) {
            $label = $uidInfo['uid'].' '.$object_it->getHtmlDecoded('DocumentName').' / '.html_entity_decode($object_it->getDisplayName());
        }

        $nodes[] = array (
            'id' => $sourceId,
            'label' => \TextUtils::mb_wordwrap($label, 60),
            'shape' => 'box',
            'group' => get_class($object_it->object),
            'url' => $object_it->getUidUrl(),
            'level' => ($level-1),
            'hidden' => $level > 2 ? true : false
        );

        if ( $level > 6 ) return false;

        $references = array_merge(
            $this->getReferences($object_it),
            $this->getDependencies($object_it)
        );

        foreach( $references as $referenceName => $reference_it )
        {
            while (!$reference_it->end()) {
                $id = get_class($reference_it->object) . $reference_it->getId();
                if ( in_array($sourceId . $id, $this->visitedEdges) ) {
                    $reference_it->moveNext();
                    continue;
                }
                $edges[] = array(
                    'from' => $sourceId,
                    'to' => $id,
                    'label' => $referenceName,
                    'length' => 400,
                    'font' => array(
                        'align' => 'middle'
                    )
                );
                $this->visitedEdges[] = $sourceId . $id;
                $this->visitedEdges[] = $id . $sourceId;
                $this->usedItems++;

                $reference_it->moveNext();
            }
            $reference_it->moveFirst();
        }

        foreach( $references as $referenceName => $reference_it ) {
            while( !$reference_it->end() )
            {
                $id = get_class($reference_it->object).$reference_it->getId();
                $this->buildVisData( $reference_it->copy(), $id, $level + 1, $nodes, $edges );
                $reference_it->moveNext();
            }
        }
        return true;
    }

    protected function getReferences( $object_it )
    {
        $result = array();
        $referencesVisited = $this->referencesVisited;
        $skip = $object_it->object->getAttributesByGroup('skip-network');

        $referencesFound = 0;
        foreach( $object_it->object->getAttributes() as $attribute => $info )
        {
            if ( in_array($attribute, $skip) ) continue;
            if ( !$object_it->object->IsReference($attribute) ) continue;

            $attributeObject = $this->extendModel($object_it->object->getAttributeObject($attribute));
            if ( !getFactory()->getAccessPolicy()->can_read($attributeObject) ) continue;

            $ids = \TextUtils::parseIds($object_it->get($attribute));
            if ( count($ids) < 1 ) continue;

            foreach( $this->allowedClasses as $className )
            {
                if ( !is_a($attributeObject, $className) ) continue;
                $rowset = $attributeObject->getExact($ids)->getRowset();

                $objectClass = get_class($attributeObject);
                $attributeId = $attributeObject->getIdAttribute();

                $rowset = array_filter($rowset, function($row) use($referencesVisited, $attributeId, $objectClass, $attribute) {
                    return !in_array($objectClass.$attribute.$row[$attributeId], $referencesVisited);
                });
                if ( count($rowset) < 1 ) continue;

                $rowset = array_splice($rowset, 0, self::MAX_ITEMS - $this->usedItems);

                foreach( $rowset as $row ) {
                    $referencesVisited[] = $objectClass.$attribute.$row[$attributeId];
                }
                $result[$object_it->object->getAttributeUserName($attribute)]
                    = $attributeObject->createCachedIterator(array_values($rowset));
            }
        }

        $this->referencesVisited = $referencesVisited;
        return $result;
    }

    protected function getDependencies( $object_it )
    {
        $rowset = array();
        foreach( \TextUtils::parseItems($object_it->get('Dependency')) as $object_info ) {
            list($class, $id) = preg_split('/:/',$object_info);
            $class = getFactory()->getClass($class);
            if ( !class_exists($class) ) continue;
            $object = $this->extendModel(getFactory()->getObject($class));
            $rowset[get_class($object)][] = $object->getExact($id)->getData();
        }

        $result = array();
        $title = $object_it->object->getAttributeUserName('Dependency');

        foreach( $rowset as $className => $rowsetData ) {
            $object = getFactory()->getObject($className);
            $result[$title . " [{$object->getDisplayName()}/{$className}]"] =
                $object->createCachedIterator(array_values($rowsetData));
        }

        return $result;
    }
}