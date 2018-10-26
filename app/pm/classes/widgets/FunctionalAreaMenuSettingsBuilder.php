<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuSettingsBuilder extends FunctionalAreaMenuProjectBuilder
{
    const AREA_UID = 'stg';
    
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);
    	
		$module = getFactory()->getObject('Module');
		
		$items = array();

        $module_it = $module->getExact('profile');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
    		$items[] = $module_it->buildMenuItem();
		}

        $module_it = $module->getExact('project-settings');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $items[] = $module_it->buildMenuItem();
        }

		$module_it = $module->getExact('methodology');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
    		$items[] = $module_it->buildMenuItem();
		}

		$module_it = $module->getExact('dicts-pmcustomattribute');
    	if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
    		$items[] = $module_it->buildMenuItem();
		}

	    $menus['quick']['items'] = array_merge($items, $menus['quick']['items']);

        if ( !getSession()->getProjectIt()->IsPortfolio() ) {

            $items = array();

            $object_it = getFactory()->getObject('Workflow')->getAll();

            while (!$object_it->end()) {
                $module_it = $module->getExact('workflow-' . strtolower($object_it->getId()));
                $items[$object_it->getId()] = $module_it->buildMenuItem();
                $object_it->moveNext();
            }
            $items[] = $module->getExact('autoactions')->buildMenuItem();

            $menus['workflow'] = array(
                'name' => translate('Состояния'),
                'uid' => 'workflow',
                'items' => $items
            );

            $template_classes = array(
                'TextTemplate', 'RequestTemplate', 'ExportTemplate'
            );
            $object_it = getFactory()->getObject('Dictionary')->getAll();

            $items = array('');

            while (!$object_it->end()) {
                if (!in_array($object_it->getId(), $template_classes)) {
                    $object_it->moveNext();
                    continue;
                }
                $items[$object_it->getId()] = $module->getExact('dicts-' . strtolower($object_it->getId()))->buildMenuItem();
                $object_it->moveNext();
            }

            $menus['templates'] = array(
                'name' => text(2622),
                'uid' => 'templates',
                'items' => $items
            );

            $items = array();

            $object_it->moveFirst();
            while (!$object_it->end()) {
                if (in_array($object_it->getId(), $template_classes)) {
                    $object_it->moveNext();
                    continue;
                }
                $module_uid = 'dicts-' . strtolower($object_it->getId());
                if (in_array($module_uid, array('dicts-pmcustomattribute'))) {
                    $object_it->moveNext();
                    continue;
                }
                $module_it = $module->getExact($module_uid);
                $items[$object_it->getId()] = $module_it->buildMenuItem();
                $object_it->moveNext();
            }

            $menus['dicts'] = array(
                'name' => translate('Справочники'),
                'uid' => 'dicts',
                'items' => $items
            );

            $items = array();

            $module_it = $module->getExact('tags');

            if (getFactory()->getAccessPolicy()->can_read($module_it)) {
                $items[] = $module_it->buildMenuItem();
            }

            if ( getSession()->getUserIt()->IsAdministrator() ) {
                $items[] = array(
                    'name' => translate('Администрирование'),
                    'url' => '/admin',
                    'uid' => 'admin',
                    'description' => text(2631)
                );
            }

            $menus['more'] = array(
                'name' => translate('Дополнительно'),
                'uid' => 'more',
                'items' => $items
            );
        }

 	    $set->setAreaMenus( FunctionalAreaMenuSettingsBuilder::AREA_UID, $menus );
    }
}