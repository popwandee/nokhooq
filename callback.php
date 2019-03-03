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
	$replyToken = $event->getReplyToken();
	$replyData='No Data';
  // Postback Event
    if (($event instanceof \LINE\LINEBot\Event\PostbackEvent)) {
		$logger->info('Postback message has come'); 
	        $multiMessage =     new MultiMessageBuilder;
	        $textReplyMessage= "Postback message has come";
                $textMessage = new TextMessageBuilder($textReplyMessage);
		$multiMessage->add($textMessage);
	        $replyData = $multiMessage;
	        $response = $bot->replyMessage($replyToken,$replyData);
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
	   
	$groupId='';$roomId='';$userId=''; $userDisplayName='';// default value
	
	    // ส่วนตรวจสอบผู้ใช้
		$userId=$event->getUserId();
	  if((!is_null($userId)){
		$response = $bot->getProfile($userId);
                if ($response->isSucceeded()) {// ดึงค่าโดยแปลจาก JSON String .ให้อยู่ใรูปแบบโครงสร้าง ตัวแปร array
                   $userData = $response->getJSONDecodedBody(); // return array
                            // $userData['userId'] // $userData['displayName'] // $userData['pictureUrl']                            // $userData['statusMessage']
                   $userDisplayName = $userData['displayName'];
		   //$bot->replyText($replyToken, $userDisplayName); ใช้ตรวจสอบว่าผู้ถาม ชื่อ อะไร
		   $textReplyMessage = 'ตอบคุณ '.$userDisplayName.' User id : '.$userId;
                   $textMessage = new TextMessageBuilder($textReplyMessage);
		   $multiMessage->add($textMessage);
	           $replyData = $multiMessage;
	           $response = $bot->replyMessage($replyToken,$replyData);
		
		}
	    }//end is_null($userId);
	    
		// จบส่วนการตรวจสอบผู้ใช้
	
      switch ($explodeText[0]) {
	case '#i':
		 
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
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $textReplyMessage= "https://www.hooq.info/img/$rec->nationid.png";
                           $textMessage = new TextMessageBuilder($textReplyMessage);
			   $multiMessage->add($textMessage);
			   $picFullSize = "https://www.hooq.info/img/$rec->nationid.png";
                           $picThumbnail = "https://www.hooq.info/img/$rec->nationid.png";
			   $imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			   $multiMessage->add($imageMessage);
			    
                           }//end for each
	            $replyData = $multiMessage;
		   }else{ //$isData <0  ไม่พบข้อมูลที่ค้นหา
		          $textReplyMessage= "ไม่พบ ".$explodeText[1]."  ในฐานข้อมูลของหน่วย";
			  $textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$ranNumber=rand(1,407);
			 // $picFullSize = "https://www.hooq.info/photos/$ranNumber.jpg";
			  //$picThumbnail = "https://www.hooq.info/photos/$ranNumber.jpg";
                         // $picThumbnail = "https://www.hooq.info/photos/thumbnails/tn_$ranNumber.jpg";
			  //$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			 // $multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		        } // end $isData>0
		   }else{ // no $explodeText[1]
	                $textReplyMessage= "คุณให้ข้อมูลในการสอบถามไม่ครบถ้วนค่ะ";
			$textMessage = new TextMessageBuilder($textReplyMessage);
			  $multiMessage->add($textMessage);
			  //$ranNumber=rand(1,407);
			  //$picFullSize = "https://www.hooq.info/photos/$ranNumber.jpg";
                          //$picThumbnail = "https://www.hooq.info/photos/$ranNumber.jpg";
			  ////$imageMessage = new ImageMessageBuilder($picFullSize,$picThumbnail);
			  //$multiMessage->add($imageMessage);
			  $replyData = $multiMessage;
			 // กรณีจะตอบเฉพาะข้อความ
		      //$bot->replyText($replyToken, $textMessage);
		   }// end !is_null($explodeText[1])
		/* จบส่วนดึงข้อมูลจากฐานข้อมูล */
		break; // break case #i
    case '$เพิ่มชื่อ':
    $x_tra = str_replace('$เพิ่มชื่อ ',"", $text);
    $pieces = explode(" ", $x_tra);
    $rank=$pieces[0];
    $name=$pieces[1];
    $lastname=$pieces[2];
    $nickname=$pieces[3];
    $position=$pieces[4];
    $Tel1=$pieces[5];
    //Post New Data
    $newData = json_encode(array('rank' => $rank,'name'=> $name,'lastname'=> $lastname,'nickname'=> $nickname,'position'=> $position,'Tel1'=> $Tel1) );
    $opts = array('http' => array( 'method' => "POST",
                                  'header' => "Content-type: application/json",
                                  'content' => $newData
                                   )
                                );
    $url = 'https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY;
    $context = stream_context_create($opts);
    $returnValue = file_get_contents($url,false,$context);
    if($returnValue)$text = "ขอแสดงความยินดีด้วยค่ะ\n ลิซ่าได้เพิ่มชื่อ \n".$rank." ".$name." ".$lastname." ".$Tel1."\n ในรายชื่อเรียบร้อยแล้วค่ะ";
    else $text="ไม่สามารถเพิ่มชื่อได้";
    $bot->replyText($replyToken, $text);
  		break; // break case #i
	case '#':
	      $json = file_get_contents('https://api.mlab.com/api/1/databases/crma51/collections/phonebook?apiKey='.MLAB_API_KEY.'&q={"$or":[{"name":{"$regex":"'.$explodeText[1].'"}},{"lastname":{"$regex":"'.$explodeText[1].'"}},{"nickname":{"$regex":"'.$explodeText[1].'"}},{"nickname2":{"$regex":"'.$explodeText[1].'"}},{"position":{"$regex":"'.$explodeText[1].'"}}]}');
              $data = json_decode($json);
              $isData=sizeof($data);
              if($isData >0){
		   $result = "";
		   $count = 1;
		   $hasImageUrlStatus = false;
		      // default image for flex message
		   $imageUrl="https://www.hooq.info/wp-content/uploads/2019/02/Connect-with-precision.jpg";
                foreach($data as $rec){
                  $result= $result.$count.' '.$rec->rank.$rec->name.' '.$rec->lastname.' ('.$rec->position.' '.$rec->deploy_position.') '.$rec->Email.' โทร '.$rec->Tel1." ค่ะ\n\n";
                  if(!is_null($rec->Image) and (!$hasImageUrlStatus)){
			 $imageUrlStatus=true;
		 	 $imageUrl="https://www.hooq.info/wp-content/uploads/".$rec->Image;
		  }
			$count++;
                }//end for each
		    $textReplyMessage= $result;
		     $flexData = new ReplyTranslateMessage;
                     $replyData = $flexData->get($explodeText[1],$textReplyMessage,$imageUrl);
		      /*
		    $textMessage = new TextMessageBuilder($textReplyMessage);
		    $multiMessage->add($textMessage);
		    $replyData = $multiMessage;  
		    */
	      }else{
		  $text= "ลิซ่า หาชื่อ ".$explodeText[1]." ไม่พบค่ะ , อัพเดตข้อมูลให้ด้วยนะค่ะ ";
		     $result= $text;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,83);
	             $picFullSize = "https://www.hooq.info/photos/300.jpg";
                     $replyData = $flexData->get($explodeText[1],$result,$picFullSize);
	      }
                     
                   break;

  case '$lisa':
            //Post New Data
		    $indexCount=1;$answer='';
	    foreach($explodeText as $rec){
		    $indexCount++;
		    if($indexCount>1){
		    $answer= $answer." ".$explodeText[$indexCount];
		    }
	    }
            $newData = json_encode(array('question' => $explodeText[1],'answer'=> $answer) );
            $opts = array('http' => array( 'method' => "POST",
                                          'header' => "Content-type: application/json",
                                          'content' => $newData
                                           )
                                        );
            // เพิ่มเงื่อนไข ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือยัง
            $url = 'https://api.mlab.com/api/1/databases/hooqline/collections/hooqbot?apiKey='.MLAB_API_KEY.'';
            $context = stream_context_create($opts);
            $returnValue = file_get_contents($url,false,$context);
            if($returnValue){
		    $text =  'ขอบคุณที่สอนลิซ่าค่ะ';
		    $text2 = 'ลิซ่าจำได้แล้วว่า '.$explodeText[1]." คือ ".$answer;
	    }else{ $text="Cannot teach Lisa";
		  $text2 = '';
		 }
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($text,$text2,$picFullSize);
            break;
		      // ---------------------------------------------------------------------------//
 case 'แปล':  
           $source = 'th';
           $text_parameter = str_replace("แปล ","", $text);  
           if (!is_null($explodeText[1])){
		   switch ($explodeText[1]) {
			case 'cn': $target = 'zh-CN' ;$text_parameter = str_replace("cn","", $text_parameter); break; // china
			case 'ko': $target = 'ko' ;$text_parameter = str_replace("ko","", $text_parameter); break; // korea
			case 'de': $target = 'de' ;$text_parameter = str_replace("de","", $text_parameter); break; // german
			case 'ms': $target = 'ms' ;$text_parameter = str_replace("ms","", $text_parameter); break; // malaysia
			case 'id': $target = 'id' ;$text_parameter = str_replace("id","", $text_parameter); break; // indonesia
			default: $target = 'en'; break;
		   }// end switch
	   }// end if
             $trans = new GoogleTranslate();
            $result = "แปลว่า\n".$trans->translate($source, $target, $text_parameter)."\nค่ะ";
		       $question = $text_parameter;
		     $answer = $result;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($question,$answer,$picFullSize);
             
                break;

case '#tran':
		      
            $text_parameter = str_replace("#tran ","", $text);  
           if (!is_null($explodeText[1])){
		   switch ($explodeText[1]) {
			case 'cn': $source = 'zh-CN' ;$text_parameter = str_replace("cn","", $text_parameter); break;
			case 'ko': $source = 'ko' ;$text_parameter = str_replace("ko","", $text_parameter); break;
			case 'de': $source = 'de' ;$text_parameter = str_replace("de","", $text_parameter); break;
			case 'ms': $source = 'ms' ;$text_parameter = str_replace("ms","", $text_parameter); break; // malaysia
			case 'id': $source = 'id' ;$text_parameter = str_replace("id","", $text_parameter); break; // indonesia
			default: $source = 'en'; break;
		   }// end switch
	   }// end if
            $target = 'th';
            $trans = new GoogleTranslate();
            $result = "แปลว่า\n".$trans->translate($source, $target, $text_parameter)."\nค่ะ";
           $question = $text_parameter;
		      $answer = $result;
		     $flexData = new ReplyTranslateMessage;
		     $image=rand(1,409);
	             $picFullSize = "https://www.hooq.info/photos/$image.jpg";
                     $replyData = $flexData->get($question,$answer,$picFullSize);
                               break;

          default: break;
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
