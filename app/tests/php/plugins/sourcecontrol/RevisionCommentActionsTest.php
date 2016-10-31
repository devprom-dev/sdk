<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."plugins/sourcecontrol/classes/notificators/RevisionCommentActionsTrigger.php";
include_once SERVER_ROOT_PATH."plugins/sourcecontrol/classes/Commit.php";

class RevisionCommentActionsTest extends DevpromDummyTestCase
{
	private $handler = null;
	
    function setUp()
    {
        parent::setUp();
        
        $this->handler = $this->getMock(
        		'RevisionCommentActionsTrigger', 
        		array('bindObjects','addWorkLog','moveObjects','info'),
        		array(getSession())
        );

        $request = $this->getMock('Request', array('getStates'));
        $request->expects($this->any())->method('getStates')->will( $this->returnValue(
                 array('submitted','inprogress','resolved')
        ));
        
        $task = $this->getMock('Task', array('getStates'));
        $task->expects($this->any())->method('getStates')->will( $this->returnValue(
                 array('submitted','open','closed')
        ));
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Request', null, $request ),
                        array ( 'Task', null, $task ),
                        array ( 'User', null, new User() )
                ) 
        ));
    }

    function testNegativeCase1()
    {
        $this->handler->expects($this->never())->method('bindObjects');
    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => 'I -123'
	        		)
    			)
        ), TRIGGER_ACTION_ADD);
    }
    
    function testWorkItemBinding()
    {
        $this->handler->expects($this->atLeastOnce())->method('bindObjects')->with(
        		$this->anything(),
			    $this->callback(function($o) { 
			    	$uid = new ObjectUID;
			    	foreach( $o as $value ) { 
			    		if (!$uid->isValidUid($value)) return false;
			    	}
			    	return true;
			    })
        );
        
    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => 'I-123'
	        		)
    			)
        	), TRIGGER_ACTION_ADD);
    	
    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => 'I-123 #resolve'
	        		)
    			)
        	), TRIGGER_ACTION_ADD);

    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => '[T-686]'
	        		)
    			)
        ), TRIGGER_ACTION_ADD);

    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => '[T-686] #resolve #time 2h #comment asdasd'
	        		)
    			)
        ), TRIGGER_ACTION_ADD);
    }

    function test2WorkItemBinding()
    {
        $this->handler->expects($this->exactly(2))->method('bindObjects')->with(
        		$this->anything(),
			    $this->callback(function($o) { 
			    	$uid = new ObjectUID;
			    	foreach( $o as $value ) { 
			    		if (!$uid->isValidUid($value)) return false;
			    	}
			    	return true;
			    })
        );
        
    	$this->handler->process((new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => 
	        					'[T-686] #resolve #time 2h #comment asdasd'.PHP_EOL.
	        					'[T-687] #resolve #time 2h #comment asdasd'
	        		)
    			)
        ), TRIGGER_ACTION_ADD);
    }
    
    function testWorkflowActions()
    {
    	$commit_it = (new Commit)->createCachedIterator(
    			array (
	        		array (
	        				'pm_SubversionRevisionId' => 1,
	        				'Description' => 'I-123 #submitted'
	        		)
    			)
        	);
    	
    	$this->handler->expects($this->atLeastOnce())->method('bindObjects')->will( 
    			$this->returnValue(array($commit_it))
    		);
    	
        $this->handler->expects($this->atLeastOnce())->method('moveObjects')->with(
        		$this->anything(),
        		$this->anything(),
        		$this->anything(),
        		$this->stringContains('submitted')
        	);
        
    	$this->handler->process($commit_it, TRIGGER_ACTION_ADD);
    }
}