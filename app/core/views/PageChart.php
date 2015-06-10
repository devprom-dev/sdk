<?php

include SERVER_ROOT_PATH."core/classes/FlotChartDataSource.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBarWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartLineWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartPieWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBurndownWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBurnupWidget.php";

class PageChart extends StaticPageList
{
 	function PageChart( $object ) 
	{
		parent::StaticPageList( $object );
	}

	function getIterator()
	{
	    global $model_factory;
	    
	    $iterator = parent::getIterator();
	    
		$object = $this->getObject();
		
		$aggs = $this->getAggregates();
		
		foreach ( $aggs as $agg )
		{
			$object->addAggregate( $agg );
		}
		
		$aggby = $this->getAggregateBy();
		
	    if ( $this->getGroup() == 'history' )
		{
		    $values = $this->getFilterValues();
		    
		    $object->addFilter(new FilterClusterPredicate($values['modifiedafter']));		
		    
		    $it = $object->getAggregatedHistory( $object->getFilters() );
		}
		else
		{
			$it = $object->getAggregated('t');
		}
		
		$data = $it->getRowset();

		if ( $this->getGroup() == 'history' )
		{
		    // restore values missed on timeline
		    
    		$keys_by_aggregate = array();
    		
    	    foreach( $data as $key => $item )
            {
                $keys_by_aggregate[$item[$aggby]][] = $item['DayDate'];
            }
            
            $common_keys = array();
            
            foreach( $keys_by_aggregate as $aggregate => $keys )
            {
                $common_keys = array_merge($common_keys, $keys_by_aggregate[$aggregate]);
            }

            $common_keys = array_unique($common_keys);
            
            asort($common_keys);
            
		    foreach( $keys_by_aggregate as $aggregate => $keys )
            {
                $additional_keys = array_diff($common_keys, $keys);
                
                if ( count($additional_keys) < 1 ) continue;
                
                foreach( $additional_keys as $daydate )
                {
                     $data[] = array( $aggby => $aggregate, 'DayDate' => $daydate, $aggs[0]->getAggregateAlias() => 0);
                }
            }
		}
		
		// sort data according states ordering
		 
		if ( $this->getGroup() == 'State' || $aggby == 'State' )
		{
		    $state = $model_factory->getObject($object->getStateClassName());
		    
		    $state_it = $state->getAll();

		    $this->state_sort_index = array();

		    while ( !$state_it->end() )
		    {
		        $this->state_sort_index[$state_it->get('ReferenceName')] = $state_it->get('OrderNum');
		         
		        $state_it->moveNext();
		    }   
		    
		    usort($data, array($this, $aggby == 'State' ? 'sortByStateDesc' : 'sortByStateAsc'));

		    $state_it->moveFirst();
		    
			while ( !$state_it->end() )
		    {
		        foreach( $data as $key => $item )
		        {
		            if ( $item['State'] == $state_it->get('ReferenceName') )
		            {
		                $data[$key]['State'] = $state_it->getDisplayName();
		            }
		        }
		        
		        $state_it->moveNext();
		    }   
		}

		return $object->createCachedIterator( $data );
	}
	
	function sortByStateAsc( $left, $right )
	{
	    if ( $left['State'] == $right['State'] )
	    {
	        return $left['DayDate'] > $right['DayDate'] ? 1 : -1; 
	    }
	    
	    return $this->state_sort_index[$left['State']] > $this->state_sort_index[$right['State']] ? 1 : -1;
	}
	
	function sortByStateDesc( $left, $right )
	{
	    if ( $left['State'] == $right['State'] )
	    {
	        return $left['DayDate'] > $right['DayDate'] ? 1 : -1; 
	    }
	    
	    return $this->state_sort_index[$left['State']] < $this->state_sort_index[$right['State']] ? 1 : -1;
	}
	
	function getAggregates()
	{
		if ( $this->getGroup() == 'history' )
		{
			return array (
				new AggregateBase( 'DayDate', '', '' ), 
				new AggregateBase( $this->getAggregateBy(), 
					$this->getAggregateBy(), $this->getAggregator() ) 
			);
		}
		elseif ( $this->getAggregator() == 'none' )
		{
			return array (
				new AggregateBase( $this->getGroup(), '', '' ), 
				new AggregateBase( $this->getAggregateBy(), '1', 'COUNT' ) 
			);
		}
		else
		{
			$agg = new AggregateBase( $this->getGroup(), 
				$this->getAggregateBy(), $this->getAggregator() );
			
			$agg->setGroupFunction( $this->getGroupFunction() );
				
			return array ( $agg );
		}
	}
	
	function getAggregators()
	{
		return array (
			'COUNT' => translate('Количество'),
			'SUM' => translate('Сумма'),
			'AVG' => translate('Среднее'),
			'MAX' => translate('Максимум'),
			'MIN' => translate('Минимум')
		);
	}

	function getGroup()
	{
		global $_REQUEST;
		
		$group = parent::getGroup();
		
		$fields = $this->getGroupFields();
		
		if ( $group == '' )
		{
			if ( count($fields) < 1 )
			{
				return $this->object->getClassName().'Id';
			}
			else
			{
				return $fields[0];
			}
		}
		
		return $group;
	}
	
	function getAggregateBy()
	{
		$values = $this->getFilterValues();
		
		if ( $values['aggby'] != '' )
		{
			return $values['aggby'];
		}
		else
		{
			return $this->getGroup() == 'history' ? 'State' : '1';
		}
	}
	
	function getAggregator()
	{
		$values = $this->getFilterValues();
		
		if ( $values['aggregator'] != '' )
		{
			return $values['aggregator'];
		}
		else
		{
			return 'COUNT';
		}
	}

	function getGroupFunction()
	{
		$values = $this->getFilterValues();
		
		if ( $values['groupfunc'] != '' )
		{
			return $values['groupfunc'];
		}
		else
		{
			return '';
		}
	}
	
	function getLegendVisible()
	{
		$values = $this->getFilterValues();
		
		if ( $values['chartlegend'] != '' )
		{
			return $values['chartlegend'] != 'hide';
		}
		else
		{
			return 'show';
		}
	}

	function getTableVisible()
	{
		$values = $this->getFilterValues();
		
		if ( $values['chartdata'] != '' )
		{
			return $values['chartdata'] != 'hide';
		}
		else
		{
			return 'show';
		}
	}
	
	function getColumnFields()
	{
		return array();
	}
	
	function getGroupFields()
	{
		$object = $this->getObject();
		$fields = array();
		
		$skip_attributes = array_merge(
				$this->getSystemAttributes(),
				$this->getObject()->getAttributesByGroup('trace')
		);
		
		$clause = $object->getRegistry()->getSelectClause('', false);
		$attrs = $object->getAttributes();
		
		foreach ( $attrs as $key => $attr )
		{
			if ( $key == 'OrderNum' ) continue;
			if ( in_array($key, $skip_attributes) ) continue;
			
			$skip = !$this->object->IsAttributeStored( $key ) && !preg_match('/\)\s+\`?'.$key.'\`?\s+,/', $clause);
			if ( $skip ) continue;

			if ( in_array($this->object->getAttributeType($key), array('','text','wysiwyg','largetext','char','varchar')) ) continue;
			
			array_push( $fields, $key );
		}
		
		$fields[] = 'RecordModified';
		$fields[] = 'RecordCreated';
		
		return $fields;
	}
	
	function getAggByFields()
	{
		$fields = parent::getColumnFields();
		
		$skip_attributes = array_merge(
				$this->getSystemAttributes(),
				$this->getObject()->getAttributesByGroup('trace')
		);
		
		foreach( $fields as $key => $field )
		{
			if ( in_array($field, $skip_attributes) ) unset ( $fields[$key] );
			if ( in_array($this->object->getAttributeType($field), array('','text','wysiwyg','largetext','char','varchar')) ) unset ( $fields[$key] );
		}
		
		return $fields;
	}
	
	function HasRows()
	{
		return false;
	}
	
	function buildFilterActions( & $base_actions )
	{
	    $actions = array();
	    $object = $this->getObject();
	    $filter_values = $this->getFilterValues();
	
	    // grouping by
	    $used_group = $filter_values['group'];
	    if ( $used_group == '' ) $used_group = $this->getGroup();
	
	    $fields = $this->getGroupFields();
	    if ( count($fields) > 0 )
	    {
	        $groups = array();
	        foreach ( $fields as $field )
	        {
	            $name = $object->getAttributeUserName($field);
	            if ( $name != '' )
	            {
	                $script = "javascript: filterLocation.setup( 'group=".$field."', 0 ); ";
	                $groups[translate($name)] = array ( 'url' => $script, 'checked' => $used_group == $field );
	            }
	        }
	
	        ksort($groups);
	        $group_actions = array();
	        	
	        foreach ( $groups as $caption => $group )
	        {
	            array_push( $group_actions,
	            array ( 'url' => $group['url'], 'name' => $caption,
	            'checked' => $group['checked'], 'radio' => true )
	            );
	        }
	
	        if ( count($group_actions) > 0 )
	        {
	            $script = "javascript: filterLocation.setup( 'group=history', 0 ); ";
	
	            array_push( $group_actions,
	            array (),
	            array ( 'url' => $script, 'name' => translate('По дате'),
	            'checked' => $used_group == 'history', 'radio' => true )
	            );
	
	            array_push($actions, array (
	            'name' => translate('Группировка'),
	            'items' => $group_actions )
	            );
	        }
	    }
	
	    // aggregate by
	    if ( $filter_values['aggby'] == '' ) $filter_values['aggby'] = $this->getAggregateBy();
	
	    $fields = $this->getAggByFields();
	    $columns = array();
	
	    foreach ( $fields as $field )
	    {
	        $name = $object->getAttributeUserName( $field );
	        	
	        $script = "filterLocation.setup( 'aggby=".$field."', 0 ); ";
	        	
	        $columns[translate($name)] = array(
	                'url' => $script, 'checked' => $filter_values['aggby'] == $field );
	    }
	
	    ksort($columns);
	    $column_actions = array();
	
	    foreach( $columns as $caption => $column )
	    {
	        array_push( $column_actions,
	        array ( 'url' => $column['url'], 'name' => $caption,
	        'checked' => $column['checked'], 'radio' => true )
	        );
	    }
	
	    if ( count($column_actions) > 0 )
	    {
	        array_push($actions, array ( 'name' => translate('Агрегация по'),
	        'items' => $column_actions , 'title' => '' ) );
	    }
	
	    // aggregators
	    if ( $filter_values['aggregator'] == '' ) $filter_values['aggregator'] = $this->getAggregator();
	
	    $fields = $this->getAggregators();
	    $columns = array();
	
	    foreach ( $fields as $key => $field )
	    {
	        $script = "filterLocation.setup( 'aggregator=".$key."', 0 ); ";
	        	
	        $columns[translate($field)] = array(
	                'url' => $script, 'checked' => $filter_values['aggregator'] == $key );
	    }
	
	    ksort($columns);
	    $column_actions = array();
	
	    foreach( $columns as $caption => $column )
	    {
	        array_push( $column_actions,
	        array ( 'url' => $column['url'], 'name' => $caption,
	        'checked' => $column['checked'], 'radio' => true )
	        );
	    }
	
	    if ( count($column_actions) > 0 )
	    {
	        $script = "javascript: filterLocation.setup( 'aggregator=none', 0 ); ";
	
	        array_push( $column_actions,
	        array (),
	        array ( 'url' => $script, 'name' => translate('Без агрегации'),
	        'checked' => $filter_values['aggregator'] == 'none', 'radio' => true )
	        );
	
	        array_push($actions, array ( 'name' => translate('Тип агрегации'),
	        'items' => $column_actions , 'title' => '' ) );
	    }
	
	    // chart options
	    if ( $filter_values['chartlegend'] == '' ) $filter_values['chartlegend'] = 'none';
	    if ( $filter_values['chartdata'] == '' ) $filter_values['chartdata'] = 'none';
	
	    $column_actions = array();
	
	    $script = "javascript: filterLocation.setup( 'chartlegend=' + ($(this).hasClass('checked') ? 'show' : 'hide'), 0 ); ";
	    array_push( $column_actions,
	    array ( 'url' => $script, 'name' => translate('Отображать легенду'),
	    'checked' => $filter_values['chartlegend'] != 'hide', 'multiselect' => true )
	    );
	
	    $script = "javascript: filterLocation.setup( 'chartdata=' + ($(this).hasClass('checked') ? 'show' : 'hide'), 0 ); ";
	    array_push( $column_actions,
	    array ( 'url' => $script, 'name' => translate('Отображать таблицу'),
	    'checked' => $filter_values['chartdata'] != 'hide', 'multiselect' => true )
	    );
	
	    array_push($actions, array ( 'name' => translate('Опции'),
	    'items' => $column_actions , 'title' => '' ) );
	
	    $base_actions = array_merge(
	            array_slice($base_actions, 0, 1),
	            $actions,
	            array_slice($base_actions, 1, count($base_actions) - 1)
	    );
	}
	
	function getChartWidget()
	{
		$aggs = $this->getAggregates();
	    
	    if ( count($aggs) < 2 )
		{
			switch ( strtolower($aggs[0]->getAggregate()) )
			{
				case 'count':
				    $widget = new FlotChartPieWidget();
					break;
					
				default:
				    $widget = new FlotChartBarWidget();
			}
		}
		else
		{
			if ( $this->getGroup() == 'history' )
			{
				$widget = new FlotChartLineWidget();
			}
			else
			{
				$widget = new FlotChartBarWidget();
			}
		}
		
		return $widget;
	}
	
	function getStyle()
	{
		return 'height:420px;';
	}
	
	function draw()
	{
	    $widget = $this->getChartWidget();
	    
	    if ( !is_object($widget) )
	    {
	        throw new Exception("Chart widget is undefined");
	    }
	    
	    $aggs = $this->getAggregates();
	    
	    $data = FlotChartDataSource::getData($this->getIteratorRef(), $aggs);
	    
	    $widget->setData( $data );

    	$chart_id = "chart".uniqid();
	    
		echo '<div style="float:left;width:67%;">';

    		echo '<div id="'.$chart_id.'" style="'.$this->getStyle().'"></div>';
    		
		    $widget->setLegend( $this->getLegendVisible() );
		    
    		$widget->draw($chart_id);
		
		echo '</div>';

		if ( count($aggs) < 2 && $this->getTableVisible() )
		{
		    if ( is_a($widget, 'FlotChartPieWidget') )
		    {
				echo '<div style="float:right;width:31%">';
					$this->drawLegend( $data, $aggs );
				echo '</div>';
		    }
		    else
		    {
				echo '<div style="clear:both;"></div>';
				echo '<div style="padding:16px 22px 0;">';
					$this->drawLegend( $data, $aggs );
				echo '</div>';
		    }
		}
		
		$this->drawScripts( $chart_id );
	}
	
	function drawLegend( $data, & $aggs )
	{
		$object = $this->getObject();
		$aggregators = $this->getAggregators();
		
		echo '<table class="table table-hover">';		

		foreach( $aggs as $agg )
		{
			$agg_title = $aggregators[$agg->getAggregate()];
			
			$attribute = $agg->getAggregatedAttribute();

			if ( $attribute != '' && $attribute != '1' )
			{
				$agg_title .= ' ('.translate($object->getAttributeUserName($attribute)).')';
			}
			
			echo '<tr>';
				echo '<th>'.translate($object->getAttributeUserName($agg->getAttribute())).'</th>';
				echo '<th>'.$agg_title.'</th>';
			echo '</tr>';
		}

		foreach ( $data as $key => $item )
		{		
			echo '<tr>';
				echo '<td>'.$key.'</td>';
				echo '<td>'.$item['data'].'</td>';
			echo '</tr>';
		}

		echo '</table>';		
	}
	
	function IsNeedNavigator()
	{
		return false;
	}
	
	function drawScripts( $chart_id )
	{
	    /*
		if ( $this->url == '' ) return;
		?>
		<script type="text/javascript">
			$('#<? echo $id ?>').bind("plotclick", function (event, pos, item) {
				window.location = "<? echo $this->url ?>";
			}).css('cursor', 'pointer');
		</script>
		<?
		*/
	    
		$dateformat = getLanguage()->getLocaleFormatter()->getDateJSFormat();
		
	    ?>
		<script type="text/javascript">

	        $("#<?=$chart_id?>").css('width', $('#tablePlaceholder').width() - 20);
		
		    var previousPoint = null;

			$(function () 
			{
			    $("#<?=$chart_id?>").bind("plotclick", function (event, pos, item) {
			    	 if (item) {
				    	 if ( typeof item.series.urls != 'undefined' )
				    	 {
				    		 var url = item.series.urls[item.datapoint[0]];

				    		 if ( typeof url != 'undefined' )
				    		 {
					    		 window.location = url; 
				    		 }
				    	 }
			    	 }
			    });
					
			    $("#<?=$chart_id?>").bind("plothover", function (event, pos, item) {
			        $("#x").text(pos.x.toFixed(2));
		    	    $("#y").text(pos.y.toFixed(2));
		 
		            if (item) {
		                if (previousPoint != item.dataIndex) {
	    	                previousPoint = item.dataIndex;
	            	        $("#charttooltip").remove();

	            	        var xValue = '';
	            	        switch( typeof item.datapoint[0] )
	            	        {
	            	        case 'number':
		            	        if ( item.datapoint[0] > 1000000 )
		            	        {
			            	        var dt = new Date(item.datapoint[0]);
			            	        xValue = dt.toString('<?=$dateformat?>');
		            	        }
		            	        else
		            	        {
			            	        xValue = item.datapoint[0];
		            	        }
		            	        break;
		            	    default:
		            	        xValue = item.datapoint[0];
	            	        }

	            	        if ( item.series.xaxis.ticks.length > 0 )
	            	        {
		            	        if ( typeof xValue == 'number' ) xValue = item.series.xaxis.ticks[xValue].label;
	            	        }

	            	        if ( typeof item.series.label != 'undefined' )
	            	        {
	            	        	yValue = item.series.data[item.dataIndex][1];
	            	        }
	            	        else
	            	        {
	            	        	yValue = "";
	            	        }
	            	        
	            	        if ( typeof item.series.axisDescription != 'undefined' )
	            	        {
    	            	        if ( typeof item.series.axisDescription.xaxis != 'undefined' )
    	            	        {
    	            	        	xValue = item.series.axisDescription.xaxis + ": " + xValue;
    	            	        }
    	            	        else
    	            	        {
    	            	        	xValue = "";
    	            	        }
    
    	            	        if ( typeof item.series.axisDescription.yaxis != 'undefined' )
    	            	        {
    	            	        	yValue = item.series.axisDescription.yaxis + ": " + item.series.data[item.dataIndex][1];
    	            	        }
    	            	        else
    	            	        {
    	            	        	yValue = "";
    	            	        }
	            	        }
	            	        
	                	    var text = (typeof item.series.label != 'undefined' ? item.series.label + ": " : "")
	                	        + yValue + ( xValue != '' ? " [" + xValue + "]" : "" );
	                    
	                    	showFlotTooltip(item.pageX, item.pageY, text);
		                }
		            }
	    	        else {
	        	        $("#charttooltip").remove();
	            	    previousPoint = null;            
	            	}
			    });
    	    });
		</script>
	    <?php 
	}
	
	function render( $view, $parms )
	{
		echo $view->render("core/PageChart.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}
}