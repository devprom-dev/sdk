<?php

include SERVER_ROOT_PATH."core/classes/FlotChartDataSource.php";
include SERVER_ROOT_PATH."core/classes/schedule/DateYearWeekModelBuilder.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBarWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartLineWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartPieWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBurndownWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBurnupWidget.php";

class PageChart extends StaticPageList
{
	private $demo = false;

 	function PageChart( $object ) 
	{
		$builder = new DateYearWeekModelBuilder();
        $builder->build($object);

		parent::__construct( $object );
	}

	function setDemo( $demo = true ) {
		$this->demo = $demo;
	}

	function getDemo() {
		return $this->demo;
	}

	function getIterator()
	{
	    $iterator = parent::getIterator();
		$minSizeValuable = 1;

		$object = $this->getObject();

		$aggs = $this->getAggregates();
		foreach ( $aggs as $agg ) {
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
			$minSizeValuable = 2;
		    // restore values missed on timeline
    		$keys_by_aggregate = array();
    		
    	    foreach( $data as $key => $item ) {
                $keys_by_aggregate[$item[$aggby]][] = $item['DayDate'];
            }
            
            $common_keys = array();
            
            foreach( $keys_by_aggregate as $aggregate => $keys ) {
                $common_keys = array_merge($common_keys, $keys_by_aggregate[$aggregate]);
            }

            $common_keys = array_unique($common_keys);
            asort($common_keys);
            
		    foreach( $keys_by_aggregate as $aggregate => $keys )
            {
                $additional_keys = array_diff($common_keys, $keys);
                if ( count($additional_keys) < 1 ) continue;
                
                foreach( $additional_keys as $daydate ) {
                     $data[] = array( $aggby => $aggregate, 'DayDate' => $daydate, $aggs[0]->getAggregateAlias() => 0);
                }
            }
		}
		
		// sort data according states ordering
		if ( $this->getGroup() == 'State' || $aggby == 'State' )
		{
			$this->state_sort_index = array();
		    $state_it = WorkflowScheme::Instance()->getStateIt($object);
		    while ( !$state_it->end() )
		    {
		        $this->state_sort_index[$state_it->get('ReferenceName')] = $state_it->get('OrderNum');
		        $state_it->moveNext();
		    }   
		    
		    usort($data, array($this, $aggby == 'State' ? 'sortByStateDesc' : 'sortByStateAsc'));

		    $state_it->moveFirst();
			while ( !$state_it->end() )
		    {
		        foreach( $data as $key => $item ) {
		            if ( $item['State'] == $state_it->get('ReferenceName') ) {
		                $data[$key]['State'] = $state_it->getDisplayName();
		            }
		        }
		        $state_it->moveNext();
		    }

			if ( $this->getGroup() == 'history' ) {
				$minSizeValuable = count($this->state_sort_index);
			}
		}

		return $object->createCachedIterator( count($data) < $minSizeValuable ? $this->getDemoData($aggs) : $data );
	}

	protected function IsAttributeInQuery( $attribute )
	{
		return parent::IsAttributeInQuery( $attribute )
			|| $this->getAggregateBy() == $attribute
			|| $this->getGroup() == $attribute;
	}

	protected function getDemoData($aggs)
	{
		return array();
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
			$agg = new AggregateBase( $this->getGroup(), '', '' );
			$agg->setGroupFunction( $this->getGroupFunction() );

			return array (
				$agg,
				new AggregateBase($this->getAggregateBy(), '1', 'COUNT')
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

	function getAllowedGroupFields()
	{
		$disallowedFields = array();
		return array_diff(
			array_merge(
				$this->getGroupFields(),
				array_keys($this->getObject()->getAttributes()),
				array (
					'history',
					'DayDate'
				)
			),
			$disallowedFields
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
		
		if ( !in_array($values['aggby'],array('','all','none')) )
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
		
		if ( !in_array($values['aggregator'],array('','all')) )
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
		return $values['groupfunc'];
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
			return $this->getGroup() != 'history';
		}
	}
	
	function getColumnFields()
	{
		return array();
	}
	
	function getGroupFields()
	{
		$object = $this->getObject();
		$attrs = $object->getAttributes();
		$fields = array();
		
		$skip_attributes = array_merge(
				$this->getSystemAttributes(),
				$this->getObject()->getAttributesByGroup('trace')
		);
		foreach( $attrs as $attribute => $info ) {
			if ( !$this->getObject()->IsAttributeStored($attribute) && $this->getObject()->getAttributeOrigin($attribute) != ORIGIN_CUSTOM ) {
				$skip_attributes[] = $attribute;
			}
		}

		$clause = $object->getRegistry()->getSelectClause('', false);

		foreach ( $attrs as $key => $attr )
		{
			if ( $key == 'OrderNum' ) continue;
			if ( in_array($key, $skip_attributes) ) continue;
			if ( !$this->object->IsAttributeStored( $key ) && !preg_match('/\)\s+\`?'.$key.'\`?\s+,/', $clause) ) continue;
			if ( $key != 'State' && in_array($this->object->getAttributeType($key), array('','text','wysiwyg','largetext','char','varchar')) ) continue;
			
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
			if ( in_array($field, $skip_attributes) ) {
				unset ( $fields[$key] );
				continue;
			}
			if ( $field != 'State' && in_array($this->object->getAttributeType($field), array('','text','wysiwyg','largetext','char','varchar')) ) {
				unset ( $fields[$key] );
				continue;
			}
			if ( !$this->getObject()->IsAttributeStored($field) ) {
				unset ( $fields[$key] );
				continue;
			}
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
            $color_attribute = $aggs[0]->getAttribute();
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
            $color_attribute = $aggs[1]->getAttribute();
			if ( $this->getGroup() == 'history' )
			{
				$widget = new FlotChartLineWidget();
			}
			else
			{
				$widget = new FlotChartBarWidget();
			}
		}

        if ( $this->getObject()->IsReference($color_attribute) ) {
            $ref = $this->getObject()->getAttributeObject($color_attribute);
            if ( $ref->getAttributeType('RelatedColor') != '' ) {
                $widget->setColors($ref->getAll()->fieldToArray('RelatedColor'));
            }
        }

		if ( $this->getDemo() ) {
			$widget->setColors(
					array(
							'rgb(128,128,128)'
					)
			);
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
	    if ( !is_object($widget) ) throw new Exception("Chart widget is undefined");

	    $aggs = $this->getAggregates();
	    
	    $data = FlotChartDataSource::getData($this->getIteratorRef(), $aggs);

	    $widget->setData( $data );

    	$chart_id = "chart".uniqid();
	    
		echo '<div style="float:left;width:67%;">';

    		echo '<div id="'.$chart_id.'" class="plot plot-wide" style="'.$this->getStyle().'"></div>';
		    $widget->setLegend( $this->getLegendVisible() );
    		$widget->draw($chart_id);
		
		echo '</div>';

        if ( count($aggs) == 2 && $this->getTableVisible() )
        {
            echo '<div style="clear:both;"></div>';
            echo '<div style="padding:16px 22px 0;">';
                $this->drawLegendTable( $data, $aggs );
            echo '</div>';
        }
        else if ( count($aggs) < 2 && $this->getTableVisible() )
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

    function drawLegendTable( $data, & $aggs )
    {
        $object = $this->getObject();
        $agg_rows = $aggs[0];
        $agg_cols = $aggs[1];

        echo '<table class="table table-hover">';
        $agg_title = '';
        $attribute = $agg_cols->getAttribute();
        if ($attribute != '' && $attribute != '1') {
            $agg_title .= translate($object->getAttributeUserName($attribute));
        }
        $attribute = $agg_rows->getAttribute();
        if ($attribute != '' && $attribute != '1') {
            $agg_title .= ' \ '. translate($object->getAttributeUserName($attribute));
        }
        echo '<tr>';
        echo '<th>'.$agg_title.'</th>';
        foreach ($data as $column => $item) {
            echo '<th>' . $column . '</th>';
        }
        echo '</tr>';
        $tmp = array_shift(array_values($data));
        if ( is_array($tmp) ) {
            $rows = array_keys($tmp['data']);
            foreach ($rows as $row_name) {
                echo '<tr>';
                echo '<td>' . $row_name . '</td>';
                foreach ($data as $column => $item) {
                    echo '<td>' . $data[$column]['data'][$row_name] . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</table>';
    }
	
	function IsNeedNavigator()
	{
		return false;
	}

	function getRenderParms()
	{
		return array_merge(
			parent::getRenderParms(),
			array (
				'demo_hint' => $this->getDemo() ? text(2095) : ''
			)
		);
	}

	function render( $view, $parms )
	{
		echo $view->render("core/PageChart.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}
}