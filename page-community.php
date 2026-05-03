<?php
/**
 * Template Name: Community
 * Description: Custom page template that loads the bbPress forum archive.
 */

get_header();

// Optional: your custom wrapper markup
echo '<!-- CaTNA Community Page Template Loaded -->';

// Load the bbPress forum archive template (archive-forum.php)
bbp_get_template_part( 'archive', 'forum' );

get_footer();
