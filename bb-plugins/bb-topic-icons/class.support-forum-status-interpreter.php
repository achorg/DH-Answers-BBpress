<?php

class SupportForumStatusInterpreter {

  public function getAllStatuses() {
    global $support_forum;
  	$statuses = array('normal', 'sticky', 'closed');
  	if(isset($support_forum)) {
  		$statuses[] = 'resolved';
  		$statuses[] = 'not-resolved';
  		$statuses[] = 'non-issue';
  	}
  	return $statuses;
  }

  public function getStatus($location, $topic) {
    global $support_forum;

    if (isset($topic->topic_resolved) && isset($support_forum)) {
      return $this->resolve_support_status($topic->topic_resolved);
    }
  
    if ($this->is_closed_topic($topic)) {
        return "closed";
    }

    if ($this->is_sticky_topic($location, $topic)) {
        return "sticky";
    }
    
    $enabled = bb_get_option('support_forum_enabled');
    if (in_array($topic->forum_id, $enabled) && isset($support_forum)) {
      return $this->resolve_support_status(bb_get_option('support_forum_default_status'));
    }

    return "normal";
  }
    
  private function resolve_support_status($status) {
    if ("yes" == $status) {
      return 'resolved';
    } else if ("no" == $status) {
      return 'not-resolved';
    } else if ("mu" == $status) {
      return 'non-issue';
  	}

  return $status;
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
