<?php

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class WikiPageMove extends CommandForm
 {
    function validate()
    {
     // check authorization was successfull
     return getSession()->getUserIt()->getId() > 0;
    }

    function checkRequest( $fields )
    {
        global $_REQUEST;

        foreach ($fields as $field) {
            if ( trim($_REQUEST[$field]) == '' ) {
                $this->replyError( sprintf(text(1730), $field) );
            }
        }
    }

    function modify( $object_id )
     {
         try {
             $object = getFactory()->getObject('WikiPage');

             $object_it = $object->getExact($object_id);
             if ($object_it->count() < 1) {
                 throw new Exception("No wiki page found for id: $object_id");
             }

             $orderNum = $this->getOrderNum($object, $_REQUEST);

             $object->modify_parms(
                 $object_it->getId(),
                 array(
                     'ParentPage' => $_REQUEST['ParentPage'],
                     'OrderNum' => $orderNum
                 )
             );

             $itemIt = $object->getRegistry()->Query(
                 array(
                     new FilterAttributePredicate('DocumentId', $object_it->get('DocumentId')),
                     new SortDocumentClause()
                 )
             );
             $resultSet = array();
             while( !$itemIt->end() ) {
                 $resultSet[] = array (
                     'id' => $itemIt->getId(),
                     'si' => $itemIt->get('SortIndex'),
                     'sn' => $itemIt->get('SectionNumber')
                 );
                 $itemIt->moveNext();
             }

             echo JsonWrapper::encode($resultSet);
         }
         catch (Exception $e) {
             $this->replyError( text(1728) . '<br/><br/>' . $e->getMessage() );
         }
     }

    protected function getOrderNum($object, $parms)
    {
        if ($parms['before'] > 0) {
            /** @var WikiPageIterator $prev_sibling */
            $prev_sibling = $object->getExact($parms['before']);
            if ($prev_sibling->count() < 1) {
                return 1;
            }
            return max(1, $prev_sibling->get('OrderNum') - 1);
        }
        if ($parms['after'] > 0) {
            /** @var WikiPageIterator $prev_sibling */
            $prev_sibling = $object->getExact($parms['after']);
            if ($prev_sibling->count() < 1) {
                return 1;
            }
            return $prev_sibling->get('OrderNum') + 1;
        }
        return 1;
    }
}
