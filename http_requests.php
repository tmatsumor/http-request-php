<?php
class HttpRequests
{
    public static function get($url, $param, $header=null){
        $ch=curl_init($url.'?'.http_build_query($param).'&'.uniqid());
        return self::no_cache($ch, $header);
    }

    public static function post($url, $param, $header=null){
        $ch=curl_init($url.'?'.uniqid());
        curl_setopt($ch,CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        return self::no_cache($ch, $header);
    }

    private static function no_cache($ch, $header=null){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            ['Cache-Control: private, no-store, no-cache, must-revalidate', 'Pragma: no-cache'],
            (is_null($header))? [] : $header));
        $body  = curl_exec($ch);
        $info  = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if (CURLE_OK !== $errno) {
            throw new RuntimeException($error, $errno);
        }
        return [$body, $info];
    }
}
?>
