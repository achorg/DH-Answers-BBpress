		</div>
			<div id="footer" role="contentinfo" class="span-24">
				<p id="lang-switch">
					<span class="lang-label"><?php _e('Language'); ?></span>
					<?php do_action('bb_language_switcher',''); ?>
				</p>			
				<p>A project of the <a href="http://www.ach.org">Association for Computers and the Humanities</a> and the Chronicle of Higher Education's <a href="http://profhacker.com">ProfHacker</a></p>
				<p>Icons by <a href="http://www.famfamfam.com/lab/icons/silk/">famfamfam</a></p>
				<p><a href="http://lib.virginia.edu/scholarslab"><img src="<?php echo(bb_active_theme_uri().'images/slab-logo.jpg'); ?>" alt="Scholars' Lab" title="Scholars' Lab"/></a></p>
			</div>
	</div>

<?php do_action('bb_foot'); ?>

</body>
</html>
