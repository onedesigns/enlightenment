<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
 */

?><!doctype html>
<html <?php enlightenment_html_atts(); ?>>
<head>
	<?php enlightenment_head(); ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class() ?> <?php enlightenment_body_extra_atts(); ?>>
<?php wp_body_open(); ?>

<?php enlightenment_before_site(); ?>

<div id="page" class="site">

	<?php enlightenment_before_site_header(); ?>

	<header id="masthead" <?php enlightenment_site_header_class(); ?> <?php enlightenment_site_header_extra_atts(); ?>>

		<?php enlightenment_site_header(); ?>

	</header><!-- #masthead -->

	<?php enlightenment_after_site_header(); ?>

	<div id="content" class="site-content">

		<?php
		if ( has_header_image() ) :
			enlightenment_before_page_header();
			?>

			<header class="page-header">
				<?php enlightenment_page_header(); ?>
			</header>

			<?php
			enlightenment_after_page_header();
		endif;

		enlightenment_before_site_content();
		?>

		<div id="primary" class="content-area">
			<main id="main" <?php enlightenment_page_content_class(); ?> <?php enlightenment_page_content_extra_atts(); ?>>

				<?php
				if ( ! has_header_image() ) :
					enlightenment_before_page_header();
					?>

					<header class="page-header">
						<?php enlightenment_page_header(); ?>
					</header>

					<?php
					enlightenment_after_page_header();
				endif;
