<?php
/**
 * bbPress - Forum Archive (CaTNA SPA simplified version)
 * This template handles the 2-column core layout for the SPA.
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="ccrm-tab-forums" class="ccrm-tab active">
    <div id="ccrm-community-wrapper">

        <div id="ccrm-community-left">
            <div class="ccrm-topics-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #111827;">Discussions</h3>
                <button id="ccrm-new-thread-trigger" class="ccrm-filter-btn active" style="background-color: #008a76; color: #fff; border: none;">+ New Thread</button>
            </div>

            <div class="ccrm-topics-list">
                <?php
                // Force bbPress to pull all global topics directly into the sidebar loop
                if ( bbp_has_topics( array( 'posts_per_page' => 20 ) ) ) :
                    while ( bbp_topics() ) : bbp_the_topic();
                        bbp_get_template_part( 'loop', 'single-topic' );
                    endwhile;
                else :
                    echo '<p>No discussions found.</p>';
                endif;
                ?>
            </div>
        </div>

        <div id="ccrm-community-right">
            <div class="ccrm-thread-placeholder">
                <p>Select a thread to view the discussion.</p>
            </div>
        </div>

    </div>
</div>