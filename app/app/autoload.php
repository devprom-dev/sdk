<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = include SERVER_ROOT_PATH . 'ext/vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader; 