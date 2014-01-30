<?php

require_once 'CrawlerTask.php';

$task = new CrawlerTask($argv[0]);
$task->setXpath(CrawlerPath::get('/rss/channel/item/'))->fetch()->store();
