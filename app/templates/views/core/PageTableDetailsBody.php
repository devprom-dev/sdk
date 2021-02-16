<?php

$list->render( $view, array(
    'object_id' => $object_id,
    'object_class' => $object_class,
    'title' => $title,
    'widget_id' => $widget_id,
    'tableonly' => $tableonly
));

$table->drawScripts();