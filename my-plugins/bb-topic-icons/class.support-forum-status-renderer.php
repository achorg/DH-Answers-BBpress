<?php

class SupportForumStatusRenderer {
    public $status_map;
    public $status_description_map;
    
    function __construct() {
    	$this->status_map = array(
    		"normal" => "topic.png",
    		"hot" => "hot.png",
    		"sticky" => "sticky.png",
    		"closed" => "locked.png",
    		"resolved" => "resolved.png",
    		"not-resolved" => "not-resolved.png",
    		"non-issue" => "non-issue.png",
    	);
    	$this->status_description_map = array(
    		"normal" => "",
    		"hot" => "Very&nbsp;Busy&nbsp;Topic",
    		"sticky" => "Sticky&nbsp;Topic",
    		"closed" => "Closed&nbsp;Topic",
    		"resolved" => "Resolved&nbsp;Issue",
    		"not-resolved" => "Open&nbsp;Issue",
    		"non-issue" => "Not&nbsp;an&nbsp;Issue",
    	);
    }
    
    public function renderStatus($status) {
    	return $this->status_map[$status];
    }
    
    public function renderStatusTooltip($status) {
    	return $this->status_description_map[$status];
    }
}

?>
