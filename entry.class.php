<?php
require_once 'wechat.class.php';

class WechatEntry{

	private $type;
	private $mpId;
	private $userId;

	public function entryPoint($options, $validate){
		$weObj = new Wechat($options);

		if($validate == true){
			$weObj->valid();
		}

		$this->type = $weObj->getRev()->getRevType();
		$this->mpId= $weObj->getRevTo();
		$this->userId = $weObj->getRevFrom();

		switch($this->type) {
			case Wechat::MSGTYPE_TEXT:
				$params = $this->getParams($weObj);
				$weObj->text("欢迎关注!")->reply();
				break;
			case Wechat::MSGTYPE_EVENT:
				$event = $weObj->getRevEvent();
				$this->handleEvent($event, $weObj);
				break;
			case Wechat::MSGTYPE_IMAGE:
				break;
			default:
				$weObj->text("help info")->reply();
		}
	}


	// 关注和取消关注事件处理入口
	function handleEvent($event, $weObj){
		$eventName = $event['event'];
		if($eventName == 'subscribe'){
			$this->showSubscribeMsg($weObj, "subscribe");
		}else if($eventName == 'unsubscribe'){
			$this->handleUnscribeMsg($weObj);
		}else{
			$this->showSubscribeMsg($weObj, "message");
		}
	}

	// 关注公众号后发送图文信息
	function showSubscribeMsg($weObj, $title){
		$zhiquAction = 'http://zhiqu.bl99w.com/index.php?r=dispatch/fromWechat';

		$newsData = array("0" => array(
			'Title' => '欢迎关注',
			'Description'=>"",
			'PicUrl' => 'http://wezhiqu.bl99w.com/images/default_image.jpg',
			'Url' => $zhiquAction.'&userId='.$this->userId.'&mpId='.$this->mpId ,
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

}
