<?php
/**
 * Created by PhpStorm.
 * User: nucc1
 * Date: 24/05/2019
 * Time: 20:28
 */

namespace nucc1;


class Hostnames
{

    /** read dhcp lease info from dnsmasq default location
     *
     * @return array of dhcp hostnames keyed by IP address (IP => hostname)
     *
     * keep in mind that the hostname is not guaranteed to exist in dhcp. it is an * if no hostname.
     */
    public static function read_leases(): array
    {
        $fn = '/var/lib/misc/dnsmasq.leases';
        $fh = fopen($fn, 'r');
        $hostnames = [];
        if ($fh) {
            $contents = fread($fh, filesize($fn));
            $lines = explode("\n", $contents);
            $contents = null;
            fclose($fh);

            foreach ($lines as $l) {

                if (strlen(trim($l))== 0) {
                    continue; //skip empty lines.
                }
                $a = strpos($l, " ");
                if ($a >= 0) {
                    $b = strpos($l, " ", $a+1);
                    if ($b >=0 ) {
                        //now we're at ip
                        $c = strpos($l, " ", $b+1); //end of IP

                        $ip = trim(substr($l, $b, $c - $b));


                        $d = strpos($l, " ", $c+1);

                        $hn = substr($l, $c, $d - $c);

                        $hostnames[$ip] = trim($hn);
                    }else {
                        break; //if space not found then not valid content
                    }
                }else {
                    break; //not valid content
                }
            }
        }



        return $hostnames;
    }
}
