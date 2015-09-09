<?php

namespace Devprom\CommonBundle\Service;
use Symfony\Component\Finder\Finder;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class ClearCacheService {

    private $cacheDir;

    function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function clearContainerCache() {
        $finder = new Finder();
        $finder->name('*ProjectContainer.php')->in($this->cacheDir);
        foreach ($finder as $file) {
            unlink($file->getRealPath());
        }

    }

}
