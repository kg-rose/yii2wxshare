<?php
namespace source\helpers;

class JSSDK{
	private $appId;
	private $appSecret;
	private $url = 'data/attachment/';//缓存文件的保存路径

	public function __construct($appId, $appSecret) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}

	/**
	 * @return array
	 */
	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => $this->appId,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage;
	}

	/**
	 * @param int $length
	 * @return string
	 */
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * @return mixed
	 */
	private function getJsApiTicket() {
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this->get_php_file("jsapi_ticket.php"));
		if ($data==false  || empty($data) || $data->expire_time < time()) {
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$arr['expire_time'] = time() + 7000;
				$arr['jsapi_ticket'] = $ticket;
				$this->set_php_file($this->url.".jsapi_ticket.php", json_encode($data));
			}
		} else {
			$ticket = $data->jsapi_ticket;
		}
		return $ticket;
	}

	/**
	 * @return mixed
	 */
	private function getAccessToken() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this->get_php_file($this->url.".access_token.php"));
		if (empty($data) || $data->expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));

			$access_token = $res->access_token;
			if ($access_token) {
				$arr['expire_time'] = time() + 7000;
				$arr['access_token'] = $access_token;
				$this->set_php_file($this->url.".access_token.php", json_encode($data));
			}
		} else {
			$access_token = $data->access_token;
		}
		return $access_token;
	}

	/**
	 * @param $url
	 * @return string
	 */
	private function httpGet($url) {
		$res = file_get_contents($url);
		return $res;
	}

	/**
	 * @param $filename
	 * @return bool|string
	 */
	private function get_php_file($filename) {
		if(file_exists($this->url.$filename)){
			return trim(substr(file_get_contents($this->url.$filename), 15));
		}else{
			return false;
		}
	}

	/**
	 * @param $filename
	 * @param $content
	 */
	private function set_php_file($filename, $content) {
		$fp = fopen($filename, "w");
		fwrite($fp, "<?php exit();?>" . $content);
		fclose($fp);
	}


	/**
	 * @return mixed
	 * 对外的接口
	 */
	public function getOutaccessToken() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this->get_php_file($this->url.".access_token.php"));
		if (empty($data) || $data->expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));

			$access_token = $res->access_token;
			if ($access_token) {
				$arr['expire_time'] = time() + 7000;
				$arr['access_token'] = $access_token;
				$this->set_php_file($this->url."access_token.php", json_encode($data));
			}
		} else {
			$access_token = $data->access_token;
		}
		return $access_token;
	}

}