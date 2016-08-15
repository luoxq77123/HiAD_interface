<?php

class PassportHelper {

    static function decrycode($txt, $encrypt_key){
        $txt = base64_decode($txt);
        return self::crycode($txt, $encrypt_key);
    }
    
    static function crycode($txt, $encrypt_key){
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

}