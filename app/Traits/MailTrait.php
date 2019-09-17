<?php
namespace App\Traits;

trait MailTrait
{
    public function maskEmail(String $email, Int $show)
    {
        list($address, $server) = explode('@', $email);
        $len = strlen($address);
        $address = substr($address, 0, $show);
        $address = str_pad($address, $len, '*');
        return implode('@', [$address, $server]);
    }
}
