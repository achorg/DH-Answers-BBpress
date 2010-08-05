<?php if ( !bb_is_topic() ) : ?>
<p id="post-form-title-container">
	<label for="topic"><?php _e('Question (please be clear and concise)'); ?>
		<input name="topic" type="text" id="topic" size="50" maxlength="80" tabindex="1" />
	</label>
</p>

<!-- should this be part of a plugin? maybe even the twitter notification plugin? -->
<p id="twitter_topic_preview" style="display: none;">
	<?php _e('This question will be posted to Twitter as follows: '); ?><span id="tweet_preview"></span>
	<input type="hidden" name="tweet" />
	<script type="text/javascript">
		jQuery('#topic').keyup(function() {
			if (jQuery('#twitter_topic_preview').is(':hidden')) {
				jQuery('#twitter_topic_preview').show();
			}
			var tweet = '@dhanswers new topic: ' + jQuery('#topic').val();
			if (tweet.length > 140) { // useless for now since topic is limited to 50 chars
				tweet = tweet.substring('0,139') + '…';
			}
			jQuery('#tweet_preview').html(tweet);
			jQuery('#tweet').val(tweet); // we could use this value server side
		})
	</script>
</p>

<?php endif; do_action( 'post_form_pre_post' ); ?>
<p id="post-form-post-container">
	<label for="post_content"><?php _e('Message'); ?>
		<textarea name="post_content" cols="50" rows="8" id="post_content" tabindex="3"></textarea>
	</label>
</p>
<p id="post-form-tags-container">
	<label for="tags-input"><?php printf(__('Tags (comma-separated)'), bb_get_tag_page_link()) ?>
		<input id="tags-input" name="tags" type="text" size="50" maxlength="100" value="<?php bb_tag_name(); ?>" tabindex="4" />
	</label>
</p>
<?php if ( bb_is_tag() || bb_is_front() ) : ?>
<p id="post-form-forum-container">
	<label for="forum-id"><?php _e('Category'); ?>
		<?php bb_new_topic_forum_dropdown(); ?>
	</label>
</p>
<?php endif; ?>
<p id="post-form-submit-container" class="submit">
  <input type="submit" id="postformsub" name="Submit" value="<?php echo esc_attr__( 'Send Message &raquo;' ); ?>" tabindex="4" />
</p>

<p id="post-form-allowed-container" class="allowed"><?php _e('You may use the following HTML elements:'); ?> <code><?php allowed_markup(); ?></code>.</p>
