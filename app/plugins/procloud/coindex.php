<?php

include('common.php');
include('system/c_controller.php');
include('system/c_co_url.php');
include('system/c_co_wiki_parser.php');
include('views/c_co_page.php');
include('views/c_co_form.php');
include('views/c_co_main_view.php');
include('views/c_co_logged_view.php');
include('views/c_co_blog_view.php');
include('views/c_co_files_view.php');
include('views/c_co_project_view.php');
include('views/c_co_desc_view.php');
include('views/c_co_request_view.php');
include('views/c_co_comment_view.php');
include('views/c_co_question_view.php');
include('views/c_co_login_forms.php');
include('views/c_co_user_view.php');
include('views/c_co_message_view.php');
include('views/c_co_catalogue_view.php');
include('views/c_co_news_view.php');
include('views/c_co_create_view.php');
include('views/c_co_about_view.php');
include('views/c_co_poll_view.php');
include('views/c_co_links_view.php');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class IntroPage extends CoPageIntro
{
	function getContent()
	{
		global $_REQUEST;
		
		switch ( $_REQUEST['mode'] )
		{
			case 'room':
				return new CoLoggedContent;
			
			case 'create':
				return new CoCreatePageContent;

			case 'about':
				return new CoAboutPageContent;

			case 'login':
				return new CoLoginController;

			case 'project':
				return new CoProjectPageContent;

			case 'admin':
				exit(header('Location: /admin/activity.php'));

			case '404':
				return new CoPageNotFound;

			case '500':
				return new CoInternalError;

			default:
				return new CoMainContent;
		}
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ( is_object($user_it) && $user_it->IsReal() && $_REQUEST['mode'] != 'about' )
{
	if ( getFactory()->getObject('Project')->getRegistry()->Count(array(new ProjectParticipatePredicate($user_it->getId()))) < 1 )
	{
		exit(header('Location: /projects/welcome'));
	}
	else
	{
		exit(header('Location: /pm/my'));
	}
}
else
{
	$page = new IntroPage;
}

$page->draw();
