<?php
include "CommentsList.php";

class CommentsTable extends PMPageTable
{
	function getList() {
		return new CommentsList( $this->getObject() );
	}

	function getObjectIt()
    {
        $className = getFactory()->getClass($_REQUEST['objectclass']);
        if ( !class_exists($className) ) return $this->getObject()->getEmptyIterator();
        return getFactory()->getObject($className)->getExact($_REQUEST['objectid']);
    }

	function getFilterPredicates( $values )
	{
	    $objectIt = $this->getObjectIt();
	    if ( $objectIt->getId() == '' ) return array(
            new FilterInPredicate('0')
        );

	    if ( $objectIt->object instanceof WikiPage ) {
            $objectIt = $objectIt->object->getRegistryBase()->Query(
                    array(
                        new ParentTransitiveFilter($objectIt->getId())
                    )
                );
        }

		return array(
			new CommentObjectFilter($objectIt),
            new FilterAttributePredicate('Closed', 'N'),
            new CommentRootFilter()
		);
	}

    function getTemplate() {
        return "core/PageTableDetailsBody.php";
    }

    function drawScripts()
    {
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                var item = $('.details-header a[did=comments][active-item]');
                if ( item.length > 0 ) {
                    filterItemComments(item.attr('active-item'));
                }
                var item = $('.details-header a[did=comments][active-object]');
                if ( item.length > 0 ) {
                    filterExactComment(item.attr('active-object'));
                }
            });
        </script>
        <?php
    }
}