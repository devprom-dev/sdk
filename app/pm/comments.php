<?php

include('header.php');
include('views/comments/CommentsPage.php');
 
$page = new CommentsPage();
 
$page->render();
