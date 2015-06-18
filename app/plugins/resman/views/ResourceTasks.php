<?php

class ResourceTasks
{
    static function export()
    {
        global $_REQUEST, $model_factory;
         
        if ( $_REQUEST['export'] != 'tasks' ) return false;
        
        header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0
        header('Content-type: text/html; charset='.APP_ENCODING);

        $uid = new ObjectUID;

        $task = $model_factory->getObject('Task');
        
        $task->disableVpd();
        
        $items = array_filter(array_values(preg_split('/,/', $_REQUEST['objects'])), function( $value ) {
		    return $value > 0;
		});
        
        $task_it = count($items) > 0 ? $task->getExact($items) : $task->getEmptyIterator();

        $limit = 10;
        $passed = 0;

        while( !$task_it->end() && $passed < $limit )
        {
            $uid->drawUidIcon( $task_it, false );

            echo ' '.$task_it->getWordsOnlyValue($task_it->getDisplayName(), 8);
            echo '<br/>';

            $passed++;

            $task_it->moveNext();
        }

        if ( $passed >= $limit )
        {
            echo '...';
        }
        
        return true;
    }
}

