<?php
/**
 * bbPress - Forum Archive (CaTNA SPA version)
 * This template is used INSIDE the Member Profile SPA.
 * It must NOT load the default bbPress archive wrapper.
 */

echo '<!-- Using child theme archive-forum (SPA mode) -->';

?>

<div id="ccrm-community-tabs">
    <button class="ccrm-tab-btn active" data-tab="forums">Discussion Forums</button>
    <button class="ccrm-tab-btn" data-tab="volunteer">Volunteer Opportunities</button>
</div>

<div id="ccrm-tab-forums" class="ccrm-tab active">

    <div id="ccrm-community-wrapper">

        <!-- FILTERS -->
        <div id="ccrm-community-filters">
            <button class="ccrm-filter-btn active" data-filter="all">All Members</button>
            <button class="ccrm-filter-btn" data-filter="my-groups">My Groups</button>
        </div>

        <!-- LEFT COLUMN: Forum List (NO bbPress wrappers) -->
        <div id="ccrm-community-left">
            <?php
                // IMPORTANT: Load ONLY the forum loop, NOT the full archive
                bbp_get_template_part( 'loop', 'forums' );
            ?>
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
