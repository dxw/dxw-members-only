<?php


class NmoHelpersTest extends PHPUnit_Framework_TestCase
{
  
  public function testStripQueryWithQueryString()
  {
    $this->assertEquals('http://foo.bar', NmoHelpers::strip_query('http://foo.bar?baz'));
  }

  public function testStripQueryWithoutQueryString()
  {
    $this->assertEquals('http://bar.baz', NmoHelpers::strip_query('http://bar.baz'));
  }

}
