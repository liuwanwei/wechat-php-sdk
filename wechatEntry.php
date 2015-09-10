<?php
include "wechat.class.php";

function entry($validate){
	global $options, $weObj;

	//明文或兼容模式可以在接口验证通过后注释此句，
	//但加密模式一定不能注释，否则会验证失败
	if($validate == true){
		$weObj->valid();
	}

	$type = $weObj->getRev()->getRevType();
	$wpID = $weObj->getRevTo();
	$userID = $weObj->getRevFrom();

	switch($type) {
		case Wechat::MSGTYPE_TEXT:
			$params = getParams($weObj);
			$weObj->text("欢迎关注!")->reply();
			break;
		case Wechat::MSGTYPE_EVENT:
			$event = $weObj->getRevEvent();
			handleEvent($event, $weObj);
			break;
		case Wechat::MSGTYPE_IMAGE:
			break;
		default:
			$weObj->text("help info")->reply();
	}
}


function handleRequest(){
}

// 关注和取消关注事件处理入口
function handleEvent($event, $weObj){
	$eventName = $event['event'];
	if($eventName == 'subscribe'){
		showSubscribeMsg($weObj, "subscribe");
	}else if($eventName == 'unsubscribe'){
		handleUnscribeMsg($weObj);
	}else{
		showSubscribeMsg($weObj, "message");
	}
}

// 关注公众号后发送图文信息
function showSubscribeMsg($weObj, $title){
	global $userID, $wpID;

	$zhiquAction = 'http://zhiqu.bl99w.com/index.php?r=dispatch/fromWechat';

	$newsData = array("0" => array(
		'Title' => '欢迎关注',
		'Description'=>"",
		'PicUrl' => 'http://wezhiqu.bl99w.com/images/default_image.jpg',
		'Url' => $zhiquAction.'&userid='.$userID.'&mpid='.$wpID ,
		)
	);
	$weObj->news($newsData)->reply();
}

// 取消关注
function handleUnscribeMsg($weObj){}

// 获取访客微信基本信息
function getParams($weObj) {
	$params = "&openId=";
	$senderOpenId = $weObj->getRevFrom();
	$params = $params . $senderOpenId;

	$user = $weObj->getUserInfo($senderOpenId);

	if ($user != false) {
		$sexArr = array('1' => 1, '2' => 0, '0' => '');

		$headImageUrl = $user["headimgurl"];
		$headPhoto = '';
		if (!empty($headImageUrl)){
			$headPhoto = substr($headImageUrl, 0, (strlen($headImageUrl) - 1)).'132';
		}

		$params = $params 
			.'&name='.urlencode($user["nickname"])
			.'&sex='.$sexArr[$user["sex"]]
			.'&headPhoto='.urlencode($headPhoto);
	}else{
		$params = $params."&user=not_found";
	}

	return $params;
}

