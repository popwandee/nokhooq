<?php // callback.php

require __DIR__."/vendor/autoload.php";
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '6f6b7e3b1aff242cd4fb0fa3113f7af3');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'RvsMabRN/IlT2BtmEoH+KcIbha8F/aPLWWzMKj8lxz/7f9c/Ygu5qvrUGtdlrTwyQwR5tFcgIGGzCkHO/SzIKrdCqUm+sal4t73YOuTPZsQX4bR35g3ZJGTvFilxvO1LVO/I6B1ouhx3UjGWe+OwswdB04t89/1O/w1cDnyilFU=');

$bot = new \LINE\LINEBot(

    new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN),

    ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]

);


$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];


try {
	$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
	error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
	error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
	error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
	error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
}


foreach ($events as $event) {
  // Postback Event
	if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
    $info='Postback message has come';
		$logger->info($info); //บันทึกว่าได้รับข้อความแล้ว
    $bot->replyText($reply_token, $info);//ตอบผู้ใช้ว่าได้รับข้อความแล้ว
		continue;
	}
	// Location Event
	if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
    $info="location -> ".$event->getLatitude().",".$event->getLongitude();
    $logger->info($info);
    $bot->replyText($reply_token, $info);//ตอบผู้ใช้ว่าได้รับ พิกัดอะไรมา
		continue;
	}


  if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
    $reply_token = $event->getReplyToken();
    $text = $event->getText();
    $text = strtolower($text);
    $explodeText=explode(" ",$text);

    switch ($explodeText[0]) {
      case '#':
       $replyText='{ "type": "bubble",
          "body": { "type": "box", "layout": "horizontal",
                "contents": [
                 {
                   "type": "image",
                   "url": "https://www.linefriends.com/content/banner/201804/3b5364c97c2d4a26988f85acdc78514e.jpg",
                   "size": "full",
                   "aspectRatio": "16:9",
                   "aspectMode": "cover"
                 }
                 ]
            }
         }';// end $replyText

      break;

		  default:
		  // $replyText=$replyText.$displayName.$statusMessage;
		  break;
            }//end switch

	    $bot->replyText($reply_token, $replyText);
    }//end if text
}// end foreach event
?>
