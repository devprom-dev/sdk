<?php

class BlogArchiveSection extends InfoSection
{
    function drawBody()
    {
        global $model_factory;
        
	    $month_aggregate = new AggregateBase( 'RecordCreated' );
	        
	    $month_aggregate->setAlias('');
	    
	    $month_aggregate->setGroupFunction('EXTRACT(YEAR_MONTH FROM ');
		
	    $post = $model_factory->getObject('BlogPost');    
	    
		$post->addAggregate( $month_aggregate );
		
		$post_it = $post->getAggregated('');

        while( !$post_it->end() )
        {
            $items = $post_it->get( $month_aggregate->getAggregateAlias() );
            
            $year = round($post_it->get('RecordCreated') / 100, 0);
            
            $month = $post_it->get('RecordCreated') % 100;
            
			?>
			<a href="<? echo $post->getPage().'&month='.$month.'&year='.$year; ?>"><? echo $month.' '.$year; ?></a>
			<span style="color:silver">(<? echo $items; ?>)</span>
			<?

			echo '<br/>';			

    		$post_it->moveNext();
        }                
	}

 	function getCaption()
 	{
 		return translate('Архив');
 	}
 }