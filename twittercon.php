<?php

function buildBaseString($baseURI, $method, $params)
{
    $r = array();
    ksort($params);
    foreach ($params as $key => $value) {
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

function buildAuthorizationHeader($oauth)
{
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach ($oauth as $key => $value)
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    $r .= implode(', ', $values);
    return $r;
}

function returnTweet()
{
    $oauth_access_token = "****************";
    $oauth_access_token_secret = "********************";
    $consumer_key = "***************";
    $consumer_secret = "**********************";
    $twitter_timeline = "user_timeline";  
    //请求参数：
    $request = array(
        'screen_name' => '*****',//请求对象名
        'count' => '10',//请求数量
        'tweet_mode'=>'extended',//是都显示完整推文
        'trim_user'=>'true'//是否对返回的user对象精简
    );
    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_nonce' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_token' => $oauth_access_token,
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0'
    );
    
    $oauth = array_merge($oauth, $request);
    
    $base_info = buildBaseString("https://api.twitter.com/1.1/statuses/$twitter_timeline.json", 'GET', $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;
    //  make request
    $header = array(buildAuthorizationHeader($oauth), 'Expect:');
    $options = array(CURLOPT_HTTPHEADER => $header,
        CURLOPT_HEADER => false,
        CURLOPT_URL => "https://api.twitter.com/1.1/statuses/$twitter_timeline.json?" . http_build_query($request),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false);
    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);
    return $json;
}

$tweet = returnTweet();
return $tweet;
#echo $tweet;

