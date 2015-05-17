<?php
class Nicolive {
	
	var $URI_MYPAGE = "http://live.nicovideo.jp/my";
	var $URI_LOGIN = "https://secure.nicovideo.jp/secure/login?site=niconico";
	var $URI_GETFMEPROFILE = "http://live.nicovideo.jp/api/getfmeprofile?v=";
	var $URI_GETPLAYERSTATUS = "http://live.nicovideo.jp/api/getplayerstatus?v=";
	
	var $user_session;
	var $id;
	var $password;
	
	

	public function Nicolive($id, $password){
		$this->user_session = file_get_contents("user_session");
		$this->id = $id;
		$this->password = $password;
	}
	
	public function Login(){

		//ユーザーセッション再利用
		$user_session = file_get_contents("user_session");
		$contents = $this->Nicolive_HTTP_GET("http://live.nicovideo.jp/my", $user_session);
		if(strstr($contents, '<title>マイページ')){
				return TRUE;
		}	

		//普通にログイン
		$data = array(
			"mail" => $this->id,
			"password" => $this->password,
			"next_url" => ""
		);
		$data = http_build_query($data, "", "&");

		//header
		$header = array(
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($data)
		);

		$context = array(
			"http" => array(
			"method"  => "POST",
			"header"  => implode("\r\n", $header),
			"content" => $data
			)
		);

		file_get_contents($this->URI_LOGIN, false, stream_context_create($context));

		$user_session = "";
		foreach ($http_response_header as $val) {
			if ( preg_match("/Set-Cookie: (user_session=user_session.*)/u", $val, $match) ) {
				$user_session =  $match[1] . "\n";
			}
		}

		if(strcmp($user_session,"") != 0 ){
			$this->user_session = $user_session;
			file_put_contents("user_session", $this->user_session);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getLiveLV(){
		
		$contents = $this->Nicolive_HTTP_GET($this->URI_MYPAGE);
		
		if(!strstr($contents, '<title>マイページ')){
			$this->Login($this->id, $this->password);
		}	
		
		$contents = $this->Nicolive_HTTP_GET($this->URI_MYPAGE);

		$lv = "";	

		if(preg_match("#http://live.nicovideo.jp/watch/(lv[0-9]+)(.*?)生放送ページへ戻る\"#s", $contents, $match)){
			 $lv =  $match[1];  
		}
		return $lv;
	}

	public function getFMEProfile($lv){
		
		$xml = $this->Nicolive_HTTP_GET($this->URI_GETFMEPROFILE . $lv);
		return $xml;
	}

	public function getPlayerStatus($lv){
		$xml = $this->Nicolive_HTTP_GET($this->URI_GETPLAYERSTATUS . $lv);
		return $xml;
	}

	private function Nicolive_HTTP_GET($url){
		$options = array(
			'http'=>array(
			'method'=>'GET',
			'header'=>"User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)\r\n".
				    "Accept-language: ja\r\n".
					"Cookie: $this->user_session\r\n"
			)
		);

		$context = stream_context_create( $options );
		return file_get_contents( $url , FALSE, $context );
	}
}
