<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/comments/CommentsPage.php";

class CommentController extends PageController
{
	public function replyAction(Request $request)
    {
        $_REQUEST['PrevComment'] = $request->get('id');

        $page = new \CommentsPage();
        $page->getFormRef()->setCommentIt(
            getFactory()->getObject('Comment')->getExact($request->get('id'))
        );

        return $this->responsePage($page);
	}
}