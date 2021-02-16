<?php

$view->extend('core/PageBody.php');
$view['slots']->start('_header');
?>
<div class="form-container">
<div class="pull-left dashboard-breadcrumb">
    <?php
        echo $view->render('core/PageTableBreadcrumb.php', array(
            'navigation_url' => $navigation_url,
            'nearest_title' => $nearest_title,
            'title' => $title,
            'filter_actions' => $table->getFilterActions()
        ));
    ?>
</div>

<?php if ( $appendUrl != '' ) { ?>
    <div class=" pull-left dashboard-breadcrumb">
        <a href="<?=$appendUrl?>" class="btn btn-success btn-xs">
            <i class="icon-plus icon-white"></i>
            <?=translate('Добавить')?>
        </a>
    </div>
<? } ?>

<script type="text/javascript" src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/freewall.js"></script>
<div class="layout">
    <div id="freewall" class="free-wall">
        <?php foreach( $cells as $order => $cell ) { ?>
            <div class='cell' style='width:<?=$cell['width']?>px; height:<?=$cell['height']?>px;' data-handle=".handle" self-id="<?=$cell['id']?>" self-order="<?=($order+1)?>">
                <div class='cover'>
                    <div class='handle'>
                        <div class="btn transparent-btn" title="<?=$cell['title']?>">
                            <span class="title"><?=$cell['title']?></span>
                        </div>
                        <a class="btn btn-sm hbtn" href="<?=$cell['url']?>" target="_blank"><i class="icon-resize-full"></i></a>
                        <?php if ( $cell['modifyUrl'] != '' ) { ?>
                            <a href="<?=$cell['modifyUrl']?>" class="btn btn-sm hbtn"><i class="icon-pencil"></i></a>
                        <?php } ?>
                        <?php if ( $cell['deleteUrl'] != '' ) { ?>
                            <a href="<?=$cell['deleteUrl']?>" class="btn btn-sm hbtn"><i class="icon-remove"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="body" url="<?=$cell['url']?>"></div>
            </div>
        <?php } ?>
    </div>
</div>
</div>

<script type="text/javascript">
    var wall = new Freewall("#freewall");
    wall.reset({
        draggable: true,
        selector: '.cell',
        animate: false,
        fixSize: 1,
        cellW: 20,
        cellH: 15,
        onResize: function() {
        },
        onComplete: function() {
            reorderCells();
        }
    });

    wall.fitWidth();
    $(window).trigger("resize");
    drawCells();

    function drawCells() {
        $('.cell .body[url]').each(function() {
            drawCell($(this));
        });
    }

    function drawCell(self) {
        $.get( self.attr('url'),
            {
                tableonly: true,
                dashboard: true,
                height: self.parent('.cell').attr('data-height') - 50,
                width: self.parent('.cell').attr('data-width') - 10
            },
            function(data) {
                self.html($(data).find('.table-master'));
                self.parent('.cell').resizable({
                    minWidth: 130,
                    grid: [25,25],
                    stop: function(e, ui) {
                        var cell = $(ui.element);
                        cell.attr('data-width', cell.width());
                        cell.attr('data-height', cell.height());
                        wall.refresh();
                        drawCell(cell.find('.body'));

                        var resizeUrl = '<?=$resizeUrl?>';
                        if ( resizeUrl != '' ) {
                            resizeUrl = resizeUrl.replace('%id%',cell.attr('self-id'))
                                .replace('%height%', cell.height())
                                    .replace('%width%', cell.width());
                            $.get(resizeUrl, function(data) {
                            });
                        }
                    }
                });
            }
        )
    }

    function reorderCells() {
        var cells = [];
        $('.cell').each(function() {
            var self = $(this);
            cells.push({id: self.attr('self-id'), left: self.position().left, top: self.position().top });
        });
        cells.sort(function(a, b) {
            return a.top != b.top ? a.top - b.top : a.left - b.left;
        });
        $.each(cells, function(index, value) {
            var self = $('.cell[self-id='+ value.id +']');
            var newOrder = (index+1);
            if ( newOrder != self.attr('self-order') ) {
                self.attr('self-order',newOrder);
                var reorderUrl = '<?=$reorderUrl?>';
                if ( reorderUrl != '' ) {
                    reorderUrl = reorderUrl.replace('%id%', value.id).replace('%value%', newOrder);
                    $.get(reorderUrl, function(data) {
                    });
                }
            }
        });
    }
</script>