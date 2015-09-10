<?php
require_once("wechatEntry.php");

$options = array(
	//填写你设定的 token
	'token'=>'zhiqu', 
	//填写加密用的EncodingAESKey，如接口为明文模式可忽略
        'encodingaeskey'=>'8ZnKfHQ2kpUbiN6zBLvtT0aCREKrDWxwAwwTcFxXfnK' 
);

$weObj = new Wechat($options);

entry(true);
