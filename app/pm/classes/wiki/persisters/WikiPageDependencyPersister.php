<?php
include_once SERVER_ROOT_PATH."pm/classes/wiki/persisters/WikiPageUsedByPersister.php";
include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";

class WikiPageDependencyPersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
		$uids = array();
		$matches = array();

        if ( !array_key_exists('Content', $parms) ) return;

		preg_match_all(REGEX_INCLUDE_PAGE, $parms['Content'], $matches);

		$uids = array_merge($uids, $matches[1]);
		if ( count($uids) > 0 ) {
            $first = array_shift(array_values($uids));
            if ( $parms['Content'] == '{{'.$first.'}}' ) {
                $parms['Includes'] = array_pop(preg_split('/-/', $first));
            }
		}

		preg_match_all(REGEX_UID, $parms['Content'], $matches);
		$uids = array_merge($uids, $matches[2]);

		preg_replace_callback(REGEX_UPDATE_UID,
			function($match) use (&$uids) {
				$url_parts = parse_url($match[0]);
				$uids[] = basename($url_parts['path']);
				return '';
			},
			$parms['Content']
		);

		$uids = array_unique(
		    array_filter(
		        array_map(function($value) {
			        return trim($value, '[]{};,.()-');
		        }, $uids),
            function($value) {
                return $value != '';
            })
        );

		$uidService = new ObjectUID();
		$objects = array();
		foreach( $uids as $uid ) {
		    if ( !$uidService->isValidUid($uid) ) {
		        $objectIt = $this->getObject()->getRegistryBase()->Query(
		            array(
		                new FilterAttributePredicate('UID', $uid)
                    )
                );
		        if ( $objectIt->getId() != '' ) {
                    $objects[] = get_class($this->getObject()).':'.$objectIt->getId();
                }
            }
		    else {
                $objects[] = $uidService->getClassNameByUid($uid).':'.array_pop(preg_split('/-/', $uid));
            }
		}

		$parms['Dependency'] = join(',',$objects);
	}

	function beforeDelete($object_it)
	{
		if ( $object_it->getId() == '' ) return;

		$registry = new WikiPageRegistryContent($this->getObject());
		$object_it = $registry->Query(
			array (
				new FilterInPredicate($object_it->getId()),
				new WikiPageUsedByPersister()
			)
		);
        $usedIds = array_filter(
            preg_split('/,/', $object_it->get('UsedBy')),
            function($value) {
                return $value != '';
            }
        );
		if ( count($usedIds) < 1 ) return;

		$this->wikiTrace = getFactory()->getObject('WikiPageTrace');
		$this->issueTrace = getFactory()->getObject('RequestTraceBase');

		$usedby_it = $registry->Query (
		    array(
                new FilterInPredicate($usedIds)
            )
		);

		while( !$usedby_it->end() ) {
			if ( preg_match(REGEX_INCLUDE_PAGE, $usedby_it->getHtmlDecoded('Content'), $matches) && $matches[1] != '' ) {
                $refId = array_pop(preg_split('/-/', $matches[1]));
                if ( $refId == $object_it->getId() ) {
                    $usedby_it->object->modify_parms($usedby_it->getId(),
                        array (
                            'Content' => $object_it->getHtmlDecoded('Content'),
                            'Includes' => 'NULL'
                        )
                    );
                    $this->copyTraces($object_it, $usedby_it->getId());
                }
			}
			$usedby_it->moveNext();
		}
	}

	protected function copyTraces( $include_it, $id )
	{
		$trace_it = $this->wikiTrace->getRegistry()->Query(
			array (
				new FilterAttributePredicate('TargetPage', $include_it->getId())
			)
		);
		while( !$trace_it->end() ) {
			$this->wikiTrace->add_parms(
				array (
					'SourcePage' => $trace_it->get('SourcePage'),
					'TargetPage' => $id
				)
			);
			$trace_it->moveNext();
		}
		$trace_it = $this->issueTrace->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectId', $include_it->getId())
			)
		);
		while( !$trace_it->end() ) {
			$this->issueTrace->add_parms(
				array (
					'ChangeRequest' => $trace_it->get('ChangeRequest'),
					'ObjectClass' => $trace_it->get('ObjectClass'),
					'ObjectId' => $id
				)
			);
			$trace_it->moveNext();
		}
	}

	function IsPersisterImportant() {
        return true;
    }
}
