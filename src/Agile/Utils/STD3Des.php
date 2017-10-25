<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/25
 * Time: 15:33
 */

namespace Agile\Utils;


class STD3Des {
    private $key  = "";
    private $iv   = "";
    private $mode = MCRYPT_MODE_CBC;
    /**
     * 构造，传递二个已经进行base64_encode的KEY与IV
     *
     * @param string $key
     * @param string $iv
     */
    function __construct($key, $iv = null) {
        if (empty($key)) {
            echo 'key is not valid';
            exit();
        }
        if ($iv == null) {
            $iv         = $key;
            $this->mode = MCRYPT_MODE_ECB;
        }
        $this->key = $key;
        $this->iv  = $iv;
    }
    /**
     * 加密
     * @param <type> $value
     * @return <type>
     */
    public function encrypt($value) {
        $td    = mcrypt_module_open(MCRYPT_3DES, '', $this->mode, '');
        $iv    = $this->mode == MCRYPT_MODE_CBC ? base64_decode($this->iv) : mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $value = $this->PaddingPKCS7($value);
        $key   = base64_decode($this->key);
        mcrypt_generic_init($td, $key, $iv);
        $dec   = mcrypt_generic($td, $value);
        $ret   = base64_encode($dec);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }
    /**
     * 解密
     * @param <type> $value
     * @return <type>
     */
    public function decrypt($value) {
        $td  = mcrypt_module_open(MCRYPT_3DES, '', $this->mode, '');
        $iv  = $this->mode == MCRYPT_MODE_CBC ? base64_decode($this->iv) : mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $key = base64_decode($this->key);
        mcrypt_generic_init($td, $key, $iv);
        $ret = trim(mdecrypt_generic($td, base64_decode($value)));
        $ret = $this->UnPaddingPKCS7($ret);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }
    private function PaddingPKCS7($data) {
        $block_size   = mcrypt_get_block_size('tripledes', $this->mode);
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char), $padding_char);
        return $data;
    }
    private function UnPaddingPKCS7($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}