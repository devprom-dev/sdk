<?php
include SERVER_ROOT_PATH."pm/classes/common/predicates/CustomAttributeSearchPredicate.php";

class SearchResultRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	    $searchString = "";
        $searchEntities = array();
        $searchPredicates = array();

        foreach( $this->getFilters() as $filter ) {
            if ( $filter instanceof FilterAttributePredicate ) {
                if ( $filter->getAttribute() == 'Caption' ) {
                    $searchString = $filter->getValue();
                    if ( in_array($searchString,array('hide','all')) ) $searchString = '';
                }
                if ( $filter->getAttribute() == 'entityId' ) {
                    $searchEntities = array_filter(
                            preg_split('/[,-]/', $filter->getValue()),
                            function ($value) {
                                return !in_array($value, array('','all','hide'));
                            }
                        );
                }
            }
            if ( $filter instanceof StateCommonPredicate ) {
                $searchPredicates[] = $filter;
            }
        }
        if ( $searchString == "" ) return $this->getObject()->getEmptyIterator();

        $uid = new ObjectUID;
        $report = getFactory()->getObject('PMReport');
        $searchable = getFactory()->getObject('SearchableObjectSet');
        $searchable_it = $searchable->getAll();
        $search_items = SearchRules::getSearchItems($searchString, getSession()->getLanguageUid());

        $data = array();
        $lists = array();
        $results = array_merge(
            $this->searchByUid($searchString),
            $this->searchByAttributes($searchString, $searchEntities, $searchPredicates)
        );

        foreach( $results as $item ) {
            $object_it = $item['object'];
            $attributes = $item['attributes'];
            $entityId = get_class($object_it->object);

            if ( $lists[$entityId] == '' ) {
                $searchable_it->moveToId($entityId);
                $report_it = $report->getExact($searchable_it->get('Report'));
                $lists[$entityId] = $report_it->getUrl(strtolower($entityId).'='.\TextUtils::buildIds($object_it->idsToArray()));
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
                    $textsFound[] = translate($object_it->object->getAttributeUserName($attribute)) . ': ' . $text;
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
                    'Url' => $object_it->getViewUrl()
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
                        new FilterSearchAttributesPredicate($search, array('Caption','ReferenceName'))
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

            if ( is_numeric($search) ) {
                $object_it = $registry->Query(
                    array_merge(
                        $parms,
                        array(
                            new FilterInPredicate($search),
                            new FilterVpdPredicate()
                        ),
                        $sorts
                    )
                );
                if ( $object_it->getId() != '' ) {
                    $results[$searchable_it->getId()] = array (
                        'object' => $object->createCachedIterator($object_it->getRowset())
                    );
                }
            }

            if ( strlen($search) > $this->length_constraint ) {
                $object_it = $registry->Query(
                    array_merge(
                        $parms,
                        $predicates,
                        array(
                            new FilterSearchAttributesPredicate($search, $searchable_it->get('attributes')),
                            new FilterVpdPredicate()
                        ),
                        $sorts
                    )
                );
                if ( $object_it->count() > 0 ) {
                    $results[$searchable_it->getId()] = array(
                        'object' => $object->createCachedIterator($object_it->getRowset()),
                        'attributes' => $searchable_it->get('attributes')
                    );
                }
                $object_it = $registry->Query(
                    array_merge(
                        $parms,
                        $predicates,
                        array(
                            new CustomAttributeSearchPredicate($search, $searchable_it->get('attributes')),
                            new FilterVpdPredicate()
                        ),
                        $sorts
                    )
                );
                if ( $object_it->count() > 0 ) {
                    $results[$searchable_it->getId()] = array(
                        'object' => $object->createCachedIterator($object_it->getRowset()),
                        'attributes' => array_filter(array_keys($object->getAttributes()), function($key) use ($object) {
                            return $object->getAttributeOrigin($key) == ORIGIN_CUSTOM;
                        })
                    );
                }
            }

            $searchable_it->moveNext();
        }

        return $results;
    }

    protected function searchByUid( $uid )
    {
        $matches = array();
        if ( !preg_match('/([A-Z]{1}-[0-9]+)/', trim(strtoupper($uid)), $matches) ) return array();

        $uid = $matches[1];
        $results = array();
        $searchable = getFactory()->getObject('SearchableObjectSet');
        $searchable_it = $searchable->getAll();

        while( !$searchable_it->end() ) {
            $object = getFactory()->getObject($searchable_it->get('ReferenceName'));
            if ( $object instanceof WikiPage ) {
                $registry = $object->getRegistryBase();
                $object_it = $registry->Query(
                    array(
                        new DocumentVersionPersister(),
                        new FilterTextExactPredicate('UID', $uid),
                        new FilterVpdPredicate(),
                        new SortRecentClause()
                    )
                );
                if ( $object_it->count() > 0 ) {
                    $results[$searchable_it->getId()] = array(
                        'object' => $object->createCachedIterator($object_it->getRowset()),
                        'attributes' => array('UID', 'Caption')
                    );
                }
            }
            $searchable_it->moveNext();
        }
        if ( count($results) > 0 ) return $results;

        $object_uid = new ObjectUid;
        if ( !$object_uid->isValidUid($uid) ) return array();

        $object_it = $object_uid->getObjectIt($uid);
        return array(
            array (
                'object' => $object_it,
                'attributes' => array('UID', 'Caption')
            )
        );
    }
}