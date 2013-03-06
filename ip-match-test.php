<?php

require "ip-match.php";

function a($ip, $range, $return) {
  echo "Comparing $ip to $range - should return ".($return ? 'true' : 'false')."\n";
  assert(ip_match($ip, $range) === $return);
}

# v4 plain addresses
a('192.168.1.1', '192.168.1.1', true);
a('::ffff:192.168.1.1', '192.168.1.1', true);
a('192.168.1.2', '192.168.1.1', false);
a('::ffff:192.168.1.2', '192.168.1.1', false);

# CIDRv4
a('192.168.1.2', '192.168.1.1/24', true);
a('::ffff:192.168.1.78', '192.168.1.1/24', true);
a('192.168.0.2', '192.168.1.1/24', false);
a('::ffff:192.168.0.78', '192.168.1.1/24', false);

# IPv6 should just fail
a('fe80::4261:86ff:fecc:2704/64', 'fe80::4261:86ff:fecc:2704/64', false);
a('fe80::4261:86ff:fecc:2704/64', '::1/128', false);
