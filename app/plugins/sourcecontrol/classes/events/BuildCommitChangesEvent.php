<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
include_once SERVER_ROOT_PATH."ext/diff/finediff.php";

class BuildCommitChangesEvent extends SystemTriggersBase
{
    private $session;
    
    function __construct( PMSession $session )
    {
        $this->session = $session;
        parent::__construct();
    }
    
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_ADD ) return;
	    if ( !$object_it->object instanceof SubversionRevision ) return;

		$this->storeChanges($object_it);
	}

	function storeChanges( $object_it )
	{
		$connector = $object_it->getRef('Repository')->getConnector();
		$changes = getFactory()->getObject('SCMFileChange');

		$file_it = $connector->getVersionFiles($object_it->get('Version'));
		while( !$file_it->end() )
		{
			$log_it = $connector->getFileLogs($file_it->get('Path'), 0, $object_it->get('Version'));
            $version = $log_it->get('Version');
			$log_it->moveNext();
            $preversion = $log_it->get('Version');

            $was_text = preg_replace('/[\r\n]+/', PHP_EOL,
                $connector->getTextFile($file_it->get('Path'), $preversion));

            $now_text = preg_replace('/[\r\n]+/', PHP_EOL,
                $connector->getTextFile($file_it->get('Path'), $version));

			$diff = new FineDiff(
                $was_text,
                $now_text,
				array(
					FineDiff::paragraphDelimiters,
					FineDiff::sentenceDelimiters,
					FineDiff::wordDelimiters
				)
			);

			$deleted = 0;
			$inserted = 0;
            $copy_pos = 0;
			foreach( $diff->getOps() as $op ) {
                if ( $op instanceof FineDiffCopyOp ) {
                    $copy_pos += $op->getToLen();
                }
				if ( $op instanceof FineDiffDeleteOp ) {
                    $text_deleted = substr($was_text, $copy_pos, $op->getFromLen());
					$deleted += $this->countLines($text_deleted);
				}
				if ( $op instanceof FineDiffInsertOp ) {
					$inserted += $this->countLines($op->getText());
				}
				if ( $op instanceof FineDiffReplaceOp ) {
                    $inserted += $this->countLines($op->getText());
				}
			}

			$changes->add_parms(
				array (
					'Repository' => $object_it->get('Repository'),
					'Revision' => $object_it->getId(),
					'Author' => $object_it->get('Author'),
					'FilePath' => $file_it->get('Path'),
					'Inserted' => $inserted,
					'Deleted' => $deleted,
					'Modified' => $inserted+$deleted
				)
			);

			$file_it->moveNext();
		}
	}

    function countLines( $text ) {
        return count(
            array_filter(
                preg_split('/[\r\n]+/', trim($text, ' \r\n')), function( $text ) {
                    return trim($text) != '';
        }));
    }

 	function info( $message )
 	{
		try {
			Logger::getLogger('SCM')->info($message);
		}
		catch( Exception $e ) {
		}
 	}
}
