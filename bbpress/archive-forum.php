<?php
/**
 * bbPress - Forum Archive (Customized for CaTNA)
 */

get_header();

echo '<!-- Using child theme bbPress template: two-column layout -->';

do_action( 'bbp_before_main_content' );
do_action( 'bbp_template_notices' );
?>

<div id="ccrm-community-tabs">
    <button class="ccrm-tab-btn active" data-tab="forums">Discussion Forums</button>
    <button class="ccrm-tab-btn" data-tab="volunteer">Volunteer Opportunities</button>
</div>

<div id="ccrm-tab-forums" class="ccrm-tab active">
	<div id="ccrm-community-wrapper">
		<div id="ccrm-community-filters">
			<button class="ccrm-filter-btn active" data-filter="all">All Members</button>
			<button class="ccrm-filter-btn" data-filter="my-groups">My Groups</button>
		</div>

		<!-- LEFT COLUMN: Thread List -->
		<div id="ccrm-community-left">
			<?php bbp_get_template_part( 'content', 'archive-forum' ); ?>
		</div>

		<!-- RIGHT COLUMN: Thread Detail -->
		<div id="ccrm-community-right">
			<div class="ccrm-thread-placeholder">
				<p>Select a thread to view the discussion.</p>
			</div>
		</div>

	</div>
</div>

<div id="ccrm-tab-volunteer" class="ccrm-tab">
    <?php get_template_part( 'volunteer-opportunities' ); ?>
</div>

<?php
do_action( 'bbp_after_main_content' );
get_footer();
