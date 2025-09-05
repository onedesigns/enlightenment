<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	enlightenment_comments_require_password();
	return;
}

?>

<?php enlightenment_before_comments(); ?>

<?php if ( have_comments() ) : ?>

	<section id="comments" class="comments-area">

		<?php enlightenment_comments(); ?>

	</section>

<?php else : ?>

	<?php enlightenment_no_comments(); ?>

<?php endif; ?>

<?php enlightenment_after_comments(); ?>
