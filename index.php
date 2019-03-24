<?php
 header("Access-Control-Allow-Origin: *");
    require_once('./library/twitter-api-php/TwitterAPIExchange.php');
    $settings = array(
        'oauth_access_token' => "60275295-q2fScwaZ0B69REU3araikjwF2KbBVhLGkmVvGSegH",
        'oauth_access_token_secret' => "HRNgyBvTmP09gRnSHvaQYDphEPgTEQMEeICbBeRhnwJIL",
        'consumer_key' => "ApUFZVb7hSvdnvzHo9sJNqbD2",
        'consumer_secret' => "UuF0GGgmfITlxi1a5v1ANkzZtLwFZVJqIBOMUSwi0hdWPCtdBR"
    );
    $twitter = new TwitterAPIExchange($settings);
    // initiate variable
    $screen_name=$_GET['username'];
    $DaysOfCode=new stdClass();
    $userArr=[];
    $arr=[];
    $requestMethod = 'GET';

    // get user data
    $userUrl='https://api.twitter.com/1.1/users/show.json';
    $getUserfield='?screen_name='.$screen_name.'';
    $userData=json_decode($twitter->setGetfield($getUserfield)->buildOauth($userUrl, $requestMethod)->performRequest());

    $obj = new stdClass();
    $obj->name=$userData->name;
    $obj->screen_name=$userData->screen_name;
    $obj->desc=$userData->description;
    $obj->url=$userData->url;
    $obj->avatar=$userData->profile_image_url;
    array_push($userArr,$obj);
    
    $DaysOfCode->user=$userArr;

    // get tweet data
    $since_id=(isset($_GET['since_id']) ? '&since_id='.$_GET['since_id']:'');
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $getfield = '?screen_name='.$screen_name.'&count=200&trim_user=false&exclude_replies=true'.$since_id.'';
    $data=json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest());

  

    foreach($data as $item){
        if(is_array($item->entities->hashtags)){
            $isHastagExsist = in_array('100daysofcode',array_map(function($o) { return strtolower($o->text); }, $item->entities->hashtags)) ? true: false;
            if($isHastagExsist==true){
                $obj = new stdClass();
                $obj->created_at=$item->created_at;
                $obj->id=$item->id;
                $obj->text=$item->text;
                $obj->retweet=$item->retweet_count;
                $obj->favorite=$item->favorite_count;

                array_push($arr,$obj);
            }
        }
    }
    $DaysOfCode->data=$arr;

    echo json_encode($DaysOfCode);


?>