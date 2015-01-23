<div class="error">
	<p><?php _e( 'WPMovieLibrary-People error: your environment does not meet all of the system requirements listed below.', 'wpmovielibrary-people' ); ?></p>

	<ul>
<?php if ( version_compare( PHP_VERSION, WPMOLYP_REQUIRED_PHP_VERSION, '<=' ) ) : ?>
		<li>
			<strong>PHP <?php echo WPMOLYP_REQUIRED_PHP_VERSION; ?>+</strong>
			<em><?php printf( __( '(You\'re running version %s)', 'wpmovielibrary-people' ), PHP_VERSION ); ?></em>
		</li>
<?php
endif;
if ( version_compare( $wp_version, WPMOLYP_REQUIRED_WP_VERSION, '<=' ) ) :
?>
		<li>
			<strong>WordPress <?php echo WPMOLYP_REQUIRED_WP_VERSION; ?>+</strong>
			<em><?php printf( __( '(You\'re running version %s)', 'wpmovielibrary-people' ), esc_html( $wp_version ) ); ?></em>
		</li>
<?php endif; ?>
	</ul>

	<p><?php _e( 'If you need to upgrade your version of PHP you can ask your hosting company for assistance, and if you need help upgrading WordPress you can refer to <a href="http://codex.wordpress.org/Upgrading_WordPress">the Codex</a>.', 'wpmovielibrary-people' ); ?></p>

	<p><?php _e( 'If you tried activating WPMovieLibrary-People without activating WPMovieLibrary first, you will need to deactivate and reactivate WPMovieLibrary-People for this notice to disapear. <a href="http://wpmovielibrary.com/wpmovielibrary-people/documentation/installation/#requirements">Learn why</a>.', 'wpmovielibrary-people' ); ?></p>

</div>