<?php

// START
 
ob_start();

date_default_timezone_set('Asia/Tehran');

// DEFINE THE NEEDS

define('API_KEY','');

$getMe				=	bot('getMe',[]);

define('BOT_FULLNAME',$getMe->result->first_name);
define('BOT_USERNAME',$getMe->result->username);

define('DEVELOPER_ID',''); // leave string

// CREATE PUBLIC FUNCTION

function bot($method,$datas=[])
{
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    };
}

function ping()
{
    $starttime = microtime(true);
    $file      = fsockopen ($_SERVER['HTTP_HOST'], 80, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file) $status = -1;  // Site is down
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}

function random_chars($length = 8)
{
	$chars	=	'abcdefghijklmnopqrstuvwxyz';
	return substr(str_shuffle($chars),0,$length);
}

function lowercase($string)
{
	return strtolower($string);
}

function uppercase($string)
{
	return strtoupper($string);
}

// TO REACH DATA FROM TELEGRAM

$update				=	json_decode(file_get_contents('php://input'));

if (isset($update->message))			$message	=	$update->message;
if (isset($update->callback_query))		$query		=	$update->callback_query;
if (isset($update->inline_query))		$inline		=	$update->inline_query;

if (isset($message))
{
	$from			=	$message->from;
	
	$who			=	$message->from->id;
	$fname			=	$message->from->first_name;
	$lname			=	$message->from->last_name;
	$uname			=	$message->from->username;
	
	$where			=	$message->chat->id;
	
	$text			=	$message->text;
	$mid			=	$message->message_id;
}
elseif(isset($query))
{
	$from			=	$query->from;
	
	$who			=	$query->from->id;
	$fname			=	$query->from->first_name;
	$lname			=	$query->from->last_name;
	$uname			=	$query->from->username;
	
	if(isset($query->chat_instance))
	{
	    $where          =   $query->chat_instance;
	    $mid            =   $query->inline_message_id;
	}
	else
	{
	    $where			=	$query->message->chat->id;
	    $mid			=	$query->message->message_id;
	    $text			=	$query->message->text;
	}
	
	$data			=	$query->data;
}
elseif(isset($inline))
{
	$iid			=	$inline->id;

	$from			=	$inline->from;
	$who			=	$inline->from->id;
	$fname			=	$inline->from->first_name;
	$lname			=	$inline->from->last_name;
	$uname			=	$inline->from->username;
    
    $where          =   $inline->inline_message_id;
	$query			=	$inline->query;
	
	$offset         =   $inline->offset;
}
// LOAD PLUGINS USING include()

if (isset($update))
{
	// REGISTER PLUGINS
	
	$plugins		=	[
		'jdf',
		'response',
	];
	
	foreach ($plugins as $plugin)
	{
		include("Plugins/".$plugin.".php");
	}
}
else
{
	if (isset($_GET['set']))
    {
        $ssl    =   ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ;
	    $host   =   $_SERVER['HTTP_HOST'];
	    $path   =   $_SERVER['PHP_SELF'];
	
	    $url    =   $ssl.$host.$path;
    
        header('content-type: application/json');
        
        echo file_get_contents("https://api.telegram.org/bot".API_KEY."/setWebhook?url=".$url);
    }
    else
    {
        header('content-type: application/json');
        echo json_encode($getMe);
    }
}