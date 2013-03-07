<?php
class MyIPv4 {
    public function validateIP($ip) {
        $quad = split('\.', $ip);
        if (count($quad) != 4) {
            return false;
        }
        foreach($quad as $q) {
            if (!is_numeric($q)) {
                return false;
            } else if (intval($q) < 0 || intval($q) > 255) {
                return false;
            }
        }
        return true;
    }
    
    public function atoh($ip) {
        $quad = split('\.', $ip);
        $new = array();
        foreach($quad as $k => $v) {
            $n = str_pad(dechex($v), 2, "0", STR_PAD_LEFT);
            $new[$k] = $n;
            #var_dump($n);
        }
        return implode($new);
    }
    
    public function htoa($ip) {
        $quad = str_split($ip, 2);
        if (count($quad) != 4) {
            return false;
        }
        $new = array();
        foreach($quad as $k => $v) {
            $new[$k] = hexdec($v);
            #var_dump($n);
        }
        return $new[0].'.'.$new[1].'.'.$new[2].'.'.$new[3];
    }
}
?>