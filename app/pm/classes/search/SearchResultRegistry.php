<?php
include SERVER_ROOT_PATH."pm/classes/common/predicates/CustomAttributeSearchPredicate.php";

class SearchResultRegistry extends ObjectRegistrySQL
{
    private $wordsMode = FilterSearchAttributesPredicate::WORDS_MODE_ALL;

 	function Query( $parms = array() )
 	{
 	    $searchString = "";
        $searchEntities = array();
        $searchPredicates = array();

        $filters = $this->extractPredicates($parms);
        foreach( $filters as $filter ) {
            if ( $filter instanceof FilterAttributePredicate ) {
                if ( $filter->getAttribute() == 'entityId' ) {
                    $searchEntities = \TextUtils::parseFilterItems($filter->getValue());
                }
            }
            if ( $filter instanceof FilterSearchAttributesPredicate ) {
                $searchString = $filter->getValue();
                $this->wordsMode = $filter->getWordsMode();
                if ( in_array($searchString,array('hide','all')) ) $searchString = '';
            }
            if ( $filter instanceof StateCommonPredicate ) {
                $searchPredicates[] = $filter;
            }
        }
        if ( $searchString == "" ) return $this->getObject()->getEmptyIterator();

        $uid = new ObjectUID;
        $report = getFactory()->getObject('PMReport');
        $module = getFactory()->getObject('Module');
        $searchable = getFactory()->getObject('SearchableObjectSet');
        $searchable_it = $searchable->getAll();

        if ( $this->wordsMode != FilterSearchAttributesPredicate::WORDS_MODE_EXACT ) {
            $search_items = SearchRules::getSearchItems($searchString, getSession()->getLanguageUid());
        }
        else {
            $search_items = array($searchString);
        }

        $data = array();
        $lists = array();
        $results = $this->searchByAttributes($searchString, $searchEntities, $searchPredicates);

        foreach( $results as $item ) {
            $object_it = $item['object'];
            $attributes = $item['attributes'];
            $entityId = get_class($object_it->object);

            if ( $lists[$entityId] == '' ) {
                $searchable_it->moveToId($entityId);
                $report_it = $report->getExact($searchable_it->get('Report'));
                if ( $report_it->getId() == '' ) {
                    $report_it = $module->getExact($searchable_it->get('Report'));
                }
                $lists[$entityId] = $report_it->getUrl('ids='.\TextUtils::buildIds($object_it->idsToArray()));
            }

            while( !$object_it->end() )
            {
                $textsFound = array();
                foreach ( $attributes as $attribute )
                {
                    $value = $object_it->object->getAttributeType($attribute) == 'wysiwyg'
                        ? $object_it->getHtmlDecoded($attribute)
                        : $object_it->get($attribute);

                    if ( $value == "" ) continue;
                    $text = $value;

                    $text = preg_replace(
                        array_map(
                            function($value) {
                                return '#'.preg_quote($value).'#iu';
                            },
                            $search_items
                        ),
                        '<span class="label label-found">\\0</span>',
                        $text
                    );
                    if ( strpos($text, 'label-found') !== false ) {
                        $userName = $object_it->object->getAttributeUserName($attribute);
                        if ( $userName == '' ) $userName = $attribute;
                        $textsFound[] = translate($userName) . ': ' . $text;
                    }
                }

                $data[$entityId][] = array (
                    'ReferenceName' => $object_it->object->getDisplayName(),
                    'entityUrl' => $lists[$entityId],
                    'UID' =>
                        $object_it->object instanceof Widget
                            ? '<a href="'.$object_it->get('url').'">'.translate('Открыть').'</a>'
                            : $uid->getUidWithCaption($object_it),
                    'Caption' => $object_it->getDisplayName(),
                    'Content' => $textsFound,
                    'Url' => $object_it->getUidUrl()
                );
                $object_it->moveNext();
            }
        }

        $rows = array();
        foreach( $data as $item ) {
            $rows = array_merge($rows, $item);
        }
        return $this->createIterator($rows);
 	}

    protected function searchByAttributes( $search, $paramters, $predicates )
    {
        $results = array();

        $uid = new ObjectUID();
        $objectItByUid = $uid->getObjectIt($search);
        if ( $objectItByUid->count() > 0 ) {
            if ( !in_array($objectItByUid->object->getEntityRefName(), array('pm_ChangeRequest','WikiPage')) ) {
                $results[get_class($objectItByUid->object)] = array(
                    'object' => $objectItByUid,
                    'attributes' => array()
                );
            }
        }

        $searchable = getFactory()->getObject('SearchableObjectSet');
        $searchable_it = $searchable->getAll();

        while( !$searchable_it->end() )
        {
            $object = getFactory()->getObject($searchable_it->get('ReferenceName'));
            if (!getFactory()->getAccessPolicy()->can_read($object)) {
                $searchable_it->moveNext();
                continue;
            }

            if ( count($paramters) > 0 && !in_array(get_class($object), $paramters) ) {
                $searchable_it->moveNext();
                continue;
            }

            $parms = array();
            $sorts = array();
            $search = html_entity_decode($search);

            if ($object instanceof WikiPage) {
                $object->setRegistry(new WikiPageRegistryContent($object));
                $parms[] = new DocumentVersionPersister();
            }
            $registry = $object->getRegistry();

            if ( $object instanceof CacheableSet ) {
                $object_it = $registry->Query(
                    array(
                        new FilterSearchAttributesPredicate($search, array('Caption','ReferenceName'), $this->wordsMode)
                    )
                );
                if ( $object_it->count() > 0 ) {
                    $results[$searchable_it->getId()] = array(
                        'object' => $object_it->copyAll(),
                        'attributes' => array('Caption','ReferenceName')
                    );
                }
                $searchable_it->moveNext();
                continue;
            }

            if ( $object instanceof MetaobjectStatable ) {
                $sorts[] = new SortAttributeClause('State');
            }
            $sorts[] = new SortRecentClause();

            if ( strlen($search) > $this->length_constraint ) {
                $object_it = $registry->Query(
                    array_merge(
                        $parms,
                        $predicates,
                        array(
                            new FilterSearchAttributesPredicate($search, $object->getSearchableAttributes(), $this->wordsMode),
                            new FilterVpdPredicate()
                        ),
                        $sorts
                    )
                );
                if ( $object_it->count() > 0 ) {
                    $results[$searchable_it->getId()] = array(
                        'object' => $object->createCachedIterator($object_it->getRowset()),
                        'attributes' => $object->getSearchableAttributes()
                    );
                }
            }

            $searchable_it->moveNext();
        }

        return $results;
    }
}