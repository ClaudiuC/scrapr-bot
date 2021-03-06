<?php

final class IRCBot {
  private $socket;
  private array $ex = array();
  private string $lastKey = null;
  private string $channel = null;
  private bool $joined = false;

  private array $ops = array(
    'Zapa_',
    'mihai_',
  );

  public function __construct(array $config) {
    // Init MySQL connection
    $url = parse_url(getenv('CLEARDB_DATABASE_URL'));

    $server = $url['host'];
    $username = $url['user'];
    $password = $url['pass'];
    $db = substr($url['path'], 1);

    $ok = mysql_connect($server, $username, $password);

    if (!$ok) {
      throw new CrawlerTaskException(
        sprintf(
          'Failed to connect to DB with credentials %s',
          getenv('CLEARDB_DATABASE_URL')
        )
      );
    }

    mysql_select_db($db);

    $this->socket = fsockopen($config['server'], $config['port']);
    $this->login($config);
    $this->send('JOIN', $config['channel']);
    $this->channel = $config['channel'];
  }

  private function login(array $config) {
    $this->send(
      'USER', 
      $config['nick'].' '.$config['server'].' '.$config['server'].' :'.$config['name']
    );
    $this->send('NICK', $config['nick']);
  }

  public function run() {
    while (!feof($this->socket)) {
      $data = fgets($this->socket, 128);
      echo $data;
      flush();

      $this->ex = explode(' ', $data);
      if($this->ex[0] == 'PING') {
        $this->send(
          'PONG', 
          $this->ex[1]
        ); 
      }

      if (strpos($data, 'JOIN ##skullbox') && !$this->joined) {
        $this->joined = true;
        //$this->setOP();
      }
      
      if ($this->joined) {
        $this->checkNewPost();
      }

      usleep(100);
    }
  }

  private function send(string $cmd, string $msg = null) {
    if($msg == null) {
      fputs($this->socket, $cmd."\r\n");
    } else {
      fputs($this->socket, $cmd.' '.$msg."\r\n");
    }
  }

  private function checkNewPost() {
    $query = 'SELECT * FROM `task_log` ORDER BY `created` DESC LIMIT 1';
    $result = mysql_query($query);
    
    $key = null;
    $name = null; 
    while ($row = mysql_fetch_assoc($result)) {
      $key = $row['key'];
      $name = $row['name'];
    }  
    
    if ($key !== $this->lastKey) {
      $msg = 'PRIVMSG '.$this->channel.' : '.$name.' '.$key;
      $this->send($msg);
      $this->lastKey = $key;
    }
  }

  private function setOP() {
    foreach ($this->ops as $op) {
      $msg = 'MSG ChanServ OP '.$this->channel.' '.$op;
      $this->send($msg);
      $msg = 'PRIVMSG '.$this->channel.' : '.$op.', you owe me a beer';
      $this->send($msg);
    }
  }
}
