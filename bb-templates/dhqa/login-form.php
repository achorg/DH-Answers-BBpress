<form class="login" method="post" action="<?php bb_uri( 'bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS ); ?>">
	<p class="span-8 last">
		<?php
	printf(
		__( '<span class="header-button"><a href="%1$s">Register</a></span> <span class="header-button"><a href="%2$s">Log in</a></span>' ),
		bb_get_uri( 'register.php', null, BB_URI_CONTEXT_A_HREF + BB_URI_CONTEXT_BB_USER_FORMS ),
		bb_get_uri( 'bb-login.php', null, BB_URI_CONTEXT_FORM_ACTION + BB_URI_CONTEXT_BB_USER_FORMS )
	);
	?>

	</p>
</form>