<?php
$proxies = file ("proxy.txt");
if (empty($proxies))
{
    exit(1);
}

$mc = curl_multi_init ();
$c = array();
for ($thread_no = 0; $thread_no<count ($proxies); $thread_no++)
{
    $c [$thread_no] = curl_init ();
    curl_setopt ($c [$thread_no], CURLOPT_URL, "https://2ip.ru/");
    curl_setopt ($c [$thread_no], CURLOPT_HEADER, 0);
    curl_setopt ($c [$thread_no], CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($c [$thread_no], CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt ($c [$thread_no], CURLOPT_TIMEOUT, 10);
    curl_setopt ($c [$thread_no], CURLOPT_PROXY, trim ($proxies [$thread_no]));
    curl_setopt ($c [$thread_no], CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
    curl_multi_add_handle ($mc, $c [$thread_no]);
}
 
$proxies_out = array();
do {
    while (($execrun = curl_multi_exec ($mc, $running)) == CURLM_CALL_MULTI_PERFORM);
    if ($execrun != CURLM_OK) break;
    while ($done = curl_multi_info_read ($mc))
    {
        $info = curl_getinfo ($done ['handle']);
        if ($info ['http_code'] >= 200) {
            echo trim ($proxies [array_search ($done['handle'], $c)])."\r\n";
            $proxies_out[trim($proxies[array_search($done['handle'], $c)])] = trim($proxies[array_search($done['handle'], $c)]);
            unset($proxies[array_search ($done['handle'], $c)]);
        }
        curl_multi_remove_handle ($mc, $done ['handle']);
    }
} while ($running);
curl_multi_close ($mc);

$mc = curl_multi_init ();
$c = array();
for ($thread_no = 0; $thread_no<count ($proxies); $thread_no++)
{
    $c [$thread_no] = curl_init ();
    curl_setopt ($c [$thread_no], CURLOPT_URL, "https://google.com");
    curl_setopt ($c [$thread_no], CURLOPT_HEADER, 0);
    curl_setopt ($c [$thread_no], CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($c [$thread_no], CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt ($c [$thread_no], CURLOPT_TIMEOUT, 10);
    curl_setopt ($c [$thread_no], CURLOPT_PROXY, trim ($proxies [$thread_no]));
    curl_setopt ($c [$thread_no], CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_multi_add_handle ($mc, $c [$thread_no]);
}
 
$proxies5_out = array();
do {
    while (($execrun = curl_multi_exec ($mc, $running)) == CURLM_CALL_MULTI_PERFORM);
    if ($execrun != CURLM_OK) break;
    while ($done = curl_multi_info_read ($mc))
    {
        $info = curl_getinfo ($done ['handle']);
        if ($info ['http_code'] >= 200) {
            echo trim ($proxies [array_search ($done['handle'], $c)])."\r\n";
            $proxies5_out[trim($proxies[array_search($done['handle'], $c)])] = trim($proxies[array_search($done['handle'], $c)]);
        }
        curl_multi_remove_handle ($mc, $done ['handle']);
    }
} while ($running);
curl_multi_close ($mc);

file_put_contents("valid-proxy.txt", implode("\r\n", $proxies_out)."\r\n\r\n".implode("\r\n", $proxies5_out));
?>
