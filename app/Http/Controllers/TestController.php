<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use PhpParser\Node\Scalar\String_;

class TestController extends Controller
{
    public function index()
    {
        $re = Redis::get('name');
        return $re;
    }


    /**
     * rsa加密 使用私钥加密
     * @param $data
     * @param $private_key
     * @return bool|string
     * @throws
     */
    public static function encrypt()
    {
        $data = 'haha';

        $private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCAkcc5JQhbxiB/beAAJaxxlY1oWmb1ecJhl62W18jaaf32/0RP
LKh2ealsY8ntLig4uje+9+xpaJ9Npdt3f7sddayb+rmrpFkbdf3xUs0f0k4cU3no
72WJ1CFxHZR5iUCREAFnBpGKCu9RvrKao71osZ/a0XsKKrsvVYAH6OxMHwIDAQAB
AoGABFY/JhJm4Mcs/ybOD3qYUFCLYNB1FgfMu1IvCTvNGdLdAsrv4EGusk/IdG3u
h2xWeZygUidULaueVeTi6GZVxsHkeSXbIBtaDEtbW2Xa2XdyqAXe+zR/Fyqbqno9
qUHqR3rxBPWOrM20Qs54tiBcUcJ9dFQf2ylrCuzMiJg5pikCQQCA55xJXA6XOEJE
CDK9yJzehV0ZKjASZnxaaP6ty7tJl2/K2zBakUDr4b/p1x48Pp0xhw0+rFBrwOpk
YcPGNoq9AkEA/1WKUBaK9TOz3ENeSpv1qJ19Zpdk3sLtvs0+4WU6+H8unXcbhECl
nHU0qR0ezVkwtKVKWUA9VdMZCTtbm9YOCwJAROQDOY2SWqz9dUBwZc8eTyo1LCrI
0DynfuYYHigAqv7dBywHdo+kg6v9leqaxRWtivejU3hh7oSGgCljqL4jfQJBAMRB
NneKbDI+FD/31BDawT5iFtH1CcYi4+QlE/DhAyufbfAAbbkAi4qvl9Nom5VQuZwP
9A6Xzs0Z3YN4CVjFdbMCQE5C18iKmmXO/DYwOuaEgpKgpdX0hGPuK6soRHQvKIn1
9CNpxh6W/1oiPSNH78TZxlDCG/JyYZNBRgO7KT8DXHI=
-----END RSA PRIVATE KEY-----';
        $data = static::serialize($data);
        $encrypt_data = '';
        $prk = openssl_pkey_get_private($private_key);

        if (!$prk) {
            return false;
        }

        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk, $encrypt_str, $prk);
            $encrypt_data .= $encrypt_str;
        }

        //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        //$encrypt_data = str_replace(array('+','/','='),array('-','_',''), base64_encode($encrypt_data));

        return base64_encode($encrypt_data);

    }


    /**
     * rsa解密 使用公钥解密
     * @param $data
     * @param $public_key
     * @return bool|string
     */
    public static function decrypt()
    {

        //$data = str_replace(array('-','_'),array('+','/'),$data);
        $data='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';
        $public_key='-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDf/syX5vkzNrQbgW5rGTH3jVae
5r/USdVobfM8LkdEOIFL9Qn6BM/0YhHHKsnzEdmvZMItmz29zlDudWYvFMMLh1RM
1hEKciJBhjSWCqAwAzYaGtVatomts7rCriBHJIVgOO0g7Wy5YgPn7JduWCVVVh1l
krXcunTLJoeZlfE4kwIDAQAB
-----END PUBLIC KEY-----';
        $data = is_string($data) ? $data : '';
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);


        $decrypt_data = '';
        $puk = openssl_pkey_get_public($public_key);

        if (!$puk) {
            return false;
        }

        foreach (str_split($data, 128) as $chunk) {
            openssl_public_decrypt($chunk, $decrypt_str, $puk);
            $decrypt_data .= $decrypt_str;
        }

        return $decrypt_data;
    }


    /**
     * 数据序列化
     * @param $data
     * @return string
     * @throws \Exception
     */
    protected static function serialize($data)
    {
        if (is_array($data)) {
            ksort($data);
            $signContent = json_encode($data);
        } else if(is_string($data)){
            $signContent = $data;
        } else {
            throw new \Exception('加密通讯序列化失败', 500);
        }

        return $signContent;
    }

    public function publishArticle(Request $request)
    {
//
        ProcessPodcast::dispatch('yest');
//
    }

    public function test_1()
    {
        $vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
        $re = Arr::last($vowels);
        return $re;
    }
}
