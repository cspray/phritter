<?php

declare(strict_types = 1);

/**
 * @license See LICENSE file in project root
 */

namespace Cspray\Phritter\Test;

use Cspray\Phritter\JsonChunkCombiner;
use PHPUnit_Framework_TestCase as UnitTestCase;

class JsonChunkCombinerTest extends UnitTestCase {

    private function chunks() : array {
        return [
            '{"created_at":"Sat Feb 06 17:29:31 +0000 2016","id":696022926747955201,"id_str":"696022926747955201","text":"Choo, choo! All aboard the testing train!","source":"\u003ca href=\"http:\/\/twitter.com\" rel=\"nofollow\"\u003eTwitter Web Client\u003c\/a\u003e","truncated":false,"in_reply_to_status_id":null,"in_reply_to_status_id_str":null,"in_reply_to_user_id":null,"in_reply_to_user_id_str":null,"in_reply_to_screen_name":null,"user":{"id":310960851,"id_str":"310960851","name":"Charles","screen_name":"charlesspray","location":"VT","url":"http:\/\/cspray.net","description":"Web dev @Designbookcom. Husband, dog trainer, video gamer. Views expressed are my own.","protected":false,"verified":false,"followers_count":91,"friends_count":280,"listed_count":3,"favourites_count":160,"statuses_count":1237,"created_at":"Sat Jun 04 17:08:51 +0000 2011","utc_offset":-18000,"time_zone":"Eastern Time',
            '(US & Canada)","geo_enabled":false,"lang":"en","contributors_enabled":false,"is_translator":false,"profile_background_color":"000000","profile_background',
            '_image_url":"http:\/\/abs.twimg.com\/images\/themes\/theme14\/bg.gif","profile_background_image_url_https":"https:\/\/abs.twimg.com\/images\/themes\/theme14\/bg.gif","profile_background_tile":false,"profile_link_color":"ABB8C2","profile_sidebar_border_color":"000000","profile_sidebar_fill_color":"000000","profile_text_color":"000000","profile_use_background_image":false,"profile_image_url":"http:\/\/pbs.twimg.com\/profile_images\/445731529084243970\/qgs-hdik_normal.jpeg","profile_image_url_https":"https:\/\/pbs.twimg.com\/profile_images\/445731529084243970\/qgs-hdik_normal.jpeg","profile_banner_url":"https:\/\/pbs.twimg.com\/profile_banners\/310960851\/1395100245","default_profile":false,"default_profile_image":false,"following":null,"follow_request_sent":null,"notifications":null},"geo":null,"coordinates":null,"place":null,"contributors":null,"is_quote_status":false,"retweet_count":0,"favorite_count":0,"entities":{"hashtags":[],"urls":[],"user_mentions":[],"symbols":[]},"favorited":false,"retweeted":false,"filter_level":"low","lang":"en","timestamp_ms":"1454779771874"}'
        ];
    }

    public function testIsValidJson() {
        $chunks = $this->chunks();

        $chunkCombiner = new JsonChunkCombiner();

        $chunkCombiner->push($chunks[0]);

        $this->assertFalse($chunkCombiner->isValidJson(), 'Should not be valid JSON after 1 chunk');

        $chunkCombiner->push($chunks[1]);
        $this->assertFalse($chunkCombiner->isValidJson(), 'Should not be valid JSON after 2 chunks');

        $chunkCombiner->push($chunks[2]);
        $this->assertTrue($chunkCombiner->isValidJson(), 'Should be valid JSON after 3 chunks');
    }

}