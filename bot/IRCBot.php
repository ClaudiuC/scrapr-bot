<?php

final class IRCBot {
  private $socket;
  private array $ex = array();

  public function __construct(array $config) {
    $this->socket = fsockopen($config['server'], $config['port']);
    $this->login($config);
    $this->run();
    $this->send('JOIN', $config['channel']);
  }

  private function login(array $config) {
    $this->send(
      'USER', 
      $config['nick'].' '.$config['nick'].' :'.$config['name']
    );
    $this->send('NICK', $config['nick']);
  }

  private function run() {
    $data = fgets($this->socket, 128);
    echo nl2br($data);
    flush();

    $this->ex = explode(' ', $data);
    if($this->ex[0] == 'PING') {
      $this->send(
        'PONG', 
        $this->ex[1]
      ); 
    }

    $command = str_replace(array(chr(10), chr(13)), '', $this->ex[3]);
    $this->main();
  }

  function send(string $cmd, string $msg = null) {
    if($msg == null) {
      fputs($this->socket, $cmd."\r\n");
    } else {
      fputs($this->socket, $cmd.' '.$msg."\r\n");
    }
  }
}
