<?php
namespace Devprom\MobileAppBundle\Service\App;

class MobileViewModelService
{
    function getTabsData()
    {
        $tabs = array(
            array(
                'id' => 'whatsnew',
                'title' => text(2458),
                'short' => translate('Новости'),
                'icon' => 'newspaper',
                'class' => 'app-tab-active app-tab-home',
                'url' => '/mobile/whatsnew',
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'whatsnew-item-tpl'
            ),
            array(
                'id' => 'work',
                'title' => text(2473),
                'short' => translate('Работы'),
                'icon' => 'stack',
                'class' => '',
                'url' => '/mobile/workitems',
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'work-item-tpl',
                'newissueurl' => '/mobile/form/Request/0',
                'newtaskurl' => '/mobile/form/Task/0'
            ),
            array(
                'id' => 'messages',
                'title' => text(2807),
                'short' => text(980),
                'icon' => 'line-bubble',
                'class' => '',
                'url' => '/mobile/comments',
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'comments-item-tpl',
                'newdiscurl' => '/mobile/form/Question/0'
            ),
            array(
                'id' => 'team',
                'title' => text(2900),
                'short' => translate('Люди'),
                'icon' => 'material-people',
                'selfUrl' => '/mobile',
                'url' => '/mobile/people',
                'view' => 'cardview'
            ),
            array(
                'id' => 'projects',
                'title' => translate('Мои проекты'),
                'short' => translate('Проекты'),
                'icon' => 'map',
                'selfUrl' => '/mobile',
                'view' => 'cardview',
                'url' => '/mobile/projects'
            )
        );

        $className = getFactory()->getClass('Build');
        if ( class_exists($className) ) {
            $tabs[] = array(
                'id' => 'build',
                'title' => translate('Сборки'),
                'short' => translate('Сборки'),
                'icon' => 'line-paperplane',
                'url' => '/mobile/builds',
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'build-item-tpl'
            );
        }

        $className = getFactory()->getClass('TestExecution');
        if ( class_exists($className) ) {
            $tabs[] = array(
                'id' => 'test',
                'title' => translate('Тесты'),
                'short' => translate('Тесты'),
                'icon' => 'meteo-thermometer',
                'url' => '/mobile/tests',
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'test-item-tpl'
            );
        }

        $tabEntities = array(
            'ProjectPage', 'Requirement', 'TestScenario', 'HelpPage'
        );
        foreach( $tabEntities as $entity ) {
            if ( !class_exists($entity) ) continue;
            $object = getFactory()->getObject($entity);
            $tabs[] = array(
                'id' => strtolower($entity),
                'title' => $object->getSetDisplayName(),
                'short' => $object->getSetDisplayName(),
                'icon' => 'ion-ios7-information-outline',
                'url' => '/mobile/wiki/' . get_class($object),
                'selfUrl' => '/mobile',
                'view' => 'listview',
                'template' => 'wiki-item-tpl'
            );
        }

        return $tabs;
    }

    function getFormData( $objectClass, $objectId, $project )
    {
        $className = getFactory()->getClass($objectClass);
        if ( !class_exists($className) ) return array();

        if ( $project != '' ) {
            \SessionBuilderProject::Instance()->openSession(array('project' => $project));
        }

        $modelService = new MobileDataService(0);
        $object = $modelService->buildObject($className);

        $data = array(
            '' => array(
                'fields' => array()
            ),
            'deadlines' => array(
                'title' => translate('Сроки'),
                'fields' => array()
            ),
            'additional' => array(
                'title' => translate('Дополнительно'),
                'fields' => array()
            ),
            'trace' => array(
                'title' => translate('Трассировки'),
                'fields' => array()
            )
        );

        $groups = array_keys($data);
        $objectIt = $object->getExact($objectId);

        $editor = \WikiEditorBuilder::build();
        $parser = $editor->getHtmlParser();
        $parser->setObjectIt($objectIt);

        $attributes = array_diff(
            array_keys($object->getAttributes()),
            $object->getAttributesByGroup('system')
        );
        if ( $objectIt->getId() != '' ) {
            $attributes = array_diff( $attributes,
                $object->getAttributesByType('wysiwyg')
            );
        }

        foreach( $attributes as $attribute ) {
            if ( !$object->IsAttributeVisible($attribute) ) continue;
            $group = array_shift(
                array_intersect(
                    $object->getAttributeGroups($attribute),
                    $groups
                )
            );
            $field = array(
                'name' => $attribute,
                'title' => $object->getAttributeUserName($attribute),
                'editable' => var_export($object->getAttributeEditable($attribute), true),
                'type' => $object->getAttributeType($attribute),
                'default' => $object->getDefaultAttributeValue($attribute),
                'value' => $objectIt->getId() != ''
                            ? $objectIt->getHtmlDecoded($attribute)
                            : $object->getDefaultAttributeValue($attribute)
            );

            if ( $field['type'] == 'float' && in_array('hours',$object->getAttributeGroups($attribute)) ) {
                $field['value'] = getSession()->getLanguage()->getHoursWording($field['value']);
                $field['editable'] = 'false';
            }

            if ( $field['type'] == 'wysiwyg' ) {
                $field['type'] = 'text';
            }

            if ( $object->IsReference($attribute) ) {
                $ref = $object->getAttributeObject($attribute);
                if ( $ref->IsDictionary() ) {
                    $foundValue = false;
                    $refIt = $ref->getRegistry()->Query(
                        array(
                            new \FilterVpdPredicate($objectIt->get('VPD') != '' ? $objectIt->get('VPD') : 'self')
                        )
                    );
                    while( !$refIt->end() ) {
                        $field['options'][] = array(
                            'value' => $refIt->getId(),
                            'title' => $refIt->getHtmlDecoded('Caption')
                        );
                        if ( $refIt->getId() == $objectIt->get($attribute) ) {
                            $foundValue = true;
                        }
                        $refIt->moveNext();
                    }
                    if ( !$foundValue ) {
                        $field['options'][] = array(
                            'value' => $refIt->getId(),
                            'title' => $objectIt->getRef($attribute)->getHtmlDecoded('Caption')
                        );
                    }
                    $field['type'] = 'select';
                }
                else {
                    $refIt = $objectIt->getRef($attribute);
                    $names = array();
                    while( !$refIt->end() ) {
                        $names[] = $refIt->getHtmlDecoded('Caption');
                        $refIt->moveNext();
                    }
                    $field['value'] = join(', ', $names);
                    $field['editable'] = 'false';
                    $field['type'] = count($names) > 1 ? 'text' : 'varchar';
                }
            }

            if ( $field['editable'] == 'true' || $field['value'] != '' ) {
                $data[$group]['fields'][] = $field;
            }
        }

        if ( $objectIt->getId() != '' ) {
            if ( $object instanceof \MetaobjectStatable ) {
                $data['']['fields'][] = array(
                    'name' => 'State',
                    'title' => translate('Состояние'),
                    'editable' => 'false',
                    'type' => 'varchar',
                    'value' => $objectIt->getStateIt()->get('Caption')
                );
            }

            foreach( $object->getAttributesByType('wysiwyg') as $attribute ) {
                if ( !$object->IsAttributeVisible($attribute) ) continue;
                $data[$attribute] = array(
                    'title' => $object->getAttributeUserName($attribute),
                    'fields' => array(
                        array(
                            'type' => 'wysiwyg',
                            'value' => $parser->parse($objectIt->getHtmlDecoded($attribute))
                        )
                    )
                );
            }

            $comment = getFactory()->getObject('Comment');
            $comment->setSortDefault(array(
                new \SortKeyClause()
            ));
            $commentIt = $comment->getAllForObject($objectIt);

            if ( $commentIt->count() > 0 ) {
                $comments = array();
                while( !$commentIt->end() ) {
                    $comments[] = array(
                        'id' => 'c' . $commentIt->getId(),
                        'title' => $commentIt->get('AuthorName'),
                        'details' => $parser->parse($commentIt->getHtmlDecoded('Caption')),
                        'userpic' => $modelService->getUserPicUrl($commentIt->get('AuthorPhotoId')),
                        'when' => $commentIt->getDateFormattedShort('RecordCreated') . ' ' . $commentIt->getTimeFormat('RecordCreated'),
                        'url' => '/mobile/comment/Comment/' . $commentIt->getId()
                    );
                    $commentIt->moveNext();
                }
                $data['comments'] = array(
                    'title' => translate('Комментарии'),
                    'fields' => array(
                        array(
                            'type' => 'comments',
                            'comments' => $comments
                        )
                    )
                );
            }
        }

        if ( $objectIt->getId() != '' ) {
            $uid = new \ObjectUID;
            $title = $uid->getObjectUid($objectIt);
        }
        else {
            $title = $object->getDisplayName();
        }

        return array(
            'id' => $objectIt->getId(),
            'tabs' => $this->getTabsData(),
            'groups' => array_filter(
                    array_values($data),
                    function($group) {
                        return count($group['fields']) > 0;
                    }
                ),
            'header' => $title,
            'url' => '/mobile/form/' . get_class($objectIt->object) . '/'
                        . ($objectIt->getId() != '' ? $objectIt->getId() : '0')
                            . '?project=' . $project,
            'commentUrl' => '/mobile/comment/'
                                . get_class($objectIt->object) . '/' . $objectIt->getId()
        );
    }

    function getProjectsData()
    {
        $data = array();
        $projectIt = getFactory()->getObject('ProjectAccessibleActive')->getAll();
        while( !$projectIt->end() ) {
            $data[] = array(
                'title' => $projectIt->getDisplayName(),
                'code' => $projectIt->get('CodeName')
            );
            $projectIt->moveNext();
        }
        return $data;
    }
}