<?php

interface StatusRenderer {
	/* for a given status value ('closed', 'sticky', etc) return the
	 * image filename that should be used as an icon.  Note: this is
	 * only the image, not a full URL as it will be combined with the
	 * current icon-set base URL to resolve to an absolute URL.
	 */
    public function renderStatus($status);
    
    /* return a string describing the given status, to be used in a
     * tooltip display.  Note: returning an empty string will result
     * in no tooltip being displayed for a given status.
     */
    public function renderStatusTooltip($status);
}

?>
