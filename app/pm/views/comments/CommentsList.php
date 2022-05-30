<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/PMDetailsList.php";

class CommentsList extends PMDetailsList
{
    private $editor = null;
    private $parser = null;
    private $displayedIds = array();

	function extendModel() {
        parent::extendModel();

        $this->editor = WikiEditorBuilder::build();
        $this->parser = $this->editor->getHtmlParser();
    }

	function drawCell( $object_it, $attr )
    {
	    $this->drawComments($object_it->copy(), $object_it->getId(), $object_it->getAnchorIt());
	}

	function drawComments( $objectIt, $rootId, $anchorIt, $level = 0 )
    {
        while( !$objectIt->end() ) {
            if ( $objectIt->get('Closed') == 'Y' ) {
                $objectIt->moveNext();
                continue;
            }

            $uid = md5($anchorIt->getId().$objectIt->getId());
            if ( in_array($uid, $this->displayedIds) ) {
                $objectIt->moveNext();
                continue;
            }

            $this->displayedIds[] = $uid; // skip displayed comments when a filter is applied

            $copyIt = $objectIt->copy();
            $this->editor->setObjectIt($copyIt);
            $this->parser->setObjectIt($copyIt);

            echo '<div class="comment-well" style="margin-left:'.(min($level,7) * 20).'px;" item="'.$anchorIt->getId().'" object-id="'.$rootId.'">';
                echo '<div class="dates">';
                    echo '<div class="plus-minus-toggle" data-toggle="collapse" href="#commentcontainer'.$uid.'"></div>';
                    echo $this->getRenderView()->render('core/UserPictureMini.php', array (
                        'id' => $objectIt->get('AuthorId'),
                        'image' => 'userpics-mini',
                        'class' => 'user-mini',
                        'title' => $objectIt->get('AuthorName')
                    ));
                    echo $objectIt->getDateFormattedShort('RecordCreated').', '.$objectIt->getTimeFormat('RecordCreated').', '.$objectIt->get('AuthorName');
                echo '</div>';

                echo '<div id="commentcontainer'.$uid.'" class="collapse in">';
                    echo '<div class="wysiwyg reset" onclick="javascript:annotationSelectComment('.$rootId.','.$anchorIt->getId().');" >';
                        echo $this->parser->parse($objectIt->getHtmlDecoded('Caption'));
                    echo '</div>';

                    $method = new CommentWebMethod( $anchorIt );
                    if ( $method->hasAccess() ) {
                        $url = "workflowNewObject('".getSession()->getApplicationUrl($anchorIt).'comment/'.$objectIt->getId().'/reply'."','Comment','Comment','',[],devpromOpts.UpdateUI);";
                        echo '<a class="btn btn-link pull-left" href="javascript:'.$url.'">' . translate('ответить') . '</a>';
                    }
                    $method = new ObjectModifyWebMethod($copyIt);
                    if ( $method->hasAccess() ) {
                        $method->setObjectUrl(
                            getSession()->getApplicationUrl($copyIt).'comments/'.
                            strtolower(get_class($anchorIt->object)).'/'.$anchorIt->getId().'?action=show'
                        );
                        echo '<a class="btn btn-link pull-left" href="javascript:'.$method->getJSCall().'">' . translate('изменить') . '</a>';
                    }
                    $method = new ModifyAttributeWebMethod($copyIt, 'Closed', 'Y');
                    if ( $method->hasAccess() ) {
                        echo '<a class="btn btn-link pull-right" href="'.$method->getJSCall().'" title="'.translate('Завершить').'"><i class="icon-ok"></i></a>';
                    }
                    echo '<div class="clearfix"></div>';
                echo '</div>';
            echo '</div>';

            $this->drawComments($objectIt->getThreadIt(), $rootId, $anchorIt, $level + 1);
            $objectIt->moveNext();
        }
    }

    function getNoItemsMessage()
    {
        return '';
    }
}
