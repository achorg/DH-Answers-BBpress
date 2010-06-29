<?php

class DefaultStatusInterpreter implements StatusInterpreter {
    public $busy_threshold = 15;
    
    function __construct($threshold) {
    	$this->busy_threshold = $threshold;
    }

	public function getAllStatuses() {
		return array('normal', 'hot', 'sticky', 'closed');
	}

    public function getStatus($location, $topic) {
        if ($this->is_sticky_topic($location, $topic)) {
            return "sticky";
        }

        if ($this->is_closed_topic($topic)) {
            return "closed";
        }

        if ($this->is_hot_topic($topic)) {
            return "hot";
        }

        return "normal";
    }

	private function is_hot_topic($topic) {
		return $topic->topic_posts > $this->busy_threshold;
	}

    private function is_sticky_topic($location, $topic) {
        return ('front-page' == $location) ? ( '2' === $topic->topic_sticky ) :
          ( '1' === $topic->topic_sticky || '2' === $topic->topic_sticky );
    }

    private function is_closed_topic($topic) {
        return ( '0' === $topic->topic_open );
    }
}

?>
