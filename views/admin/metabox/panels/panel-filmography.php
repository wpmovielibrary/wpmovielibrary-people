
		<div id="wpmoly-filmography" class="wpmoly-filmography">

			<div id="wpmoly-filmography-cast" class="wpmoly-filmography-cast">
				<h3><?php _e( 'Cast', 'wpmovielibrary-people' ) ?></h3>
				<div id="wpmoly-filmography-cast-list">
				</div>
			</div>

			<div id="wpmoly-filmography-crew" class="wpmoly-filmography-crew">
				<h3><?php _e( 'Crew', 'wpmovielibrary-people' ) ?></h3>
				<div id="wpmoly-filmography-crew-list">
				</div>
			</div>

<?php /*foreach ( $details as $slug => $detail ) :
	$slug = str_replace( 'movie_', '', $slug );
?>
			<div id="wpmoly-details-<?php echo $slug ?>" class="wpmoly-details-item wpmoly-details-<?php echo $slug ?>">
				<h4 class="wpmoly-details-item-title"><span class="<?php echo $detail['icon'] ?>"></span>&nbsp; <?php echo $detail['title'] ?></h4>
				<div class="redux-field-init redux-field-container redux-field redux-container-select">
					<?php echo $detail['html'] ?>
				</div>
			</div>

<?php endforeach;*/ ?>
		</div>
