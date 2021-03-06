<?php
/**
 * Partial template to display related posts for the current single entry.
 *
 * @since 1.0.0
 */

if( 'post' != get_post_type() ) {
	return;
}

$key = 'setting-relationship_taxonomy';
// Either 'tag' or 'category'. Used later in query.
$taxonomy_type = 'tag' == themify_get( $key ) ? 'tag' : 'category';
// Number of entries to display
$number_of_entries = themify_check( $key.'_entries' ) ? themify_get( $key.'_entries' ) : 3;

// Set taxonomy for the corresponding post type. Defaults to category/{post_type}-category.
if ( 'post' == get_post_type() ) {
	$taxonomy = 'tag' == $taxonomy_type ? 'post_tag' : 'category';
} else {
	$taxonomy = 'tag' == $taxonomy_type ? get_post_type() . 'tag' : get_post_type() . 'category';
}
$saved_entry = get_post();
$terms       = wp_get_post_terms( get_the_ID(), $taxonomy );
$term_ids    = array();

if ( $terms ) {
	for ( $i = 0; $i < count( $terms ); $i ++ ) {
		$term_ids[] = $terms[$i]->term_id;
	}
	$related = new WP_Query( array(
		$taxonomy_type . '__in' => $term_ids,
		'post__not_in'          => array_merge( array( get_the_ID() ), get_option( 'sticky_posts' ) ),
		'posts_per_page'        => $number_of_entries,
		'ignore_sticky_posts '  => true,
	) );
	if ( $related->have_posts() ) : ?>
		<div class="related-posts">
			<h4 class="related-title"><?php _e( 'Related Posts', 'themify' ); ?></h4>
			<?php while ( $related->have_posts() ) : $related->the_post(); ?>
				<article class="post type-post clearfix">

					<?php if ( ! themify_check( $key . '_hide_image' ) && has_post_thumbnail() ) : ?>
						<?php if ( $post_image = themify_get_image( 'setting=image_post_single&w=255&h=155&ignore=true' ) ) : ?>
							<figure class="post-image clearfix">
								<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
							</figure>
						<?php endif; // if there's a featured image?>
					<?php endif; ?>

					<div class="post-content">
						<p class="post-meta">
							<?php the_terms( get_the_ID(), 'post' != get_post_type() ? get_post_type() . '-category' : 'category', ' <span class="post-category">', ', ', '/</span>' ); ?>
						</p>
						<h4 class="post-title">
							<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
								<?php the_title(); ?>
							</a>
						</h4>
						<?php the_excerpt(); ?>
					</div>
					<!-- /.post-content -->

					<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

				</article>
			<?php endwhile; ?>
		</div>
		<!-- /.related-posts -->
	<?php endif;
}

$post = $saved_entry;
wp_reset_query();
?>