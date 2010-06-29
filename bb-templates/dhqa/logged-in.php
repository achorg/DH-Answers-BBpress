<?php if (in_array(bb_get_location(), array('search-page', 'profile-page', 'tag-page'))) { ?>
<span class="header-button ask-link"><a href="/answers/?new=1">Ask a Question</a></span>
<?php } else { ?>
<span class="header-button ask-link"><?php bb_new_topic_link('Ask a Question'); ?></span>
<?php } ?>
<span class="header-button profile-link"><?php bb_profile_link('My Profile'); ?></span>
<?php if($bb_current_user->has_cap('administrate') || $bb_current_user->has_cap('moderate')) { ?>
<span class="header-button admin-link"><?php bb_admin_link();?></span>
<?php } ?>
<span class="header-button login-link"><?php bb_logout_link(); ?></span>
