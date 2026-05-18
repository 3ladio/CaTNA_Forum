<?php
/**
 * Single Topic Row (CaTNA SPA Simplified Version)
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ccrm-topic-card" id="bbp-topic-<?php bbp_topic_id(); ?>" data-permalink="<?php bbp_topic_permalink(); ?>">
    
    <div class="ccrm-topic-icon-wrapper">
        <div class="ccrm-topic-circle-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#008a76" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
    </div>

    <div class="ccrm-topic-card-body">
        <div class="ccrm-topic-title">
            <a href="<?php bbp_topic_permalink(); ?>" class="ccrm-load-topic-link">
                <?php bbp_topic_title(); ?>
            </a>
        </div>
        
        <div class="ccrm-topic-meta">
            <span class="ccrm-topic-author">
                by <?php bbp_topic_author_display_name(); ?>
            </span>
        </div>

        <div class="ccrm-topic-card-footer">
            <span class="ccrm-stat-replies"><?php bbp_topic_reply_count(); ?> replies</span>
            <span class="ccrm-stat-divider">·</span>
            <span class="ccrm-stat-date">Last active: <?php bbp_topic_last_active_time(); ?></span>
        </div>
    </div>

</div>