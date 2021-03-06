<?php
	/*
	* Template name: Full Width
	*/
?>
<?php get_header(); ?>

			<?php if ( have_posts() ) : ?>


				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<div id="content" class="full-width clearfix">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<?php if( has_post_thumbnail() ) : ?>
							<div class="featured">
							<?php the_post_thumbnail('thumb-single-full'); ?>
							<span class="stripe"></span>
							</div> <!-- .featured -->
						<?php endif; ?>

						<div class="content-wrap">
							<h1 class="page-title"><?php the_title(); ?></h1>
							<div class="entry-content">
								<?php the_content(); ?>
								<?php wp_link_pages(); ?> 
							</div><!-- .entry-content -->

							<?php pukka_after_content(); ?>

						</div> <!-- .content-wrap -->
					</article>

					<section>
					<?php comments_template(); ?>
					</section>
					</div><!-- #content -->

				<?php endwhile; ?>

			<?php endif; ?>

<?php get_footer(); ?>