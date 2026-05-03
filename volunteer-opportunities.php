<?php
/**
 * Template part: Volunteer Opportunities list for Community page
 */

$args = [
    'post_type'      => 'volunteer_opportunity',
    'posts_per_page' => -1,
];

$query = new WP_Query( $args );

echo '<div class="ccrm-volunteer-list">';

if ( $query->have_posts() ) :

    while ( $query->have_posts() ) : $query->the_post();

        $location = get_post_meta( get_the_ID(), 'ccrm_location', true );
        $deadline = get_post_meta( get_the_ID(), 'ccrm_deadline', true );
        $groups   = get_post_meta( get_the_ID(), 'ccrm_groups', true );
        ?>

        <div class="ccrm-volunteer-card">

            <h3 class="ccrm-volunteer-title"><?php the_title(); ?></h3>

            <div class="ccrm-volunteer-meta">
                <?php if ( $location ) : ?>
                    <p><strong>Location:</strong> <?php echo esc_html( $location ); ?></p>
                <?php endif; ?>

                <?php if ( $deadline ) : ?>
                    <p><strong>Deadline:</strong> <?php echo esc_html( $deadline ); ?></p>
                <?php endif; ?>

                <?php if ( $groups ) : ?>
                    <p><strong>Eligible Groups:</strong> <?php echo esc_html( implode(', ', $groups) ); ?></p>
                <?php endif; ?>
            </div>

            <div class="ccrm-volunteer-excerpt">
                <?php the_excerpt(); ?>
            </div>

        </div>

        <?php
    endwhile;

else :
    echo '<p>No volunteer opportunities available at the moment.</p>';
endif;

echo '</div>';

wp_reset_postdata();
