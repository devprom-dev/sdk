<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartWidget.php";

class PlanChartWidget extends FlotChartWidget
{
	private $iterator = null;
	
	public function __construct( $iterator )
	{
		$this->iterator = $iterator;
		parent::__construct();
	}
	
    public function draw( $chart_id )
    {
    	$start = strftime('%Y-%m-%d', strtotime('-7 day', strtotime(SystemDateTime::date('Y-m-d'))));
    	$finish = strftime('%Y-%m-%d', strtotime('+1 month', strtotime(SystemDateTime::date('Y-m-d'))));
    	
    	?>
		<link rel="stylesheet" href="/scripts/vis/vis.min.css?v=<?=$_SERVER['APP_VERSION']?>" />
		<script src="/plugins/ee/resources/js/moment-with-locales.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/scripts/vis/vis.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/plugins/ee/resources/js/handlebars-v2.0.0.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
	   	<script id="uid-template" type="text/x-handlebars-template">
			<a class="with-tooltip" placement="bottom" info="/pm/{{project}}/tooltip/{{objectclass}}/{{objectid}}" href="/pm/{{project}}/{{letter}}-{{objectid}}">[{{letter}}-{{objectid}}]</a>
			{{content}} 
  		</script>
	   	<script id="base-template" type="text/x-handlebars-template">
			{{title}}
  		</script>
		<script id="release-template" type="text/x-handlebars-template">
			<div style="display:table-cell;vertical-align:top;">
				{{title}} <br/>
				{{{velocity}}} <br/>
				<?=text(1020)?>: {{{maximum}}} <br/>
				<div style="{{estimationStyle}}"><?=text(1021)?>: {{{estimation}}}</div>
			</div>
			<div style="display:table-cell;vertical-align:top;padding-left: 12px;">
				<?php
				$url = getFactory()->getObject('PMReport')->getExact('releaseburndown')->getUrl().'&release={{objectid}}';
				$releaseBurndownId = 'chart'.md5($url);
				echo '<div id="'.$releaseBurndownId.'{{objectid}}" class="plot" url="'.$url.'" style="height:90px;width:210px;"></div>';
				?>
			</div>
		</script>
		<script id="iteration-template" type="text/x-handlebars-template">
			{{title}}
			<?php
				$url = getFactory()->getObject('PMReport')->getExact('iterationburndown')->getUrl().'&release={{objectid}}';
				$iterationBurndownId = 'chart'.md5($url);
				echo '<div id="'.$iterationBurndownId.'{{objectid}}" class="plot" url="'.$url.'" style="height:90px;width:210px;"></div>';
			?>
			{{{velocity}}} <br/>
			<?=text(1020)?>: {{{maximum}}} <br/>
			<div style="{{estimationStyle}}"><?=text(1021)?>: {{{estimation}}}</div>
		</script>
		<?php
		$items = $this->getItems();

		foreach( $items as $item ) {
			if ( $item['objectclass'] == 'Iteration' ) {
				$flot = new FlotChartBurndownWidget();
				$flot->setUrl( getSession()->getApplicationUrl().'chartburndown.php?release_id='.$item['objectid'].'&json=1' );
				$flot->draw($iterationBurndownId.$item['objectid']);
			}
			if ( $item['objectclass'] == 'Release' ) {
				$flot = new FlotChartBurndownWidget();
				$flot->setUrl( getSession()->getApplicationUrl().'chartburndownversion.php?version='.$item['objectid'].'&json=1' );
				$flot->draw($releaseBurndownId.$item['objectid']);
			}
		}
		?>
	    <script type="text/javascript">
    		var templates = {
				'uid-template': Handlebars.compile(document.getElementById('uid-template').innerHTML),
				'base-template': Handlebars.compile(document.getElementById('base-template').innerHTML),
				'iteration-template': Handlebars.compile(document.getElementById('iteration-template').innerHTML),
				'release-template': Handlebars.compile(document.getElementById('release-template').innerHTML)
    		};
    		var dataSet = new vis.DataSet(<?=JsonWrapper::encode($items)?>);
			
			var container = document.getElementById('<?=$chart_id?>');
			var options = {
				 start: '<?=$start?>',
				 end: '<?=$finish?>',
				 editable: {
				 	 add: false,
					 updateGroup: false,
					 remove: false,
					 updateTime: true
				 },
				 groupOrder: 'content',
				 locales: {
					    // create a new locale (text strings should be replaced with localized strings)
					    ru: {
					      current: '<?='Текущее'?>',
					      time: '<?='время'?>',
					    },
					    en: {
						      current: 'Current',
						      time: 'time',
						}
					  },
				 locale: '<?=strtolower(getSession()->getLanguageUid())?>',
				 template: function (item) {
					    var template = templates[item.template];
					    return template(item);                  
				 },
				 onMove: function(item, callback) {
				 	if ( typeof item.modifyUrl != 'undefined' ) {
						item.modifyUrl = item.modifyUrl.replace('{{start}}', (new Date(item.start)).toString('yyyy-MM-dd'));
						if ( typeof item.end != 'undefined' ) {
							item.modifyUrl = item.modifyUrl.replace('{{end}}', (new Date(item.end)).toString('yyyy-MM-dd'));
						}
						window.location = item.modifyUrl;
					}
					callback(item);
				 }
			};
			
			var timeline = new vis.Timeline( container, dataSet,
				new vis.DataSet(<?=JsonWrapper::encode($this->getGroups())?>),
				options
			);
			timeline.on('doubleClick', function( data ) {
				var item = dataSet.get(data.item);
				if ( item != undefined && typeof item.url != 'undefined' ) {
					window.location.replace(item.url);
				}
			});
	   	</script>
	   	<?php
    }
    
    protected function getGroups()
    {
        $groups = array();
    	
    	$project_ids = $this->iterator->fieldToArray('Project');
    	if ( count($project_ids) > 0 ) {
			$registry = getFactory()->getObject('Project')->getRegistry();
			$registry->setPersisters(array());
	    	$project_it = $registry->Query(
				array (
					new FilterInPredicate($project_ids),
					new SortAttributeClause('Importance'),
					new SortAttributeClause('Caption')
				)
	    	);
	    	while( !$project_it->end() )
	    	{
	    		$groups[] = array (
					'id' => $project_it->getId(),
					'content' => $project_it->getDisplayName()
	    		);
	    		$project_it->moveNext(); 
	    	}
    	}
    	return $groups;
    }
    
    protected function getItems()
    {
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        $project_ids = $this->iterator->fieldToArray('Project');
    	if ( count($project_ids) > 0 ) {
			$registry = getFactory()->getObject('Project')->getRegistry();
			$registry->setPersisters(array());
	    	$project_it = $registry->Query(
				array (
					new FilterInPredicate($project_ids)
				)
	    	);
	    	while( !$project_it->end() )
	    	{
	    		$groups[$project_it->getId()] = $project_it->get('CodeName');
	    		$project_it->moveNext(); 
	    	}
    	}

		$release_it = getFactory()->getObject('Release')->getRegistry()->Query(
			array(
				new FilterInPredicate($this->iterator->idsToArray())
			)
		);
    	$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
    		array(
    			new FilterInPredicate($this->iterator->idsToArray())
			)
		);
		$milestone_it = getFactory()->getObject('Milestone')->getRegistry()->Query(
			array(
				new FilterInPredicate($this->iterator->idsToArray())
			)
		);

    	$items = array();
    	while( !$this->iterator->end() )
    	{
    		$item = array (
				'id' => $this->iterator->get('ObjectClass').':'.$this->iterator->getId(),
				'content' => $this->iterator->getHtmlDecoded('Caption'),
				'start' => date('Y-m-d H:i:s', strtotime('+9 hours', strtotime($this->iterator->getDateFormatUser('StartDate', '%Y-%m-%d')))),
				'startText' => getSession()->getLanguage()->getDateFormattedShort($this->iterator->get('StartDate')),
				'end' => date('Y-m-d H:i:s', strtotime('+20 hours', strtotime($this->iterator->getDateFormatUser('FinishDate', '%Y-%m-%d')))),
				'endText' => getSession()->getLanguage()->getDateFormattedShort($this->iterator->get('FinishDate')),
				'group' => $this->iterator->get('Project'),
				'objectid' => $this->iterator->getId(),
				'objectclass' => $this->iterator->get('ObjectClass'),
				'template' => 'base-template',
				'project' => $groups[$this->iterator->get('Project')]
    		);

			switch( $this->iterator->get('ObjectClass') ) 
			{
			    case 'Release':
					$item['title'] = translate('Релиз').' '.$item['content'];
					$item['className'] = 'hie-release';

					if ( $methodology_it->HasPlanning() ) {
						$item['type'] = 'background';
						$item['template'] = 'base-template';
					}
					else {
						$item['template'] = 'release-template';
					}

					$release_it->moveToId($this->iterator->getId());

					if ( $methodology_it->HasVelocity() ) {
						$item = array_merge( $item,
							$this->getReleaseMetrics($release_it)
						);
					}

					$method = new ObjectModifyWebMethod($release_it);
					$item['url'] = $method->getJSCall();

					$method = new ModifyAttributeWebMethod($release_it, 'StartDate', '{{start}}');
					$method->setCallback('donothing');
					$item['modifyUrl'] = $method->getJSCall(
						array (
							'FinishDate' => '{{end}}'
						)
					);

			    	break;
			    
			    case 'Iteration':
					$item['title'] = translate('Итерация').' '.$item['content'];
					$item['template'] = 'iteration-template';

					if ( $methodology_it->HasReleases() ) {
						$item['className'] = 'hie-iteration-overlapped';
					}
					else {
						$item['className'] = 'hie-iteration';
					}

					$iteration_it->moveToId($this->iterator->getId());
					$item = array_merge( $item,
						$this->getIterationMetrics($iteration_it)
					);

					$method = new ObjectModifyWebMethod($iteration_it);
					$item['url'] = $method->getJSCall();

					$method = new ModifyAttributeWebMethod($iteration_it, 'StartDate', '{{start}}');
					$method->setCallback('donothing');
					$item['modifyUrl'] = $method->getJSCall(
						array (
							'parms' => array (
								'FinishDate' => '{{end}}'
							)
						)
					);

					break;
			    	
			    case 'Milestone':
			    	$item['className'] = join(' ', 
			    		array(
			    			'hie-milestone',
			    			strlen($item['content']) > 20 ? 'overflow' : ''
			    		)
			    	);
			    	$item['letter'] = 'M';
			    	$item['start'] = $this->iterator->get('FinishDate');
			    	$item['template'] = 'uid-template';

					$milestone_it->moveToId($this->iterator->getId());
					$method = new ObjectModifyWebMethod($milestone_it);
					$item['url'] = $method->getJSCall();

					$method = new ModifyAttributeWebMethod($milestone_it, 'MilestoneDate', '{{start}}');
					$method->setCallback('donothing');
					$item['modifyUrl'] = $method->getJSCall();

					unset($item['end']);
			    	break;
			}
			$items[] = $item;
			$this->iterator->moveNext();
    	}
    	$this->iterator->moveFirst();
    	
    	return $items;
    }

    protected function getIterationMetrics( $object_it )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		$strategy = $methodology_it->getEstimationStrategy();

		if ( $methodology_it->RequestEstimationUsed() ) {
			list( $capacity, $maximum, $actual_velocity ) = $object_it->getEstimationRealBurndownMetrics();
			$estimation = $object_it->getLeftEstimation();
		}
		else {
			list( $capacity, $maximum, $actual_velocity ) = $object_it->getRealBurndownMetrics();
			$estimation = $object_it->getTotalWorkload();
		}
		if ( $estimation == '' ) $estimation = 0;

		$show_limit = SystemDateTime::date() <= $object_it->get('EstimatedFinishDate') || $object_it->get('UncompletedTasks') > 0;

		return array (
			'velocity' => str_replace('%1', round($object_it->getVelocity(), 0), $strategy->getVelocityText($object_it->object)),
			'maximum' => $strategy->getDimensionText(round($maximum, 1)),
			'estimation' => $strategy->getDimensionText(round($estimation,0)),
			'estimationStyle' => ($show_limit && $estimation > $maximum ? 'color:red;' : '')
		);
	}

	protected function getReleaseMetrics( $object_it )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		$strategy = $methodology_it->getEstimationStrategy();

		$estimation = $object_it->getTotalWorkload();
		list( $capacity, $maximum, $actual_velocity ) = $object_it->getRealBurndownMetrics();
		$show_limit = SystemDateTime::date() <= $object_it->get('EstimatedFinishDate') || $object_it->get('UncompletedIssues') > 0;

		return array (
			'velocity' => str_replace('%1', round($object_it->getVelocity(), 1), $strategy->getVelocityText($object_it->object)),
			'maximum' => $strategy->getDimensionText(round($maximum, 1)),
			'estimation' => $strategy->getDimensionText(round($estimation)),
			'estimationStyle' => ($show_limit && $maximum > 0 && $estimation > $maximum ? 'color:red;' : '')
		);
	}
}