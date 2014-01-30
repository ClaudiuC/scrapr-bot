<?php

require_once 'CrawlerTask.php';

set_exception_handler(
  function(Exception $ex) {
    error_log($ex->getMessage());
  }
);

$task = new CrawlerTask($argv[1]);
$task->setXpath('/rss/channel/item')->fetch()->store();
