<?php

class PassportComponent {

    // 加密
    function passport_encrypt($txt, $key) {
        srand((double) microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
        }
        return $this->base_encode($this->passport_key($tmp, $key));
    }

    // 解密
    function passport_decrypt($txt, $key) {
        $txt = $this->passport_key($this->base_decode($txt), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }

    function passport_key($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

    function base_encode($str) {
        $src = array("/", "+", "=");
        $dist = array("-a", "-b", "-c");
        $old = base64_encode($str);
        $new = str_replace($src, $dist, $old);
        return $new;
    }

    function base_decode($str) {
        $src = array("-a", "-b", "-c");
        $dist = array("/", "+", "=");
        $old = str_replace($src, $dist, $str);
        $new = base64_decode($old);
        return $new;
    }

}