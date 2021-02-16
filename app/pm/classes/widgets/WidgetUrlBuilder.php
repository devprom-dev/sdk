<?php

class WidgetUrlBuilder
{
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        static::$singleInstance = new static();
        return static::$singleInstance;
    }

    public static function Destroy() {
        static::$singleInstance = null;
    }

    public function buildWidgetUrlIt( $objectIt, $idsKey = 'ids', $widgetIt = null )
    {
        return $this->buildWidgetUrlIds(
            get_class($objectIt->object),
            $objectIt->idsToArray(),
            $objectIt->fieldToArray('VPD'),
            $idsKey,
            $widgetIt
        );
    }

    public function buildWidgetUrlIds( $className, $ids, $vpds, $idsKey = 'ids', $widgetIt = null )
    {
        $vpds = array_unique($vpds);

        $projectIt = getSession()->getProjectIt();
        if ( count($vpds) == 1 && !in_array($projectIt->get('VPD'), $vpds) ) {
            $projectIt = getFactory()->getObject('Project')->getByRef('VPD', $vpds[0]);
        }

        if ( !is_object($widgetIt) ) {
            $widgetIt = getFactory()->getObject('ObjectsListWidget')
                            ->getByRef('Caption', $className)->getWidgetIt();
        }

        if ( $widgetIt->getId() != '' ) {
            return $widgetIt->getUrl(
                        $idsKey . '=' . \TextUtils::buildIds($ids),
                        count($vpds) > 1 && is_object(self::$projectIt) ? self::$projectIt : $projectIt
                    );
        }
        return '';
    }

    private function __construct() {
        if ( class_exists('Portfolio') ) {
            $portfolio = getFactory()->getObject('Portfolio');
            self::$projectIt = $portfolio->getByRef('CodeName', 'my');
            if ( self::$projectIt->getId() == '' ) {
                self::$projectIt = $portfolio->getByRef('CodeName', 'all');
            }
        }
    }

    protected static $singleInstance = null;
    protected static $projectIt = null;
}