<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartWidget.php";

class DocumentVersionChartWidget extends FlotChartWidget
{
	private $iterator = null;
	
	public function __construct( $iterator )
	{
		$this->iterator = $iterator;
		parent::__construct();
	}
	
    public function draw( $chart_id )
    {
    	$start = strftime('%Y-%m-%d', strtotime('-3 month', strtotime(SystemDateTime::date('Y-m-d'))));
    	$finish = strftime('%Y-%m-%d', strtotime('+1 month', strtotime(SystemDateTime::date('Y-m-d'))));

    	?>
		<link rel="stylesheet" href="/scripts/vis/vis.min.css?v=<?=$_SERVER['APP_VERSION']?>" />
		<script src="/scripts/vis/moment-with-locales.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/scripts/vis/vis.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
		<script src="/scripts/vis/handlebars-v4.1.0.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>
	   	<script id="base-template" type="text/x-handlebars-template">
			{{title}}
  		</script>

	    <script type="text/javascript">
    		var templates = {
				'base-template': Handlebars.compile(document.getElementById('base-template').innerHTML),
    		};
    		var dataSet = new vis.DataSet(<?=JsonWrapper::encode($this->getItems())?>);
			var container = document.getElementById('<?=$chart_id?>');
			var options = {
				 start: '<?=$start?>',
				 end: '<?=$finish?>',
				 editable: false,
				 margin: {
					item: {
						horizontal: 0
					}
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
				 }
			};
			var timeline = new vis.Timeline( container, dataSet,
				new vis.DataSet(<?=JsonWrapper::encode($this->getGroups())?>),
				options
			);
			timeline.on('click', function( data ) {
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
		$uid = new ObjectUID();
        $groups = array();
		while( !$this->iterator->end() )
		{
			$title = $uid->getUidWithCaption($this->iterator);
			$groups[] = array (
				'id' => $this->iterator->getId(),
				'content' => $title
			);
			$this->iterator->moveNext();
		}
    	return $groups;
    }
    
    protected function getItems()
    {
        $items = array();

        $this->iterator->moveFirst();
        while( !$this->iterator->end() )
        {
            $items[] = array (
                'id' => $this->iterator->getId(),
                'title' => $this->iterator->get('DocumentVersion') != ''
                                ? $this->iterator->get('DocumentVersion')
                                : $this->iterator->getDisplayName(),
                'url' => $this->iterator->getUIDUrl(),
                'template' => 'base-template',
                'start' => date('Y-m-d H:i:s', strtotime($this->iterator->getDateFormatUser('RecordCreated', '%Y-%m-%d %H:%M:%S'))),
                'group' => $this->iterator->getId(),
                'template' => 'base-template',
                'className' => 'vis-req-ver'
            );
            $this->iterator->moveNext();
        }

		$item_it = getFactory()->getObject('cms_Snapshot')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectClass', get_class($this->iterator->object)),
				new FilterAttributePredicate('ObjectId', $this->iterator->idsToArray()),
				new FilterAttributeNullPredicate('Type')
			)
		);
    	while( !$item_it->end() )
    	{
			$this->iterator->moveToId($item_it->get('ObjectId'));
			$items[] = array (
				'id' => $item_it->getId(),
				'title' => $item_it->getDisplayName(),
				'url' => $this->iterator->getUIDUrl().'&baseline='.$item_it->getId(),
				'template' => 'base-template',
				'start' => date('Y-m-d H:i:s', strtotime($item_it->getDateFormatUser('RecordCreated', '%Y-%m-%d %H:%M:%S'))),
				'group' => $item_it->get('ObjectId'),
				'template' => 'base-template',
				'className' => 'vis-req-ver'
    		);
			$item_it->moveNext();
    	}
    	$this->iterator->moveFirst();
    	
    	return $items;
    }
}