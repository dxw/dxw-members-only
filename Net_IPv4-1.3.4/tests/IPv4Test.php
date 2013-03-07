<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Net/IPv4.php';
require_once 'MyIPv4.php';

class IPv4Test extends PHPUnit_Framework_TestCase {
    protected $net;
    protected $quadIPs;
    protected $hexIPs;
    
    protected function setUp() {
        $this->net = new Net_IPv4();
        $this->quadIPs = array('10.0.0.15' => 167772175, '172.16.0.155' => 2886729883,
        '192.168.0.255' => 3232235775, '1.2.3.4' => 16909060, '1.0.0.0' => 16777216,
        '255.255.255.255' => 4294967295, '127.0.0.1' => 2130706433);
        $this->hexIPs = array('0a00000f', 'ac10009b', 'c0a800ff', '01020304', 
        '01000000', 'ffffffff', '7f000001');
    }
    
    protected function tearDown() {
        $this->net = null;
    }
    
    public function test_atoh() {
        foreach ($this->quadIPs as $ip => $v) {
            $this->assertTrue(Net_IPv4::atoh($ip) === MyIPv4::atoh($ip));
        }
    }
    
    public function test_htoa() {
        foreach ($this->hexIPs as $ip => $v) {
            $this->assertTrue(Net_IPv4::htoa($ip) === MyIPv4::htoa($ip));
        }
    }
    
    public function test_validateIP() {
        foreach ($this->quadIPs as $ip => $v) {
            $this->assertTrue(Net_IPv4::validateIP($ip) === MyIPv4::validateIP($ip));
        }
    }
    public function test_ip2double() {
        foreach ($this->quadIPs as $k => $v) {
            $this->assertTrue(Net_IPv4::ip2double($k) == $v);
        }
    }
    
    
}

    
?>