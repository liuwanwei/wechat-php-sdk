<?php

require_once 'entry.class.php';

$options = array(
	//填写你设定的 token
	'token'=>'zhiqu', 
	//填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'encodingaeskey'=>'8ZnKfHQ2kpUbiN6zBLvtT0aCREKrDWxwAwwTcFxXfnK' 
);

// 明文或兼容模式可以在接口验证通过后传入 false ，但加密模式一定不能这样，会验证失败
$handler = new WechatEntry();
$handler->entryPoint($options, true);
