<?php
include_once "PMSession.php";

class SessionBuilderProject extends SessionBuilder
{
    protected function buildSession(array $parms, $cacheService = null)
    {
        global $session;
        $session = new COSession();

        $session = $this->newSession($parms['project'], $cacheService);

        // cache context
        $session->getProjectIt()->getMethodologyIt();

        return $session;
    }

    protected function newSession( $project, $cacheService )
    {
        $cache = new ProjectAccessible();
        $cache_it = $cache->getByRef('CodeName', $project);

        if ( $cache_it->getId() < 1 )
        {
            // build portfolios
            $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
            while( !$portfolio_it->end() )
            {
                if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) ) {
                    $portfolio_it->moveNext(); continue;
                }
                if ( $project == $portfolio_it->get('CodeName') ) {
                    // build session object for the given portfolio
                    return $portfolio_it->getSession();
                }
                $portfolio_it->moveNext();
            }
            if ( in_array($project, array('my','all')) ) {
                $firstProjectIt = getFactory()->getObject('Project')->getRegistry()->Query(
                    array(
                        new ProjectParticipatePredicate(),
                        new ProjectStatePredicate('active')
                    )
                );
                if ( $firstProjectIt->getId() == '' ) {
                    $portfolio_it->moveTo('CodeName', 'all');
                    if ( $portfolio_it->get('CodeName') == 'all' ) {
                        return $portfolio_it->getSession();
                    }
                }
                return new PMSession($firstProjectIt, null, null, $cacheService);
            }
            else {
                return new PMSession($cache->getEmptyIterator(), null, null, $cacheService);
            }
        }
        else {
            return new PMSession($cache_it, null, null, $cacheService);
        }
    }
}