<link rel="stylesheet" href="/scripts/vis/vis.min.css?v=<?=$_SERVER['APP_VERSION']?>" />
<script src="/scripts/vis/vis.min.js?v=<?=$_SERVER['APP_VERSION']?>" type="text/javascript" charset="UTF-8"></script>

<div id="<?=$id?>"><div class="document-loader"></div></div>

<script type="text/javascript">
    var data = [];
    bindTabHandler('networksection', function () {
        $.getJSON('<?=$networkUrl?>', function(json) {
            data = json;
            var network = new vis.Network(document.getElementById('<?=$id?>'), json, {
                height: ($(window).height() * 4/5 - 100) + 'px',
                edges: {
                    smooth: {
                        type: 'cubicBezier',
                        roundness: 0.1,
                        forceDirection: 'vertical'
                    }
                },
                nodes: {
                    shape: 'box',
                    widthConstraint: {
                        maximum: 200
                    }
                },
                layout:{
                    hierarchical: {
                        direction: 'UD'
                    }
                }
            });
            network.on('doubleClick', function(e) {
                if (e.nodes.length > 0) {
                    $.each(data.nodes, function(index,value) {
                        if ( value.id == e.nodes[0] ) {
                            window.open(value.url, '_blank');
                        }
                    });
                }
            });
            completeUIExt($('<?=$id?>'));
        })
    })
</script>
