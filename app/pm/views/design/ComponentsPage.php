<?php
include "ComponentForm.php";
include "ComponentTable.php";
include "PageSettingComponentBuilder.php";

class ComponentsPage extends PMPage
{
 	function ComponentsPage()
 	{
 	    getSession()->addBuilder( new PageSettingComponentBuilder() );

 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
 		    if ( $this->getFormRef() instanceof ComponentForm ) {
                $this->addInfoSection(new PageSectionAttributes(
                    $this->getFormRef()->getObject(), 'hierarchy', translate('Декомпозиция')));

                $object_it = $this->getObjectIt();
                if( is_object($object_it) && $object_it->getId() > 0 ) {
                    $this->addInfoSection( new PageSectionComments($object_it, $this->getCommentObject()) );
                    $this->addInfoSection( new PMLastChangesSection ( $object_it ) );
                    $this->addInfoSection( new NetworkSection($object_it) );
                }
            }
 		}
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Component');
 	}
 	
 	function getTable() {
		return new ComponentTable( $this->getObject() );
 	}
 	
 	function getEntityForm() {
        return new ComponentForm( $this->getObject() );
 	}

    function getPageWidgets() {
        return array(
            'components-list',
            'components-trace'
        );
    }

    function buildExportIterator( $object, $ids, $iteratorClassName, $queryParms )
    {
        $iterator = $object->getRegistry()->Query(
            array_merge(
                array(
                    new ParentTransitiveFilter($ids),
                    new SortObjectHierarchyClause()
                ),
                $queryParms
            )
        );

        if ( is_subclass_of($iteratorClassName, 'WikiIteratorExport') ) {
            $data = array();
            while( !$iterator->end() ) {
                $data[] = array(
                    'WikiPageId' => $iterator->getId(),
                    'Caption' => $iterator->getDisplayName(),
                    'Content' => $iterator->getHtmlDecoded('Description'),
                    'ContentEditor' => getSession()->getProjectIt()->get('WikiEditorClass'),
                    'UID' => 'X-' . $iterator->getId(),
                    'IsNoIdentity' => 'N',
                    ''
                );
                $iterator->moveNext();
            }
            return getFactory()->getObject('WikiPage')->createCachedIterator($data);
        }

        return $iterator;
    }
}