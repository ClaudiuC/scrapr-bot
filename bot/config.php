<?php

$config = array( 
  'server' => 'chat.freenode.net', 
  'port' => 6667, 
  'nick' => 'Skullb0t', 
  'name' => 'Skullb0t', 
  'password' => getenv('IRC_BOT_PASS'),
  'channel' => '##skullbox', 
);

echo getenv('IRC_BOT_PASS');
