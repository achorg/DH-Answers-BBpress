<form class="login" method="post" action="<?php bb_uri( 'bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS ); ?>">
		<?php
	printf(
		__( '<span class="header-button register-link"><a href="%1$s">Sign up</a></span> <span class="header-button login-link"><a href="%2$s">Log in</a></span>' ),
		bb_get_uri( 'register.php', null, BB_URI_CONTEXT_A_HREF + BB_URI_CONTEXT_BB_USER_FORMS ),
		bb_get_uri( 'bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS )
	);
	?>
</form>