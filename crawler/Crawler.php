<?php

final class Crawler {
  private RSSFeed $feed;

  public function __construct(string $url) {
    $result = null;

    if (extension_loaded('curl')) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HEADER, FALSE);
      curl_setopt($curl, CURLOPT_TIMEOUT, 20);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); 
      $result = curl_exec($curl);
      
      if (curl_errno($curl) || curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
        throw new CrawlerException(
          sprintf(
            'Failed to fetch %s with HTTP code %d',
            $url,
            curl_getinfo($curl, CURLINFO_HTTP_CODE)
          )
        );
      }
    } else {
      throw new CrawlerException('Crawler requires CURL. CURL is missing. What gives?');
    } 
    
    $this->result = new RSSFeed($result);
  }

  public function getPath(CrawlerPath $path) {
    return $this->result->getSimpleXML()->xpath($path);              
  }
}
final class CrawlerException extends Exception {}

final class RSSFeed {
  private SimpleXMLElement $XML;
  private array $XMLArray; // Ugh
  
  public function __construct(string $feed_string) {
    // Easier than fixing the feed :(
    $this->XML = new SimpleXMLElement($feed_string, LIBXML_NOWARNING | LIBXML_NOERROR);
    if (!$this->XML) {
      throw new RSSFeedException('Feed is broken beyond repair');
    }
    $this->XMLArray = $this->toArray($this->XML);
  }

  private function toArray(SimpleXMLElement $xml) {
    if (!$xml->children()) {
      return (string) $xml;
    }

    $ret = array();
    foreach ($xml->children() as $tag => $child) {
      if (count($xml->{$tag}) === 1) {
        $ret[$tag] = $this->toArray($child);                
      } else {
        $ret[$tag][] = $this->toArray($child);
      }
    }

    return $ret;
  }

  public function getSimpleXML() {
    return $this->XML;
  }

  public function getArray() {
    return $this->XMLArray;
  }
}
final class RSSFeedException extends Exception {}

final class CrawlerPath {
  // PHP doesn't have a method to validate xpath syntax. Such joy.
  public static function get(string $xpath) {
    $xml = new SimpleXMLElement('<xml></xml>');
    if ($xml->xpath($xpath) === false) {
      throw new CrawlerPathException(
        sprintf('%s is not valid xpath syntax', $xpath)
      );
    } 

    return $xpath;
  }
}
final class CrawlerPathException extends Exception {}

