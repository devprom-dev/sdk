<?php

include SERVER_ROOT_PATH."core/classes/FlotChartDataSource.php";
include SERVER_ROOT_PATH."core/classes/schedule/DateYearWeekModelBuilder.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBarWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartLineWidget.php";
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartMultiLineWidget.php";
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

	function buildIterator()
	{
		$minSizeValuable = 1;

		$object = $this->getObject();

        $predicates = $this->getPredicates( $this->getFilterValues() );
        $ids = $this->getIds();
        if ( count($ids) > 0 ) {
            $predicates[] = new FilterInPredicate($ids);
        }
        $predicates[] = new FilterVpdPredicate();

        foreach( $predicates as $predicate ) {
            $object->addFilter($predicate);
        }

		$aggs = $this->getAggregates();
		foreach ( $aggs as $agg ) {
			$object->addAggregate( $agg );
		}

	    if ( $this->getGroup() == 'history' )
		{
			$values = $this->getFilterValues();
		    $object->addFilter(new FilterClusterPredicate($values['modifiedafter']));
		    $it = $object->getAggregatedHistory( $object->getFilters() );
		}
		elseif ( count($aggs) > 0 )
		{
			$it = $object->getAggregated('t');
		}
		else {
            $it = $object->getEmptyIterator();
        }

        $aggby = $this->getAggregateBy();
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
		if ( $object instanceof MetaobjectStatable && ($this->getGroup() == 'State' || $aggby == 'State') )
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

		if ( count($data) < $minSizeValuable ) {
            $this->setDemo(true);
            return $object->createCachedIterator($this->getDemoData($aggs));
        }
        else {
            return $object->createCachedIterator($data);
        }
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
			return $this->getGroup() != $values['aggby'] ? $values['aggby'] : '1';
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

	function getChartFields()
    {
        $object = $this->getObject();
        $attrs = $object->getAttributes();
        $fields = array();

        $skip_attributes = array_merge(
            $this->getSystemAttributes(),
            $this->getObject()->getAttributesByGroup('skip-chart')
        );

        $clause = $object->getRegistry()->getSelectClause('', false);
        $skip_types = array('','text','wysiwyg','largetext','char','varchar','date','datetime');

        foreach ( $attrs as $key => $attr )
        {
            if ( $key == 'OrderNum' ) continue;
            if ( in_array($key, $skip_attributes) ) continue;
            if ( $key != 'DocumentId' && !$this->object->IsAttributeStored( $key ) && !preg_match('/\)\s+\`?'.$key.'\`?\s+,/', $clause) ) continue;
            if ( $key != 'State' && in_array($this->object->getAttributeType($key), $skip_types) ) continue;

            array_push( $fields, $key );
        }
        return $fields;
    }

	function getGroupFields()
	{
        $fields = $this->getChartFields();
		$fields[] = 'RecordModified';
		$fields[] = 'RecordCreated';
		return $fields;
	}
	
	function getAggByFields()
	{
	    return $this->getChartFields();
	}
	
	function HasRows()
	{
		return false;
	}

	function getChartSettings()
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
                    $groups[translate($name)] = array ( 'click' => $script, 'checked' => $used_group == $field );
                }
            }

            ksort($groups);
            $group_actions = array();

            foreach ( $groups as $caption => $group )
            {
                $group_actions[] = array (
                    'click' => $group['click'],
                    'name' => $caption,
                    'checked' => $group['checked']
                );
            }

            if ( count($group_actions) > 0 )
            {
                $script = "javascript: filterLocation.setup( 'group=history', 0 ); ";

                array_push( $group_actions,
                    array (),
                    array ( 'click' => $script, 'name' => translate('Дата'),
                        'checked' => $used_group == 'history' )
                );

                array_push($actions, array (
                    'name' => 'group-by',
                    'title' => text(2482),
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

            $script = "javascript: filterLocation.setup( 'aggby=".$field."', 0 ); ";

            $columns[translate($name)] = array(
                'click' => $script, 'checked' => $filter_values['aggby'] == $field );
        }

        ksort($columns);
        $column_actions = array();

        foreach( $columns as $caption => $column )
        {
            array_push( $column_actions,
                array ( 'click' => $column['click'], 'name' => $caption,
                    'checked' => $column['checked'] )
            );
        }

        if ( count($column_actions) > 0 )
        {
            array_push($actions, array ( 'title' => text(2483),
                'items' => $column_actions , 'name' => 'agg-by' ) );
        }

        // aggregators
        if ( $filter_values['aggregator'] == '' ) $filter_values['aggregator'] = $this->getAggregator();

        $fields = $this->getAggregators();
        $columns = array();

        foreach ( $fields as $key => $field )
        {
            $script = "javascript: filterLocation.setup( 'aggregator=".$key."', 0 ); ";

            $columns[translate($field)] = array(
                'click' => $script, 'checked' => $filter_values['aggregator'] == $key );
        }

        ksort($columns);
        $column_actions = array();

        foreach( $columns as $caption => $column )
        {
            array_push( $column_actions,
                array ( 'click' => $column['click'], 'name' => $caption,
                    'checked' => $column['checked'] )
            );
        }

        if ( count($column_actions) > 0 )
        {
            $script = "javascript: filterLocation.setup( 'aggregator=none', 0 ); ";

            array_push( $column_actions,
                array (),
                array ( 'click' => $script, 'name' => translate('нет'),
                    'checked' => $filter_values['aggregator'] == 'none' )
            );

            array_push($actions, array ( 'title' => translate('Агрегация'),
                'items' => $column_actions , 'name' => 'aggregator' ) );
        }

        // chart options
        if ( $filter_values['chartlegend'] == '' ) $filter_values['chartlegend'] = 'none';
        if ( $filter_values['chartdata'] == '' ) $filter_values['chartdata'] = 'none';

        $column_actions = $this->getOptions($filter_values);
        if ( count($column_actions) > 0 ) {
            array_push($actions, array ( 'title' => translate('Опции'),
                'items' => $column_actions , 'name' => 'chart-options' ) );
        }

        return $actions;
    }

	function buildFilterActions( & $base_actions )
	{
	}

	function getOptions( $filter_values )
    {
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
        return $column_actions;
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
                case 'sum':
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

        if ( $this->getObject()->IsReference($color_attribute) )
        {
            $ref = $this->getObject()->getAttributeObject($color_attribute);
            if ( $ref->getAttributeType('RelatedColor') != '' ) {
                $builtinColors = array (
                    'rgb(255, 102, 0)', 'rgb(153, 204, 0)', 'rgb(255, 204, 0)', 'rgb(89, 143, 239)', 'rgb(255, 153, 204)', 'rgb(0, 255, 0)', 'rgb(255, 255, 0)'
                );
                $colors = array();
                $colorIt = $ref->getAll();
                while( !$colorIt->end() ) {
                    $colors[$colorIt->getDisplayName()] = $colorIt->get('RelatedColor') != ''
                        ? $colorIt->get('RelatedColor')
                        : array_shift($builtinColors);
                    $colorIt->moveNext();
                }
                if ( !$this->getObject()->IsAttributeRequired($color_attribute) ) {
                    $colors = array_merge(
                        array(
                            translate('нет') => 'rgb(192,192,192)'
                        ),
                        $colors
                    );
                }
                $widget->setColors($colors);
            }
        }
        if ( $color_attribute == 'State' ) {
            $state_it = \WorkflowScheme::Instance()->getStateIt($this->getObject());
            $colors = array();
            while( !$state_it->end() ) {
                $colors[$state_it->getDisplayName()] = $state_it->get('RelatedColor');
                $state_it->moveNext();
            }
            $widget->setColors($colors);
        }
		return $widget;
	}
	
	function getStyle()
	{
		return 'height:420px;';
	}

	function buildData( $aggs ) {
        return FlotChartDataSource::getData($this->getIteratorRef(), $aggs);
    }

	function buildDataIterator()
    {
        $aggs = $this->getAggregates();
        $data = $this->buildData($aggs);

        $object = $this->getObject();

        $entity = new \Metaobject('entity');
        foreach( $entity->getAttributes() as $attribute => $info ) {
            $entity->removeAttribute($attribute);
        }

        $agg_rows = $aggs[0];
        $agg_cols = $aggs[1];

        $agg_title = '';
        if ( is_object($agg_cols) ) {
            $attribute = $agg_cols->getAttribute();
            if ($attribute != '' && $attribute != '1') {
                $agg_title .= translate($object->getAttributeUserName($attribute));
            }
        }
        if ( is_object($agg_rows) ) {
            $attribute = $agg_rows->getAttribute();
            if ($attribute != '' && $attribute != '1') {
                if ( $agg_title != '' ) {
                    $agg_title .= ' \\ ';
                }
                $agg_title .= translate($object->getAttributeUserName($attribute));
            }
        }

        $columns = array(
            $agg_title
        );
        $entity->addAttribute($agg_title, 'VARCHAR', $agg_title, true, true);
        foreach ($data as $column => $item) {
            $columns[] = $column;
            $entity->addAttribute($column, 'VARCHAR', $column, true, true);
        }

        $result = array();
        $rows = array();
        foreach( array_values($data) as $values ) {
            $rows = array_merge($rows, array_keys($values['data']));
        }

        foreach (array_unique($rows) as $row_name) {
            $resultRow = array(
                array_shift(array_values($columns)) => $row_name
            );
            foreach ($data as $column => $item) {
                $resultRow = array_merge(
                    $resultRow,
                    array (
                        $column => $data[$column]['data'][$row_name]
                    )
                );
            }
            $result[] = $resultRow;
        }

        return $entity->createCachedIterator($result);
    }
	
	function draw( $view )
	{
	    $aggs = $this->getAggregates();
	    $data = $this->buildData($aggs);

        $widget = $this->getChartWidget();
        if ( !is_object($widget) ) throw new Exception("Chart widget is undefined");

	    $widget->setData( $data );
        if ( $this->getDemo() ) {
            $widget->setColors(
                array(
                    'rgb(128,128,128)'
                )
            );
        }

    	$chart_id = "chart".uniqid();
        $chartClass = $widget instanceof FlotChartPieWidget ? "" : "plot-wide";
        $chartStyle = $widget->getStyle() != "" ? $widget->getStyle() : $this->getStyle();
	    
        echo '<div id="'.$chart_id.'" class="plot '.$chartClass.'" style="'.$chartStyle.'"></div>';
        $widget->setLegend( $this->getLegendVisible() );
        $widget->draw($chart_id);
		
        if ( count($aggs) == 2 && $this->getTableVisible() )
        {
            echo '<div style="clear:both;"></div>';
            echo '<div style="padding:16px 22px 0;">';
                $this->drawLegendTable( $view );
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

			if ( !in_array($attribute, array('', '1', $agg->getAttribute())) ) {
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

    function drawLegendTable( $view )
    {
        $dataIt = $this->buildDataIterator();

        echo '<table class="table table-hover">';
        echo '<tr>';
        foreach (array_keys($dataIt->getData()) as $column ) {
            echo '<th>' . $column . '</th>';
        }
        $actions = $this->getExportActions();
        if ( $dataIt->count() > 0 ) {
            if ( count($actions) > 0 ) {
                echo '<th width="1%">';
                echo $view->render('core/ButtonMenu.php', array(
                    'title' => translate('Экспорт'),
                    'items' => $actions
                ));
                echo '</th>';
            }
        }
        echo '</tr>';
        while( !$dataIt->end() ) {
            echo '<tr>';
            foreach ($dataIt->getData() as $column => $value) {
                if ( $value == '' ) $value = text(2536);
                echo '<td>' . $value . '</td>';
            }
            if ( count($actions) > 0 ) {
                echo '<td></td>';
            }
            echo '</tr>';
            $dataIt->moveNext();
        }
        echo '</table>';
    }
	
	function IsNeedNavigator()
	{
		return false;
	}

	function IsNeedToSelect()
    {
        return false;
    }

	function getExportActions()
    {
        return array();
    }

	function getRenderParms()
	{
		return array_merge(
			parent::getRenderParms(),
			array (
				'demo_hint' => $this->getDemo() ? text(2095) : '',
                'chartSettingsItems' => $this->getChartSettings()
			)
		);
	}

	function render( $view, $parms )
	{
		echo $view->render("core/PageChart.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}
}