<?php if ( bbp_has_topics() ) : ?>

<div class="ccrm-topic-cards">

    <?php while ( bbp_topics() ) : bbp_the_topic(); ?>

        <div class="ccrm-topic-card" data-groups="<?php echo esc_attr( implode(',', (array) $groups ) ); ?>">

            <div class="ccrm-topic-card-header">
                <h3 class="ccrm-topic-title">
                    <a href="<?php bbp_topic_permalink(); ?>">
                        <?php bbp_topic_title(); ?>
                    </a>
                </h3>

                <div class="ccrm-topic-meta">
                    <span class="ccrm-topic-author">
                        <?php echo get_avatar( bbp_get_topic_author_id(), 40 ); ?>
                        <?php bbp_topic_author_display_name(); ?>
                    </span>

                    <span class="ccrm-topic-group">
                        <?php
                        // Example: show your CRM group name
                        $groups = get_user_meta( bbp_get_topic_author_id(), 'ccrm_groups', true );
                        echo $groups ? esc_html( implode(', ', (array) $groups ) ) : '—';
                        ?>
                    </span>
                </div>
            </div>

            <div class="ccrm-topic-card-footer">
                <span class="ccrm-topic-replies">
                    <?php bbp_topic_reply_count(); ?> replies
                </span>

                <span class="ccrm-topic-last-active">
                    Last reply: <?php bbp_topic_last_active_time(); ?>
                </span>
            </div>

        </div>

    <?php endwhile; ?>

</div>

<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

<?php endif; ?>
