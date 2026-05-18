<?php
/**
 * Template Name: Community
 * Description: Custom page template that loads the bbPress forum archive inside the member portal.
 */

defined( 'ABSPATH' ) || exit;

// We only want the core bbPress templates to load here, without duplicating the header/footer
bbp_get_template_part( 'archive', 'forum' );