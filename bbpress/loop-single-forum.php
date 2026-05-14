<?php
/**
 * Single Forum Row (CaTNA SPA)
 */

defined( 'ABSPATH' ) || exit;
?>

<li id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>

    <div class="ccrm-forum-card">
        <div class="ccrm-forum-info">
            <h3 class="ccrm-forum-title"><?php bbp_forum_title(); ?></h3>
            <p class="ccrm-forum-description"><?php bbp_forum_content(); ?></p>
        </div>

        <div class="ccrm-forum-stats">
            <span class="ccrm-forum-topics"><?php bbp_forum_topic_count(); ?> topics</span>
            <span class="ccrm-forum-posts"><?php bbp_forum_reply_count(); ?> posts</span>
            <span class="ccrm-forum-freshness"><?php bbp_forum_last_active_time(); ?></span>
        </div>

        <div class="ccrm-forum-actions">
            <button
                class="ccrm-forum-open-btn"
                data-forum-url="<?php bbp_forum_permalink(); ?>"
                type="button"
            >
                View Topics →
            </button>
        </div>
    </div>

</li>
