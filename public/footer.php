<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

?>
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php enlightenment_after_site_content(); ?>

	</div><!-- #content -->

	<?php enlightenment_before_site_footer(); ?>

	<footer id="colophon" <?php enlightenment_site_footer_class(); ?> <?php enlightenment_site_footer_extra_atts(); ?>>

		<?php enlightenment_site_footer(); ?>

	</footer><!-- #colophon -->

	<?php enlightenment_after_site_footer(); ?>

</div><!-- #page -->

<?php enlightenment_after_site(); ?>

<?php wp_footer(); ?>

</body>
</html>
