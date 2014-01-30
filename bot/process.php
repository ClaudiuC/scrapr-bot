<?php

require_once 'IRCBot.php';

set_time_limit(0);
ini_set('display_errors', 'on');

$config = array( 
  'server' => 'chat.freenode.net', 
  'port' => 6667, 
  'nick' => 'Skullbot', 
  'name' => 'Skullbot', 
  'channel' => '##skullbox', 
);

$bot = new IRCBot($config);
$bot->run();
