<?php  
require_once(dirname(__FILE__) . '/simpletest/autorun.php');  
require_once('../bird-watcher.php');  
  
class TestBirdWatcher extends UnitTestCase {  

	function test_get_tweets() {
		$test_tweets = bw_get_tweets( 'test' );
		$this->assertIsA( $test_tweets , 'array' );
	}
	
	function test_tweet_content()	{
		$test_content = bw_get_tweets( 'test' );
		foreach( $test_content as $content ) {
			$this->assertIsA( $content->title, 'string' );
			$this->assertIsA( bw_get_tweet_user( $content->author ), 'string' );		
			$this->assertIsA( bw_get_tweet_id( $content->guid ), 'string' );
		}
	}
	
	// test these functions
	// !bw_check_duplicate( $tweet_id ) && !bw_has_mention( $tweet_text ) && bw_is_user( $tweet_user
  
}

?>