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
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ContainerDirection;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use PHPUnit\Framework\TestCase;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentImageSize;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\CarouselContainerBuilder;

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
      $json = '{
 "type":"bubble",
 "direction":"ltr",
 "header":{
   "type":"box",
   "layout":"vertical",
   "contents":[
     {"type":"text", "text":"header"}
   ]
 },
 "hero":{
   "type":"image",
   "url":"https://example.com/hero.png"
 },
 "body":{
   "type":"box",
   "layout":"vertical",
   "contents":[
     {"type":"text", "text":"body"}
   ]
 },
 "footer":{
   "type":"box",
   "layout":"vertical",
   "contents":[
     {"type":"text", "text":"footer"}
   ]
 },
 "styles":{
   "body":{
     "separator": true,
     "separatorColor": "#000000"
   }
 }
}';
      $builder = new BubbleContainerBuilder(
      ContainerDirection::LTR,
      new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('header')]),
      new ImageComponentBuilder('https://example.com/hero.png'),
      new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('body')]),
      new BoxComponentBuilder(ComponentLayout::VERTICAL, [new TextComponentBuilder('footer')]),
      BubbleStylesBuilder::builder()->setBody(new BlockStyleBuilder(null, true, '#000000'))
  );
  $this->assertEquals(json_decode($json, true), $builder->build());

      break;

		  default:
       //$handler = new ImageMessageHandler($bot, $logger, $req, $event);
		  // $replyText=$replyMessage.$displayName.$statusMessage;
		  break;
            }//end switch
      $replyText='Nothing';
	    $bot->replyMessage($reply_token, $replyText);
    }//end if text
}// end foreach event


class FlexSuspectedPeople
{
    private static $items = [
        '111' => [
            'photo' => 'https://example.com/photo1.png',
            'name' => 'Arm Chair, White',
            'price' => 49.99,
            'stock' => true,
        ],
        '112' => [
            'photo' => 'https://example.com/photo2.png',
            'name' => 'Metal Desk Lamp',
            'price' => 11.99,
            'stock' => false,
        ],
    ];
    /**
     * Create sample shopping flex message
     *
     * @return \LINE\LINEBot\MessageBuilder\FlexMessageBuilder
     */
    public static function get()
    {
        return FlexMessageBuilder::builder()
            ->setAltText('Suspected People')
            ->setContents(new CarouselContainerBuilder([
                self::createItemBubble(111),
                self::createItemBubble(112),
                self::createMoreBubble()
            ]));
    }
    private static function createItemBubble($itemId)
    {
        $item = self::$items[$itemId];
        return BubbleContainerBuilder::builder()
            ->setHero(self::createItemHeroBlock($item))
            ->setBody(self::createItemBodyBlock($item))
            ->setFooter(self::createItemFooterBlock($item));
    }
    private static function createItemHeroBlock($item)
    {
        return ImageComponentBuilder::builder()
            ->setUrl($item['photo'])
            ->setSize(ComponentImageSize::FULL)
            ->setAspectRatio(ComponentImageAspectRatio::R20TO13)
            ->setAspectMode(ComponentImageAspectMode::COVER);
    }
    private static function createItemBodyBlock($item)
    {
        $components = [];
        $components[] = TextComponentBuilder::builder()
            ->setText($item['name'])
            ->setWrap(true)
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::XL);
        $price = explode('.', number_format($item['price'], 2));
        $components[] = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText('$'.$price[0])
                    ->setWrap(true)
                    ->setWeight(ComponentFontWeight::BOLD)
                    ->setSize(ComponentFontSize::XL)
                    ->setFlex(0),
                TextComponentBuilder::builder()
                    ->setText('.'.$price[1])
                    ->setWrap(true)
                    ->setWeight(ComponentFontWeight::BOLD)
                    ->setSize(ComponentFontSize::SM)
                    ->setFlex(0)
            ]);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents($components);
    }
    private static function createItemFooterBlock($item)
    {
        $color = $item['stock'] ? null : '#aaaaaa';
        $cartButton = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setColor($color)
            ->setAction(
                new UriTemplateActionBuilder(
                    'Add to Cart',
                    'https://example.com'
                )
            );
        $wishButton = ButtonComponentBuilder::builder()
            ->setAction(
                new UriTemplateActionBuilder(
                    'Add to wishlist',
                    'https://example.com'
                )
            );
        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setSpacing(ComponentSpacing::SM)
            ->setContents([$cartButton, $wishButton]);
    }
    private static function createMoreBubble()
    {
        return BubbleContainerBuilder::builder()
            ->setBody(
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::VERTICAL)
                    ->setSpacing(ComponentSpacing::SM)
                    ->setContents([
                        ButtonComponentBuilder::builder()
                            ->setFlex(1)
                            ->setGravity(ComponentGravity::CENTER)
                            ->setAction(new UriTemplateActionBuilder('See more', 'https://example.com'))
                    ])
            );
    }
}//end class
