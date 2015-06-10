<?php

 include('header.php');
 include('methods/c_date_methods.php');
 include('methods/ViewSpentTimeWebMethod.php');
 include('views/reports/ReportSpentTimePage.php');

 $page = new ReportSpentTimePage;
 $page->render();
