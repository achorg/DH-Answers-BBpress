<span class="header-button profile-link"><?php printf(__('Welcome, %1$s'), bb_get_profile_link(bb_get_current_user_info( 'name' )));?></span>
<span class="header-button admin-link"><?php bb_admin_link();?></span>
<span class="header-button login-link"><?php bb_logout_link(); ?></span>
