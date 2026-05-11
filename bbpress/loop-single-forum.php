<?php
/**
 * Single Forum Row (CaTNA SPA override)
 */

$forum_id = bbp_get_forum_id();
$topic_count = bbp_get_forum_topic_count( $forum_id );
$reply_count = bbp_get_forum_reply_count( $forum_id );
$last_active = bbp_get_forum_last_active_time( $forum_id );
?>

<div class="ccrm-forum-card"
     data-forum-id="<?php echo esc_attr( $forum_id ); ?>">

    <div class="ccrm-forum-title">
        <?php echo esc_html( bbp_get_forum_title( $forum_id ) ); ?>
    </div>

    <div class="ccrm-forum-meta">
        <span><?php echo $topic_count; ?> topics</span>
        <span><?php echo $reply_count; ?> posts</span>
        <span>Last active: <?php echo $last_active; ?></span>
    </div>

    <button class="ccrm-forum-open-btn"
            data-forum-id="<?php echo esc_attr( $forum_id ); ?>"
            data-forum-url="<?php bbp_forum_permalink(); ?>">
        View Topics →
    </button>

</div>
