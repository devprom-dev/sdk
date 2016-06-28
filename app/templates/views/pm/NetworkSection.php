<link rel="stylesheet" href="/scripts/vis/vis.min.css" />
<script src="/scripts/vis/vis.min.js" type="text/javascript" charset="UTF-8"></script>

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
                        roundness: 0.1
                    }
                },
                layout:{randomSeed:8}
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
        })
    })
</script>
