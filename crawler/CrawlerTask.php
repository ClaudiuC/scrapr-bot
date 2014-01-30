<?php

require_once 'Crawler.php';

// Definitely should use PDO
final class CrawlerTask {
  private Crawler $crawler;
  private array $data;
  private CrawlerPath $path = null;
  
  const string DEFAULT_XPATH = '/rss/channel/item';

  public function __construct(string $uri) {
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

    // Crawler settings
    $this->crawler = new Crawler($uri);
    $this->path = CrawlerPath::get(self::DEFAULT_XPATH);
  }

  public function setXpath(string $path) {
    $this->path = CrawlerPath::get($path);

    return $this;
  } 
  
  public function fetch() {
    $this->data = $this->crawler->getPath($this->path);
    return $this;
  }
  
  public function extractNth(int $nth) {
    if (!$this->data) {
      throw new CrawlerTaskException('No data to extract from');
    }

    $this->data = $this->data[$nth];
    return $this;
  }
  
  // This is really built to handle posts only and it's dumb 
  public function store() {
    if (!$this->data) {
      throw new CrawlerTaskException('No data to store');      
    }
    
    $key = null;
    $name = null;
    
    foreach ($this->data as $post) {
      echo $post['title'] . '\n';
      var_dump($post);
      // Get first post that's not a reply
      if (strpos($post['title'], 'Re: ') === false || strpos($post['title'], 'Re: ') !== 0) {
        $key = $post['guid'];
        $name = $post['title'];
        break;
      }
    }
      
    // Replies, replies everywhere
    if (!$key && !$name) {
      return;
    }
    
    echo 'key', $key, 'name', $name;

    // Check duplicate
    $query = sprintf(
      'SELECT * FROM `task_log` WHERE `key` = \'%s\'',
      mysql_real_escape_string($key)
    );

    $result = mysql_query($query);
    $this->validateQueryResult($result, $query);

    if (mysql_num_rows($result)) {
      return; // nothing to do here
    }

    $query = sprintf(
      'INSERT INTO `task_log` (`key`, `name`, `created`) VALUES (\'%s\', \'%s\', NOW())',
      mysql_real_escape_string($key),
      mysql_real_escape_string($name)
    );
    
    echo $query;

    $result = mysql_query($query);
    $this->validateQueryResult($result, $query);
  }  
  
  // Should type that result
  private function validateQueryResult($result, string $query) {
    if (!$result) {
      throw new CrawlerTaskException(
        sprintf(
          'Failed to run query \'%s\': \'%s\'',
          $query,
          mysql_errno()
        )
      );
    }
  }
}
final class CrawlerTaskException extends Exception {}
