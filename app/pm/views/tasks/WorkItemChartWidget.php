<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartWidget.php";

class WorkItemChartWidget extends FlotChartWidget
{
	private $iterator = null;
	private $userIt = null;
	private $start = '';
	private $finish = '';
	
	public function __construct( $iterator, $start, $finish )
	{
		$this->iterator = $iterator;
		$assignee = $this->iterator->fieldToArray('Assignee');
		if ( count($assignee) < 1 ) {
            $this->userIt = getFactory()->getObject('User')->getEmptyIterator();
        }
		else {
            $this->userIt = getFactory()->getObject('User')->getRegistry()->Query(
                array(
                    new FilterInPredicate($assignee)
                )
            );
        }
		$this->start = $start;
		$this->finish = $finish;
		parent::__construct();
	}

    public function draw( $chart_id )
    {
    	?>
		<link rel="stylesheet" href="/scripts/vis/vis.min.css?v=<?=$_SERVER['APP_VERSION']?>" />
		<script src="/scripts/vis/moment-with-locales.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/scripts/vis/vis.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/scripts/vis/handlebars-v4.1.0.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script id="task-template" type="text/x-handlebars-template">
            {{uidText}} {{{title}}}
		</script>

		<?php
		$items = $this->getItems();
		?>
	    <script type="text/javascript">
    		var templates = {
				'task-template': Handlebars.compile(document.getElementById('task-template').innerHTML)
    		};
    		var dataSet = new vis.DataSet(<?=JsonWrapper::encode($items)?>);
			
			var container = document.getElementById('<?=$chart_id?>');
			var options = {
				 start: '<?=$this->start?>',
				 end: '<?=$this->finish?>',
				 editable: false,
                 margin: {
					item: {
						horizontal: -1
					}
				},
                hiddenDates: [
                    {start: '2013-10-26 00:00:00', end: '2013-10-28 00:00:00', repeat: 'weekly'}, // weekends
                    {start: '2013-03-29 20:00:00', end: '2013-03-30 09:00:00', repeat: 'daily'} // night time
                ],
				groupOrder: 'index',
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
        $index = 0;

        $this->userIt->moveFirst();
        while( !$this->userIt->end() ) {
            $groups[$this->userIt->getId()] = array (
                'id' => $this->userIt->getId(),
                'content' => $this->userIt->getDisplayName(),
                'index' => $index++
            );
            $this->userIt->moveNext();
        }

    	return array_values($groups);
    }
    
    protected function getItems()
    {
    	$items = array();
    	$start = array();
    	$end = array();
    	$uid = new ObjectUID();

    	$projectAccessible = new ProjectAccessible();
    	$accessibleVpds = $projectAccessible->getAll()->fieldToArray('VPD');

    	while( !$this->iterator->end() )
    	{
    	    $userId = $this->iterator->get('Assignee').$this->iterator->get('VPD');
    	    $objectIt = $this->iterator->getObjectIt();

    	    $start[$userId] = $this->iterator->getDateFormatUser('StartDate', '%Y-%m-%d %H:%M:%S');
    	    if ( $start[$userId] == '' ) {
    	        if ( $end[$userId] != '' ) {
                    $start[$userId] = date('Y-m-d H:i:s', strtotime('+10 minute', strtotime($end[$userId])));
                }
                else {
                    $start[$userId] = date('Y-m-d H:i:s');
                }
                $start[$userId] = max($start[$userId], $this->iterator->getDateFormatUser('PlannedStartDate', '%Y-%m-%d %H:%M:%S'));
            }

            $end[$userId] = $this->iterator->getDateFormatUser('FinishDate', '%Y-%m-%d %H:%M:%S');
    	    if ( $end[$userId] == '' ) {
                $leftHours = $this->iterator->get('LeftWork');
                if ( $leftHours == '' ) $leftHours = $this->iterator->get('Planned');
                if ( $leftHours == '' ) $leftHours = 1;

                $end[$userId] = date('Y-m-d H:i:s',
                        $this->addWorkingHours(
                                strtotime(max(date('Y-m-d H:i:s'),$start[$userId])),
                                $leftHours,
                                $this->iterator->get('Capacity'),
                                true
                        )
                    );
            }

    		$item = array (
				'id' => $this->iterator->get('ObjectClass').$this->iterator->getId(),
				'start' => date('Y-m-d\TH:i:s', strtotime($start[$userId])),
                'end' => date('Y-m-d\TH:i:s', strtotime($end[$userId])),
				'template' => 'task-template',
				'group' => $this->iterator->get('Assignee'),
                'style' => 'border-color:' . $this->iterator->get('PriorityColor') . ';background-color:' . \ColorUtils::hex2rgb($this->iterator->get('PriorityColor'), 0.3),
                'className' => 'wich'
    		);

    	    if ( in_array($this->iterator->get('VPD'), $accessibleVpds) )
    	    {
                $method = new ObjectModifyWebMethod($objectIt);
                $item['url'] = $method->getJSCall();
                $item['title'] = $this->iterator->get('Caption');
                $item['uidText'] = $uid->getObjectUid($objectIt);
            }
            $items[] = $item;

			$this->iterator->moveNext();
    	}
    	return $items;
    }

    function addWorkingHours($timestamp, $hoursToAdd, $workingHours = 8, $skipWeekends = false)
    {
        // Set constants
        $dayStart = 10;
        $dayEnd = $dayStart + $workingHours;

        // For every hour to add
        for($i = 0; $i < $hoursToAdd; $i++)
        {
            // Add the hour
            $timestamp += 3600;

            // If the time is between 1800 and 0800
            if ((date('G', $timestamp) >= $dayEnd && date('i', $timestamp) >= 0 && date('s', $timestamp) > 0) || (date('G', $timestamp) < $dayStart))
            {
                // If on an evening
                if (date('G', $timestamp) >= $dayEnd)
                {
                    // Skip to following morning at 08XX
                    $timestamp += 3600 * ((24 - date('G', $timestamp)) + $dayStart);
                }
                // If on a morning
                else
                {
                    // Skip forward to 08XX
                    $timestamp += 3600 * ($dayStart - date('G', $timestamp));
                }
            }

            // If the time is on a weekend
            if ($skipWeekends && (date('N', $timestamp) == 6 || date('N', $timestamp) == 7))
            {
                // Skip to Monday
                $timestamp += 3600 * (24 * (8 - date('N', $timestamp)));
            }
        }

        // Return
        return $timestamp;
    }
}