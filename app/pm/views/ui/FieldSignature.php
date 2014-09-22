<?php

class FieldSignature extends Field
{
    var $date, $author;
    
    function setDate( $date )
    {
        $this->date = $date;    
    }

    function setAuthor( $author )
    {
        $this->author = $author;
    }
    
    function render( $view )
    {
        echo $view->render('pm/FieldSignature.php', array (
            'author' => $this->author,
            'date' => $this->date 
        ));
    }
}