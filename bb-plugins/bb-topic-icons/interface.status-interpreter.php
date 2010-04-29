<?php

interface StatusInterpreter {
	/* return an array containing all of the status values
	 * that this interpreter knows abot.
	 */
	public function getAllStatuses();

	/* for a given topic, determine the status and return
	 * it.
	 */
    public function getStatus($location, $topic);
}

?>
