<?php

final class IRCBot {
  private $socket;
  private array $ex = array();

  public function __construct(array $config) {
    $this->socket = fsockopen($config['server'], $config['port']);
    $this->login($config);
    $this->send('JOIN', $config['channel']);
  }

  private function login(array $config) {
    $this->send(
      'USER', 
      $config['nick'].' '.$config['server'].' '.$config['server'].' :'.$config['name']
    );
    $this->send('NICK', $config['nick']);
  }

  public function run() {
    while (!feof($socket)) {
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
      
      usleep(100);
    }
  }

  function send(string $cmd, string $msg = null) {
    if($msg == null) {
      fputs($this->socket, $cmd."\r\n");
    } else {
      fputs($this->socket, $cmd.' '.$msg."\r\n");
    }
  }
}
