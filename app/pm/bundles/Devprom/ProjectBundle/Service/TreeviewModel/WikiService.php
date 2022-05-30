<?php

namespace Devprom\ProjectBundle\Service\TreeviewModel;

include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectAccessibleActiveVpdPredicate.php";

class WikiService
{
	private $object = null;
	private $object_it = null;
	private $root = 0;
	private $crossProject = false;
	
	public function __construct( $class_name, $root, $crossProject = false )
	{
		getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
		
    	$this->object = getFactory()->getObject($class_name);
		$this->root = $root;
		$this->crossProject = $crossProject;

    	$predicates = $this->root > 0
    		? array (
    				new \FilterAttributePredicate('ParentPage', $root)
    			)
    		: ( $crossProject ?
                    array(
                        new \WikiRootFilter(),
                        new \ProjectAccessibleActiveVpdPredicate(),
                        new \FilterNoVpdPredicate(getSession()->getProjectIt()->get('VPD'))
                    ) :
                    array(
                        new \WikiRootFilter(),
                        new \FilterVpdPredicate()
                    )
            );

    	$this->setObjectIt(
			$this->object->getRegistry()->Query(
				array_merge(
					array (
						new \SortProjectSelfFirstClause(),
                        new \SortAttributeClause('Project'),
						new \SortDocumentClause(),
					),
					$predicates
				)
			)
		);
	}

	public function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	public function getObjectIt()
	{
		return $this->object_it;
	}
	
    public function getData()
    {
    	$uid = new \ObjectUID();
    	$data = array();
        $object_it = $this->getObjectIt();

     	while ( !$object_it->end() ) {
            $title = $object_it->get('ParentPage') == ''
                ? $object_it->getDisplayNameExt($object_it->get('UID') . ' ')
                : $object_it->getTreeDisplayName(array('uid'));

    		$selfIt = $object_it;
    		if ( $object_it->get('Includes') != '' ) {
                $selfIt = $object_it->object->getExact($object_it->get('Includes'));
            }
            $uid_info = $uid->getUidInfo($selfIt);
    		if ( $this->root < 1 && $uid_info['alien'] ) $title = '{'.$uid_info['project'].'} ' . $title;

            $data[] = array (
                'title' => $title,
                'folder' => $object_it->get('TotalCount') > 0,
                'key' => $object_it->get('Includes') != '' ?  $object_it->get('Includes') : $object_it->getId(),
                'expanded' => false,
                'lazy' => $object_it->get('TotalCount') > 0,
                'data' => array(
                    'caption' => $object_it->getHtmlDecoded('Caption'),
                    'documentid' => $object_it->get('DocumentId')
                )
            );
 			$object_it->moveNext();
 		}

 		if ( $this->root < 1 && !$this->crossProject ) {
            $data[] = array(
                'title' => text(2505),
                'folder' => true,
                'key' => '',
                'expanded' => false,
                'lazy' => true,
                'unselectable' => true,
                'data' => array(
                    'crossNode' => true
                )
            );
        }

 		return $data;
    }
}