<?php

namespace Dxw\CIDR;

class IPv4Address extends AddressBase
{
    public static function Make(string $address): \Dxw\Result\Result
    {
        if (strpos($address, '.') === false || strpos($address, ':') !== false) {
            return \Dxw\Result\Result::err('not an IPv4 address');
        }

        return parent::Make($address);
    }
}
