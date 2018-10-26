<?php

class PMPageChart extends PageChart
{
    function getChartWidget()
    {
	    $report_it = getFactory()->getObject('PMReport')->getExact($this->getTable()->getReport());
	    
	    if ( $report_it->get('WidgetClass') == '' ) return parent::getChartWidget();
	    
	    if ( !class_exists($report_it->get('WidgetClass')) )
	    {
	        throw new Exception('Unknown chart widget class name: '.$report_it->get('WidgetClass'));
	    }
	    
	    $className = $report_it->get('WidgetClass');
	    
	    return new $className; 
    }

	protected function getDemoData( $aggs )
	{
		if ( $this->getAggregateBy() == 'State' && $this->getGroup() == 'history' ) {
			return $this->getCommulativeFlowDemoData($aggs);
		}
		return parent::getDemoData( $aggs );
	}

	protected function getCommulativeFlowDemoData( $aggs )
	{
		$this->setDemo();

		$x_attribute = 'DayDate';
		$y_attribute = $aggs[1]->getAggregateAlias();
		$x_value = time();
		$x_delta = 70000;

        $stateObject = $this->getObject() instanceof MetaobjectStatable
            ? getFactory()->getObject($this->getObject()->getStateClassName())
            : getFactory()->getObject('StateBase');

		$state_it = $stateObject->getRegistry()->Query(
            array (
                new FilterBaseVpdPredicate(),
                new SortRevOrderedClause()
            )
		);

        $data = array();
		for( $i = 0; $i < 20; $i++ ) {
			$state_it->moveFirst();
			$max_items = ($i + 1) * 2 + rand($i, $i + 5);
			$value = max(0, ($i + 1) - rand(0, 3));
			while( !$state_it->end() ) {
				$max_items -= $value;
				if ( $state_it->getPos() == $state_it->count() - 1 ) {
					$value += max(0,$max_items);
				}
				$data[] = array (
						$x_attribute => $x_value + $x_delta * ($i + 1),
						$y_attribute => $value,
						'State' => $state_it->getDisplayName()
				);
				if ( $state_it->getPos() > $i / 2) {
					$value = max(0, rand($max_items/2, $max_items));
				}
				else {
					$value = max(0, rand(0, $max_items/3));
				}
				$state_it->moveNext();
			}
		}
		return $data;
	}

    function getExportActions()
    {
        $actions = array();

        $method = new ExcelExportWebMethod();
        $actions[] = array(
            'uid' => 'export-excel',
            'name' => 'Excel',
            'url' => $method->url( $this->getTable()->getCaption() )
        );

        $method = new XmlExportWebMethod();
        $actions[] = array(
            'uid' => 'export-xml',
            'name' => $method->getCaption(),
            'url' => $method->url()
        );

        return $actions;
    }
}