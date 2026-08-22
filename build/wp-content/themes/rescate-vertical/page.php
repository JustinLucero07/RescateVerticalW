<?php
/**
 * Default page template (fallback for pages without a section template).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="rv-page-content">
	<div class="rv-shell">
		<?php
		while ( have_posts() ) {
			the_post();
			?>
			<div class="rv-section-head rv-reveal">
				<h1 style="font-size:clamp(28px,4.2vw,42px);"><?php the_title(); ?></h1>
			</div>
			<div class="rv-editor-content rv-reveal d1"><?php the_content(); ?></div>
			<?php
		}
		?>
	</div>
</section>

<?php get_footer(); ?>
