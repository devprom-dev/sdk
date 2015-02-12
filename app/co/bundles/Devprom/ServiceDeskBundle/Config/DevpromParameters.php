<?php
/**
 * Загрузчик пропертей для сервисдеска. Используется с PhpFileLoader
 *
 * @see Symfony\Component\DependencyInjection\Loader\PhpFileLoader
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */

namespace Devprom\ServiceDeskBundle\Config;

/** @var Container $container */
use Symfony\Component\DependencyInjection\Container;

$loader = new DevpromParametersLoader();
$devpromParameters = $loader->loadSystemSettings();
foreach ($devpromParameters as $paramName => $paramValue) {
    $container->setParameter($paramName, $paramValue);
}
$devpromParameters = $loader->loadProjectSettings();
foreach ($devpromParameters as $paramName => $paramValue) {
    $container->setParameter($paramName, $paramValue);
}
