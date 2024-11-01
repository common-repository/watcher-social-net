<?php
/**
*
* PHP version 5.3.10
*
* @category Functions
* @package  Plugin Watcher Social Net
* @author   Comworks Technologies
* @license  MIT License
* @version  1.0.0
*/
include ('TwitterAPIExchange.php');

class functions
{
	private $twitter_access_token;
	private $twitter_access_token_secret;
	private $twitter_consumer_key;
	private $twitter_consumer_secret;
	private $instagram_access_token;
	private $hashtag;
	private $namemonth = array("Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic.");
	
	public function __construct($attributes){
		$this->twitter_access_token = $attributes['twitter_access_token'];
		$this->twitter_access_token_secret = $attributes['twitter_access_token_secret'];
		$this->twitter_consumer_key = $attributes['twitter_consumer_key'];
		$this->twitter_consumer_secret = $attributes['twitter_consumer_secret'];
		$this->instagram_access_token = $attributes['instagram_access_token'];
		$this->hashtag = $attributes['hashtag'];
	}
	
	public function getFeeds(){
			
		$feeds = array();
		
		$feeds_instagram = $this->getFeedsInstagram();
		
		$feeds_twitter = $this->getFeedsTwitter();
		
		$feeds = array_merge($feeds_instagram,$feeds_twitter);
		
		$feeds = $this->orderArray($feeds);

		return $feeds;
		
	}
	
	private function getFeedsTwitter(){
		
		$feeds = array();
		
		if (isset($this->hashtag) && 
				isset($this->twitter_access_token) && 
				isset($this->twitter_access_token_secret) && 
				isset($this->twitter_consumer_key) && 
				isset($this->twitter_consumer_secret)){
		
			$settings = array(
        			'oauth_access_token' => $this->twitter_access_token,
        			'oauth_access_token_secret' => $this->twitter_access_token_secret,
        			'consumer_key' => $this->twitter_consumer_key,
        			'consumer_secret' => $this->twitter_consumer_secret
        	);
        	
        	$url = 'https://api.twitter.com/1.1/search/tweets.json';
        	$getfield = '?q=%23'.$this->hashtag.'&count=50';
        	$requestMethod = 'GET';
        	
        	
        	$twitter = new TwitterAPIExchange($settings);
        	$res = $twitter->setGetfield($getfield)
        			->buildOauth($url, $requestMethod)
        			->performRequest();
        	
			if(isset($res)) {
				$json = json_decode($res);
		
				foreach($json->statuses as $obj) {
		
					$feed = array();
					
					$day = date("d", strtotime($obj->created_at));
        			$month = date("n", strtotime($obj->created_at));
        			$year = date("Y", strtotime($obj->created_at));
        			$dateprint =  $day . " " .  $this->namemonth[$month - 1] . " " . $year;
		
					$feed['type'] = "twitter";
					$feed['id'] = $obj->id;
					$feed['created_time'] = date("YndHis",strtotime($obj->created_at));
					$feed['text'] = $obj->text;
					$feed['url'] = "http://twitter.com/" . $obj->user->screen_name . "/status/" . $obj->id_str;
					$feed['user'] = $obj->user->screen_name;
					$feed['user_picture'] = $obj->user->profile_image_url;
					$feed['img'] = null;
					
					$feed['date_show'] = $dateprint;
					
					array_push($feeds, $feed);
					unset($feed);
		
				}
		
			}
			unset($res);
		}
		
		return $feeds;
		
	}
	
	private function getFeedsInstagram(){
		
		$feeds = array();
		
		if (isset($this->hashtag) && isset($this->instagram_access_token)){
		
			$url = "https://api.instagram.com/v1/tags/".str_replace("#","",$this->hashtag)."/media/recent?access_token=".$this->instagram_access_token;
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$res = curl_exec($ch);
			curl_close($ch);
			
			if(isset($res)) {
				$json = json_decode($res);
		
				foreach($json->data as $obj) {
						
					$feed = array();
					$day = date("d", $obj->created_time);
					$month = date("n", $obj->created_time);
					$year = date("Y", $obj->created_time);
					$dateprint =  $day . " " .  $this->namemonth[$month - 1] . " " . $year;
						
					$feed['type'] = "instagram";
					$feed['id'] = $obj->id;
					$feed['created_time'] = date("YndHis",$obj->created_time);
					$feed['text'] = $obj->caption->text;
					$feed['url'] = $obj->link;
					$feed['user'] = $obj->user->username;
					$feed['user_picture'] = $obj->user->profile_picture;
					$feed['img'] = $obj->images->standard_resolution->url;
					$feed['date_show'] = $dateprint;
						
					array_push($feeds, $feed);
					unset($feed);
						
				}
		
			}
			
			unset($res);
		}
		
		return $feeds;
	}
	
	private function orderArray($data){
		
		function sortDateFunction( $a, $b ) {
			return $b["created_time"] - $a["created_time"];
		}
		usort($data, "sortDateFunction");
		
		return $data;
	}

	
}