<?php

 include('header.php');
 include('classes/c_interval_cache.php');
 include('methods/c_date_methods.php');
 include('methods/c_watcher_methods.php');
 include('views/product/FunctionsPage.php');
 
 $page = new FunctionsPage;
 
 $page->render();
