<?php

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class WikiPageMove extends CommandForm
 {
    function validate()
    {
     // proceeds with validation
     $this->checkRequest( array('ObjectClass', 'ParentPage', 'PrevSiblingPage') );

     if (!is_numeric($_REQUEST['ParentPage']) || !is_numeric($_REQUEST['PrevSiblingPage'])) {
        $this->replyError( text(1731) );
     }

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
         global $_REQUEST, $model_factory;


         /** @var WikiPage $object */
         try {
             $object = $model_factory->getObject($_REQUEST['ObjectClass']);

             $prev_sibling_id = intval($_REQUEST['PrevSiblingPage']);

             $orderNum = $this->getOrderNum($object, $prev_sibling_id);

             $object_it = $object->getExact($object_id);

             if ($object_it->count() < 1) {
                 throw new Exception("No wiki page found for id: $prev_sibling_id");
             }

             $object->modify_parms($object_id, array('ParentPage' => $_REQUEST['ParentPage'], 'OrderNum' => $orderNum));

             $this->replySuccess(text(1729), $object_id);
         } catch (Exception $e) {
             $this->replyError( text(1728) );
         }
     }

    protected function getOrderNum($object, $prev_sibling_id)
    {
        if ($prev_sibling_id > 0) {
            /** @var WikiPageIterator $prev_sibling */
            $prev_sibling = $object->getExact($prev_sibling_id);
            if ($prev_sibling->count() < 1) {
                throw new Exception("No wiki page found for id: $prev_sibling_id");
            }

            return $prev_sibling->get('OrderNum') + 1;
        }
        return 1;
    }
}

?>