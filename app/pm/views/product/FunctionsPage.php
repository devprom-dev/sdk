<?php
include_once SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";
include "FunctionForm.php";
include "FunctionSearchIssueForm.php";
include "FunctionTable.php";
include "PageSettingFeatureBuilder.php";

class FunctionsPage extends PMPage
{
 	function FunctionsPage()
 	{
 	    getSession()->addBuilder( new PageSettingFeatureBuilder() );
        getSession()->addBuilder( new FeatureModelExtendedBuilder() );

 		parent::__construct();
 		
 		if ( $this->needDisplayForm() )
 		{
 		    if ( $this->getFormRef() instanceof FunctionForm ) {
                $this->addInfoSection(new PageSectionAttributes($this->getFormRef()->getObject(), 'hierarchy', translate('Декомпозиция')));
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
 		return getFactory()->getObject('Feature');
 	}
 	
 	function getTable() {
		return new FunctionTable( $this->getObject() );
 	}
 	
 	function getEntityForm()
 	{
 	    if ( $_REQUEST['BindIssue'] != '' ) {
 	        $object = $this->getObject();
            $object->resetAttributeGroup('Request', 'trace');
            return new FunctionSearchIssueForm($object);
        }
        else {
            return new FunctionForm( $this->getObject() );
        }
 	}

    function needDisplayForm() {
        return in_array($_REQUEST['view'], array('import')) ? true : parent::needDisplayForm();
    }

    function getPageWidgets() {
        return array('features-list');
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
                    'UID' => 'F-' . $iterator->getId(),
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