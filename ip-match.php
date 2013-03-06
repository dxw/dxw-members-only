<?php

function ip_match($ip, $range) {
  if (preg_match('_^::ffff:(.*)$_', $ip, $m)) {
    # v4-in-v6
    $ip = $m[1];
  }
  if (preg_match('_:_', $ip)) {
    # IPv6 isn't supported
    return false;
  }
  if ($ip === $range) {
    # bare IP
    return true;
  }
  return false;
}
