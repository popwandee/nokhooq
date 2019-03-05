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
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentIconSize;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentButtonHeight;
use LINE\LINEBot\Constant\Flex\ComponentSpaceSize;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
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
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\IconComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SpacerComponentBuilder;
use LINE\LINEBot\QuickReplyBuilder\ButtonBuilder\QuickReplyButtonBuilder;
use LINE\LINEBot\QuickReplyBuilder\QuickReplyMessageBuilder;
$logger = new Logger('LineBot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));
define("MLAB_API_KEY", '6QxfLc4uRn3vWrlgzsWtzTXBW7CYVsQv');
define("LINE_MESSAGING_API_CHANNEL_SECRET", 'eb6cf532359c17403e5e20339b389466');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'yf5kpt5rDBiNTVwoI/tkKWlCXvD2fJBq9dDKfqxcuu7qIwf+auxo5hs3wGJsj0Shq5UCfkhGf8gLrcB4PluHJ4ViBppUh5/6PllJ4xi7z+dMUTaNwLa3FXC+FwgVqSvbn7WGnUASUMtkgsh/9dhl9AdB04t89/1O/w1cDnyilFU=');
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
	$replyToken = $event->getReplyToken();
	$replyData='No Data';

  // Postback Event
    if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
		$logger->info('Postback message has come');
		continue;
	}
	// Location Event
    if  ($event instanceof LINE\LINEBot\Event\MessageEvent\LocationMessage) {
		$logger->info("location -> ".$event->getLatitude().",".$event->getLongitude());
	        $multiMessage =     new MultiMessageBuilder;
	        $textReplyMessage= "location -> ".$event->getLatitude().",".$event->getLongitude();
                $textMessage = new TextMessageBuilder($textReplyMessage);
		$multiMessage->add($textMessage);
	        $replyData = $multiMessage;
	        $response = $bot->replyMessage($replyToken,$replyData);
		continue;
	}
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {

        $text = $event->getText();
        $text = strtolower($text);
        $explodeText=explode(" ",$text);
	$textReplyMessage="";

        $multiMessage =     new MultiMessageBuilder;

	$groupId='';$roomId=''; $userDisplayName='';// default value

	    // ส่วนตรวจสอบผู้ใช้
		$userId=$event->getUserId();
	   if((!is_null($userId)){
		$response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array
                   $userData = $response->getJSONDecodedBody(); // return array
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']                            // $userData['statusMessage']
                   $userDisplayName = $userData['displayName'];
		   $bot->replyText($replyToken, $userDisplayName); ใช้ตรวจสอบว่าผู้ถาม ชื่อ อะไร
		}else{
		 //$bot->replyText($replyToken, $userId);  ใช้ตรวจสอบว่าผู้ถาม ID อะไร
			$userDisplayName = $userId;
		}// end get profile
	   }//end is_null($userId);
	     $textReplyMessage = 'ตอบคุณ '.$userDisplayName.' User id : '.$userId;
                    $textMessage = new TextMessageBuilder($textReplyMessage);
		    $multiMessage->add($textMessage);
		// จบส่วนการตรวจสอบผู้ใช้
	

      switch ($explodeText[0]) {

	case '#p':
		/* ส่วนดึงข้อมูลจากฐานข้อมูล */
		if (!is_null($explodeText[1])){
		   $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/people?apiKey='.MLAB_API_KEY.'&q={"nationid":"'.$explodeText[1].'"}');
                   $data = json_decode($json);
                   $isData=sizeof($data);

                 if($isData >0){
		    $count=1;
                    foreach($data as $rec){
			   $count++;
                           $textReplyMessage= "\nหมายเลข ปชช. ".$rec->nationid."\nชื่อ".$rec->name."\nที่อยู่".$rec->address."\nหมายเหตุ".$rec->note;
                          /* $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $textReplyMessage= "https://www.hooq.info/img/$rec->nationid.png";
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $picFullSize = "https://www.hooq.info/img/$rec->nationid.png";
                           $picThumbnail = "https://www.hooq.info/img/$rec->nationid.png";
			   $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			   $multiMessage->add($imageMessage);
			    */
			    
			   $picFullSize = "https://www.hooq.info/img/$rec->nationid.png";
                           }//end for each
	           // $replyData = $multiMessage;
			 
		     $flexData = new ReplyTranslateMessage;
                     $replyData = $flexData->get($explodeText[1],$textReplyMessage,$picFullSize);
			
			 
		   }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
		          $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			 
			  $replyData = $multiMessage;
			
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	                $textReplyMessage= "คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ,/n กรุณาพิมพ์ #p เว้นวรรค ตามด้วยหมายเลข ปชช. ค่ะ";
			$textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			 
			  $replyData = $multiMessage;
			
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */


		break; // break case #i
   
          default: $replyData='';break;
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


class ReplyTranslateMessage
{
    /**
     * Create  flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get($question,$answer,$picUrl)
    {
        return FlexMessageBuilder::builder()
            ->setAltText('Lisa')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setHero(self::createHeroBlock($picUrl))
                    ->setBody(self::createBodyBlock($question,$answer))
                    ->setFooter(self::createFooterBlock($picUrl))
            );
    }
    private static function createHeroBlock($picUrl)
    {
	   
        return ImageComponentBuilder::builder()
            ->setUrl($picUrl)
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::FIT)
            ->setAction(new UriTemplateActionBuilder(null, $picUrl));
    }
    private static function createBodyBlock($question,$answer)
    {
        $title = TextComponentBuilder::builder()
            ->setText($question)
            ->setWeight(ComponentFontWeight::BOLD)
	    ->setwrap(true)
            ->setSize(ComponentFontSize::SM);
        
        $textDetail = TextComponentBuilder::builder()
            ->setText($answer)
            ->setSize(ComponentFontSize::LG)
            ->setColor('#000000')
            ->setMargin(ComponentMargin::MD)
	    ->setwrap(true)
            ->setFlex(2);
        $review = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            //->setLayout(ComponentLayout::BASELINE)
            ->setMargin(ComponentMargin::LG)
            //->setMargin(ComponentMargin::SM)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$title,$textDetail]);
	
	    /*    
        $place = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('ที่อยู่')
                    ->setColor('#aaaaaa')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText('Samsen, Bangkok')
                    ->setWrap(true)
                    ->setColor('#666666')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(5)
            ]);
        $time = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('Time')
                    ->setColor('#aaaaaa')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(1),
                TextComponentBuilder::builder()
                    ->setText('10:00 - 23:00')
                    ->setWrap(true)
                    ->setColor('#666666')
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(5)
            ]);
	    
        $info = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::LG)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$place, $time]);*/
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            //->setContents([$review, $info]);
            ->setContents([$review]);
    }
    private static function createFooterBlock($picUrl)
    {
        
        $websiteButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::LINK)
            ->setHeight(ComponentButtonHeight::SM)
            ->setFlex(0)
            ->setAction(new UriTemplateActionBuilder('เพิ่มเติม','https://www.hooq.info'));
        $spacer = new SpacerComponentBuilder(ComponentSpaceSize::SM);
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setFlex(0)
            ->setContents([$websiteButton, $spacer]);
    }

} 

/*
function pushMsg($arrayHeader,$arrayPostData){
		 $strUrl ="https://api.line.me/v2/bot/message/push";
		 $ch=curl_init();
		 curl_setopt($ch,CURLOPT_URL,$strUrl);
		 curl_setopt($ch,CURLOPT_HEADER,false);
		 curl_setopt($ch,CURLOPT_POST,true);
		 curl_setopt($ch,CURLOPT_HTTPHEADER,$arrayHeader);
		 curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($arrayPostData));
		 curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		 curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		 $result=curl_exec($ch);
		 curl_close($ch);
		 }
		 */

  /*
	 case '$เพิ่มรถ':
              $x_tra = str_replace("#เพิ่มรถ ","", $text);
              $pieces = explode("|", $x_tra);
              $_licence_plate=$pieces[0];
              $_brand=$pieces[1];
              $_model=$pieces[2];
              $_color=$pieces[3];
              $_owner=$pieces[4];
              $_user=$pieces[5];
              $_note=$pieces[6];
              //Post New Data
              $newData = json_encode(array('licence_plate' => $_licence_plate,'brand'=> $_brand,'model'=> $_model,'color'=> $_color,'owner'=> $_owner,'user'=> $_user,'note'=> $_note) );
              $opts = array('http' => array( 'method' => "POST",
                                            'header' => "Content-type: application/json",
                                            'content' => $newData
                                             )
                                          );
              $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY;
              $context = stream_context_create($opts);
              $returnValue = file_get_contents($url,false,$context);
              if($returnValue)$text = 'เพิ่มรถสำเร็จแล้ว';
              else $text="ไม่สามารถเพิ่มรถได้";
              $bot->replyText($reply_token, $text);

              break;
	 case '$ทะเบียน':
		  $json = file_get_contents('https://api.mlab.com/api/1/databases/hooqline/collections/carregister?apiKey='.MLAB_API_KEY.'&q={"licence_plate":"'.$explodeText[1].'"}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		   $text="";
		   $count=1;
                foreach($data as $rec){
                  $text= $text.$count.' '.$rec->licence_plate.' '.$rec->brand.' '.$rec->model.' '.$rec->color."\n ผู้ถือกรรมสิทธิ์ ".$rec->owner."\n ผู้ครอบครอง ".$rec->user."\n หมายเหตุ/ประวัติ ".$rec->note."\n\n";
                  $count++;
                }//end for each
	      }else{
		  $text= "ไม่พบข้อมูลทะเบียนรถ ".$explodeText[1];
	      }
                  $bot->replyText($reply_token, $text);
                   break;
         


      */
