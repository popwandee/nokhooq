<?php // callback.php
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__."/vendor/autoload.php";
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use \Statickidz\GoogleTranslate;
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
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\Flex;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder
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
		$logger->info('Postback message has come');
		continue;
	}
	// Location Event
    if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
		continue;
	}
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
        $replyToken = $event->getReplyToken();
	    $replyData='No Data';
        $text = $event->getText();
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	    
	    // ส่วนตรวจสอบผู้ใช้
		$userId=$event->getUserId();
		$response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array 
                   $userData = $response->getJSONDecodedBody(); // return array     
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']                            // $userData['statusMessage']
                   $userDisplayName = $userData['displayName']; 
		   //$bot->replyText($replyToken, $userDisplayName); ใช้ตรวจสอบว่าผู้ถาม ชื่อ อะไร	
		}else{
		 //$bot->replyText($replyToken, $userId);  ใช้ตรวจสอบว่าผู้ถาม ID อะไร	
			$userDisplayName = $userId;
		}
		// จบส่วนการตรวจสอบผู้ใช้
		      
      switch ($explodeText[0]) {
		
	case '#i':
		
		/* ส่วนดึงข้อมูลจากฐานข้อมูล */
		if (!is_null($explodeText[1])){
		   $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
                   $data = json_decode($json);
                   $isData=sizeof($data);
		      
                 if($isData >0){
		    $textReplyMessage="";
		    $count=1;
	            $multiMessage =     new MultiMessageBuilder;
		    $textMessage = 'ตอบคุณ @'.$userDisplayName; 
		    $multiMessage->add($textMessage);
                    foreach($data as $rec){
                           $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
                           $count++;
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			    
			   $picFullSize = 'https://www.matichon.co.th/wp-content/uploads/2017/05/matichon-logo.png';
                           $picThumbnail = 'https://www.matichon.co.th/wp-content/uploads/2017/05/matichon-logo.png';
                           $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			   $multiMessage->add($imageMessage);
                           }//end for each
	            $replyData = $multiMessage;
			 
		   }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
		      $textMessage= "ตอบคุณ ".$userDisplayName."ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย"; 
		      $bot->replyText($replyToken, $textMessage);
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	              $textMessage= "ตอบคุณ ".$userDisplayName."คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ"; 
		      $bot->replyText($replyToken, $textMessage);
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */
		      
		
		break; // break case #i
          default:
		 $textMessage= "ตอบคุณ ".$userDisplayName."คุณไม่ได้ถามค่ะตามที่กำหนดค่ะ"; 
		 $bot->replyText($replyToken, $textMessage);
		break;
            }//end switch
	    
	   // ส่วนส่งกลับข้อมูลให้ LINE
           $response = $bot->replyMessage($replyToken,$replyData);
           if ($response->isSucceeded()) {
              echo 'Succeeded!';
              return;
              }
 
              // Failed ส่งข้อความไม่สำเร็จ
             $statusMessage = $response->getHTTPStatus() . ' ' . $response->getRawBody();
             echo $statusMessage;
             $bot->replyText($replyToken, $statusMessage);
         }//end if event is textMessage
}// end foreach event
	

?>
