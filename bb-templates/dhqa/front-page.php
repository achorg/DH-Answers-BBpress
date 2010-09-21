<?php bb_get_header(); ?>

<?php if ( $forums ) : ?>

<div id="sidebar" role="main" class="span-6">
<h2>About</h2>
<p>We're building a community-based Q&A board for digital humanities questions that need (just a little) more than 140 character answers.</p>
<p><a href="http://chronicle.com/blog/ProfHacker/27/" id="ph-logo"><img alt="ProfHacker" title="ProfHacker" src="<?php echo(bb_active_theme_uri().'images/ph-icon.png'); ?>"/></a><strong><a href="http://twitter.com/dhanswers">@DHAnswers</a></strong> is a collaborative project of the <a href="http://www.ach.org">Association for Computers and the Humanities</a> (ACH) and the Chronicle of Higher Education's <a href="http://chronicle.com/blog/ProfHacker/27/">ProfHacker</a>.</p>
<p><a id="follow-us" href="http://www.twitter.com/DHAnswers">Follow @DHAnswers on Twitter</a></p>
<!-- <h2>How to</h2>
<p><a href="/answers/bb-login.php">Create an account</a> and <?php bb_new_topic_link('ask a question'); ?> </p> -->
<h2><?php _e('Popular Tags'); ?></h2>
<p class="frontpageheatmap"><?php bb_tag_heat_map(10, 18, 'px', 20 ); ?></p>

<h2><?php _e('Find Questions'); ?></h2>
<ul id="views">
<?php foreach ( bb_get_views() as $the_view => $title ) : ?>
<li class="view"><a href="<?php view_link( $the_view ); ?>"><?php view_name( $the_view ); ?></a></li>
<?php endforeach; ?>
</ul>

</div>

<div id="discussions" class="span-18 last">
<?php if ( $topics || $super_stickies ) : ?>

<h2><?php _e('Latest Questions'); ?></h2>

<table id="latest">
<tr class="table-head">
	<th><?php _e('Question'); ?></th>
	<th><?php _e('Posts'); ?></th>
	<!-- <th><?php _e('Voices'); ?></th> -->
	<th><?php _e('Last Poster'); ?></th>
	<th><?php _e('Freshness'); ?></th>
</tr>

<?php if ( $super_stickies ) : foreach ( $super_stickies as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <big><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></big><?php topic_page_links(); ?></td>
	<td class="num"><?php topic_posts(); ?></td>
	<!-- <td class="num"><?php bb_topic_voices(); ?></td> -->
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; // $super_stickies ?>

<?php if ( $topics ) : foreach ( $topics as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a><?php topic_page_links(); ?></td>
	<td class="num"><?php topic_posts(); ?></td>
	<!-- <td class="num"><?php bb_topic_voices(); ?></td> -->
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; // $topics ?>
</table>
<?php bb_latest_topics_pages( array( 'before' => '<div class="nav">', 'after' => '</div>' ) ); ?>
<?php endif; // $topics or $super_stickies ?>

<?php if ( bb_forums() ) : ?>
<h2><?php _e('Categories'); ?></h2>
<table id="forumlist">

<tr class="table-head">
	<th><?php _e('Category'); ?></th>
	<th><?php _e('Questions'); ?></th>
	<th><?php _e('Posts'); ?></th>
</tr>
<?php while ( bb_forum() ) : ?>
<?php if (bb_get_forum_is_category()) : ?>
<tr<?php bb_forum_class('bb-category'); ?>>
	<td colspan="3"><?php bb_forum_pad( '<div class="nest">' ); ?><a href="<?php forum_link(); ?>"><?php forum_name(); ?></a><?php forum_description( array( 'before' => '<small> &#8211; ', 'after' => '</small>' ) ); ?><?php bb_forum_pad( '</div>' ); ?></td>
</tr>
<?php continue; endif; ?>
<tr<?php bb_forum_class(); ?>>
	<td><?php bb_forum_pad( '<div class="nest">' ); ?><a href="<?php forum_link(); ?>"><?php forum_name(); ?></a><?php forum_description( array( 'before' => '<small> &#8211; ', 'after' => '</small>' ) ); ?><?php bb_forum_pad( '</div>' ); ?></td>
	<td class="num"><?php forum_topics(); ?></td>
	<td class="num"><?php forum_posts(); ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; // bb_forums() ?>


</div>

<?php else : // $forums ?>

<div class="bbcrumb"><a href="<?php bb_uri(); ?>"><?php bb_option('name'); ?></a> &raquo; <?php _e('Ask a New Question'); ?></div>

<?php post_form(); endif; // $forums ?>

<p class="rss-link"><a href="/answers/rss/topics/" class="rss-link"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr> feed for new questions') ?></a></p>

<?php bb_get_footer(); ?>
