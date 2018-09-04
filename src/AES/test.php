<?php
	/**
	 * Created by PhpStorm.
	 * User: mybook-lhp
	 * Date: 18/7/5
	 * Time: 下午12:00
	 */

	//PHP的加密解密方法：

	$privateKey = "@12345678912345!";
	$iv = "@12345678912345!";
	//加密
	$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
	echo base64_encode($encrypted);


	//解密
	$encryptedData = base64_decode($data);
	$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
	$decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作

	return $decrypted;