<?php

set_time_limit(0);

require_once 'CrawlerTask.php';

set_exception_handler(
  function(Exception $ex) {
    error_log($ex->getMessage());
  }
);

$feeds = array(
  // Chitty Chat
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=33',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=2',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=27',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=57',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=25',

  // Grafica si design
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=41',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=36',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=40',

  // Administrare, configurare si intretinere
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=56',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=11',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=44',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=29',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=37',

  // Development
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=50',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=10',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=39',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=53',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=64',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=99',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=6',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=5',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=34',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=7',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=77',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=8',

  // Entertainment si telecomunicatii
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=30',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=32',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=79',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=88',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=58',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=59',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=94',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=31',
  'http://www.skullbox.info/index.php?type=rss2;action=.xml;sa=recent;limit=10;board=74',
);

foreach ($feeds as $feed_uri) {
  $task = new CrawlerTask($feed_uri);
  $task->setXpath('/rss/channel/item')->fetch()->store();
  usleep(100000);
}
