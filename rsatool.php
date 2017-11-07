<?php
/*
* Rsa 加解密工具
* Author:Radiation
* Blog:http://b.zlweb.cc
* Time:2017-11-6
*/


class RSATool{

	/*
	* 构造函数
	* 用来检测是否开启了openssl扩展
	*/
	public function __construct(){
		if(!function_exists("openssl_public_encrypt")){
			echo "Require Function Not Exists!please open php_openssl.dll extension in php.ini";
			exit();
		}
	}


	/*
	* @param data  要解密的数据，可以是一个文件名，也可以是数据字符串
	* @param key   密钥文件路径
	* @param model 解密模式，1为私钥加密，公钥解密，其他数则是标准的公钥加密，私钥解密
	* @param isfile 要解密的数据是否为文件，如果你data设置的是普通的数据字符串，则这里设置为false
	* @param base64  密文是否经过base64加密，加密则为true，否则为false
	* @return 解密后的数据
	*/
	public function RsaDeCrypt($data,$key,$model=1,$isfile=true,$base64=false){
		$key = file_get_contents($key);
		$decrypted="";
		if($isfile){
			$f = fopen($data, "rb");
			$con = fread($f, filesize($data));
			fclose($f);
		}else{
			$con = $data;
		}
		if($model==1){
			$key = openssl_pkey_get_public($key);
			if($base64){
				openssl_public_decrypt(base64_decode($con), $decrypted, $key);
			}else{
				openssl_public_decrypt($con, $decrypted, $key);
			}
		}else{
			$key = openssl_pkey_get_private($key);
			if($base64){
				openssl_private_decrypt(base64_decode($con), $decrypted, $key);
			}else{
				openssl_private_decrypt($con, $decrypted, $key);
			}
		}
		return $decrypted;
	}


	/*
	* @param data  要加密的字符串
	* @param key   密钥文件路径
	* @param model 加密模式，1为私钥加密，公钥解密，其他数则是标准的公钥加密，私钥解密
	* @param toBase64  是否将加密后的结果转换为base64，是则设置成true，如果为false则会在当前目录写入一个名为Rsa_Encrypt的文件
	* @return 如果设置toBase64为true，则返回base64加密后的加密字符串，否则返回文件写入结果
	*/
	public function RsaEnCrypt($data,$key,$model=1,$toBase64=true){
		$key = file_get_contents($key);
		$crypted="";
		if($model==1){
			$key = openssl_pkey_get_private($key);
			openssl_private_encrypt($data, $crypted, $key);
		}else{
			$key = openssl_pkey_get_public($key);
			openssl_public_encrypt($data, $crypted, $key);
		}

		if($toBase64)
			return base64_encode($crypted);
		else{
			$f = fopen("Rsa_Encrypt", "wb");
			fwrite($f, $crypted) or die("write faile");
			fclose($f);
			return "write ok!";
		}
	}

}