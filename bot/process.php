<?php

require_once 'config.php';
require_once 'IRCBot.php';

set_time_limit(0);
ini_set('display_errors', 'on');

$bot = new IRCBot($config);
$bot->run();
