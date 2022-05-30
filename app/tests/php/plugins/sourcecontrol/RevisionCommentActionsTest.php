<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."plugins/sourcecontrol/classes/notificators/RevisionCommentActionsTrigger.php";
include_once SERVER_ROOT_PATH."plugins/sourcecontrol/classes/Commit.php";
include_once SERVER_ROOT_PATH."plugins/sourcecontrol/classes/CommitMetadataBuilder.php";

class RevisionCommentActionsTest extends DevpromDummyTestCase
{
	private $handler = null;
	
    function setUp()
    {
        parent::setUp();
        
        $this->handler = $this->getMockBuilder(\RevisionCommentActionsTrigger::class)
            ->setConstructorArgs(array(getSession()))
            ->setMethods(['bindObjects','addWorkLog','moveObjects','info','addComment'])
            ->getMock();

        $request = $this->getMockBuilder(\Request::class)
            ->setConstructorArgs(array())
            ->setMethods(['getStates'])
            ->getMock();
        $request->expects($this->any())->method('getStates')->will( $this->returnValue(
                 array('submitted','inprogress','resolved')
        ));
        
        $task = $this->getMockBuilder(\Task::class)
            ->setConstructorArgs(array())
            ->setMethods(['getStates'])
            ->getMock();
        $task->expects($this->any())->method('getStates')->will( $this->returnValue(
                 array('submitted','open','closed')
        ));

        $user = $this->getMockBuilder(\User::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact'])
            ->getMock();
        $user->expects($this->any())->method('getExact')->will( $this->returnValue(
            $user->createCachedIterator(array(
                array(
                    'cms_UserId' => 1,
                    'Caption' => 'test'
                )
            ))
        ));

        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Request', null, $request ),
                        array ( 'Task', null, $task ),
                        array ( 'User', null, $user )
                ) 
        ));
    }

    function getMetadataBuilders()
    {
        return array_merge( parent::getMetadataBuilders(), array(
            new CommitMetadataBuilder()
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
	        					'[T-686] #resolve #time 2h #comment asdasd '.PHP_EOL.
	        					'[T-687] #resolve #time 2h #comment asdasd'
	        		)
    			)
        ), TRIGGER_ACTION_ADD);
    }

    function test2WorkItemBindingNoEOL()
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
                        '[T-686] #resolve #time 2h #comment asdasd [T-687] #resolve #time 2h #comment asdasd'
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
        		$this->stringContains('submitted')
        	);
        
    	$this->handler->process($commit_it, TRIGGER_ACTION_ADD);
    }

    function testMessOfTags()
    {
        $commit_it = (new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' => 'I-123 #submitted',
                    'SystemUser' => 1
                )
            )
        );

        $this->handler->expects($this->atLeastOnce())->method('bindObjects')->will(
            $this->returnValue(array($commit_it))
        );

        $this->handler->expects($this->exactly(2))->method('addWorkLog')->with(
            $this->anything(),
            $this->anything(),
            $this->callback(function($o) {
                return $o == 2;
            }),
            $this->callback(function($o) {
                return $o == 'asdasd';
            })
        );
        $this->handler->expects($this->exactly(0))->method('addComment');


        $this->handler->process((new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' =>
                        '[T-687] #comment asdasd #time 2h'.PHP_EOL.
                        '[T-688] #time 2h #comment asdasd',
                    'SystemUser' => 1
                )
            )
        ), TRIGGER_ACTION_ADD);
    }

    function testComments()
    {
        $commit_it = (new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' => 'I-123 #submitted',
                    'SystemUser' => 1
                )
            )
        );

        $this->handler->expects($this->atLeastOnce())->method('bindObjects')->will(
            $this->returnValue(array($commit_it))
        );

        $this->handler->expects($this->exactly(1))->method('addComment')->with(
            $this->anything(),
            $this->anything(),
            $this->callback(function($o) {
                return $o == 'asdasd';
            }),
            $this->anything()
        );

        $this->handler->process((new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' => '[T-687] #comment asdasd',
                    'SystemUser' => 1
                )
            )
        ), TRIGGER_ACTION_ADD);
    }

    function testCommentWhenStateChanged()
    {
        $commit_it = (new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' => 'T-123 #submitted',
                    'SystemUser' => 1
                )
            )
        );

        $this->handler->expects($this->atLeastOnce())->method('bindObjects')->will(
            $this->returnValue(array($commit_it))
        );

        $this->handler->expects($this->atLeastOnce())->method('moveObjects')->with(
            $this->anything(),
            $this->callback(function($o) {
                return $o == 'asd ads';
            }),
            $this->stringContains('resolve')
        );

        $this->handler->process((new Commit)->createCachedIterator(
            array (
                array (
                    'pm_SubversionRevisionId' => 1,
                    'Description' => '[T-687] #resolve asd ads',
                    'SystemUser' => 1
                )
            )
        ), TRIGGER_ACTION_ADD);
    }
}