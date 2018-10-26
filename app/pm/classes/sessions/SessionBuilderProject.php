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
        $session->getProjectIt()->getParentIt();

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
            throw new Exception();
        }
        else {
            return new PMSession($cache_it, null, null, $cacheService);
        }
    }

    public function getUserProjectIt()
    {
        return getFactory()->getObject('Project')->getRegistry()->Query(
            array(
                new ProjectParticipatePredicate(),
                new ProjectStatePredicate('active')
            )
        );
    }

}