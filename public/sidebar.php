<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Enlightenment_Framework
 * @subpackage Enlightenment_Theme
**/

$sidebar_id = enlightenment_get_dynamic_sidebar_id();

if ( ! is_active_sidebar( $sidebar_id ) ) {
	return;
}
?>

<?php enlightenment_before_sidebar(); ?>

<div id="secondary" <?php enlightenment_sidebar_class(); ?> <?php enlightenment_sidebar_extra_atts(); ?>>

	<?php enlightenment_before_widgets(); ?>

	<?php dynamic_sidebar( $sidebar_id ); ?>

	<?php enlightenment_after_widgets(); ?>

</div>

<?php enlightenment_after_sidebar(); ?>
