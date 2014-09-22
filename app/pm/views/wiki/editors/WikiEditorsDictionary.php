<?php

include_once "WikiEditorSet.php"; 

class WikiEditorsDictionary extends FieldDictionary
{
    function __construct()
    {
        parent::FieldDictionary( getSession()->getProjectIt()->object );
    }

    function getOptions()
    {
        $set = new WikiEditorSet();
        
        $editor_it = $set->getAll();
         
        $options = array();
        
        while( !$editor_it->end() )
        {
            $options[] = array ( 
                    'value' => $editor_it->getId(), 
                    'caption' => $editor_it->getDisplayName()
            );
            
            $editor_it->moveNext();
        }
        
        $this->setNullOption( false );
        
        return $options;
     }
 }