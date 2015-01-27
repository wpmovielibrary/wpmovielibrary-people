
		<div id="wpmoly-filmography" class="wpmoly-filmography">

			<div id="wpmoly-filmography-cast" class="wpmoly-filmography-cast">
				<h3><?php _e( 'Cast', 'wpmovielibrary-people' ) ?></h3>
				<textarea id="wpmoly-filmography-cast-data" style="display:none;"><?php echo $json['cast'] ?></textarea>
				<div id="wpmoly-filmography-cast-container">
					<table class="wp-list-table widefat fixed posts">
						<thead>
							<tr>
								<th scope="col" id="wpmoly-filmography-cast-item-poster" class="manage-column"><span class="wpmolicon icon-poster" title="<?php _e( 'Poster', 'wpmovielibrary' ) ?>"></span></th>
								<th scope="col" id="wpmoly-filmography-cast-item-title" class="manage-column"><?php _e( 'Title' ) ?></th>
								<th scope="col" id="wpmoly-filmography-cast-item-character" class="manage-column"><?php _e( 'Character', 'wpmovielibrary-people' ) ?></th>
								<th scope="col" id="wpmoly-filmography-cast-item-date" class="manage-column"><span class="wpmolicon icon-date" title="<?php _e( 'Release Date', 'wpmovielibrary' ) ?>"></span></th>
							</tr>
						</thead>

						<tbody id="wpmoly-filmography-cast-list">
<?php
if ( ! empty( $cast ) ) :
	$i = 0;
	foreach ( $cast as $role ) :
		$role['date'] = date( 'Y', strtotime( $role['release_date'] ) );
		if ( '' == $role['character'] )
			$role['character'] = __( 'Unknown' );

		if ( isset( $role['post'] ) ) {
			$poster = wp_get_attachment_image_src( get_post_thumbnail_id( $role['post']->ID ), 'thumbnail' );
			$poster = $poster[0];
			$poster_size = '';
			$title  = sprintf( '<a href="%s" title="%s">%s</a>', get_permalink( $role['post']->ID ), $role['original_title'], $role['post']->post_title );
		} else {
			$poster = $url . $role['poster_path'];
			$poster_size = 'thumb';
			$title  = sprintf( '<span title="%s">%s</span>', $role['original_title'], $role['title'] );
		}
?>
							<tr id="post-<?php echo $role['id'] ?>" class="post-<?php echo $role['id'] ?> type-movie status-publish hentry<?php if ( $i % 2 ) echo ' alternate' ?> iedit author-self level-0">
								<th scope="col" id="wpmoly-filmography-cast-item-<?php echo $role['id'] ?>-poster" class="manage-column wpmoly-filmography-cast-item-poster"><div class="wpmoly-filmography-cast-item-poster-container"><img class="<?php echo $poster_size ?>" src="<?php echo $poster ?>" alt="" /></div></th>
								<th scope="col" id="wpmoly-filmography-cast-item-<?php echo $role['id'] ?>-title" class="manage-column wpmoly-filmography-cast-item-title"><?php echo $title ?></th>
								<th scope="col" id="wpmoly-filmography-cast-item-<?php echo $role['id'] ?>-character" class="manage-column wpmoly-filmography-cast-item-character"><?php echo __( 'as' ) . ' ' . $role['character'] ?></th>
								<th scope="col" id="wpmoly-filmography-cast-item-<?php echo $role['id'] ?>-date" class="manage-column wpmoly-filmography-cast-item-date"><span title="<?php echo $role['release_date'] ?>"><?php echo $role['date'] ?></span></th>
							</tr>

<?php
		$i++;
	endforeach;
endif;
?>
						</tbody>
					</table>
				</div>
			</div>

			<div id="wpmoly-filmography-crew" class="wpmoly-filmography-crew">
				<h3><?php _e( 'Crew', 'wpmovielibrary-people' ) ?></h3>
				<textarea id="wpmoly-filmography-crew-data" style="display:none;"><?php echo $json['crew'] ?></textarea>
				<div id="wpmoly-filmography-crew-container">
					<table class="wp-list-table widefat fixed posts">
						<thead>
							<tr>
								<th scope="col" id="wpmoly-filmography-crew-item-poster" class="manage-column"><span class="wpmolicon icon-poster" title="<?php _e( 'Poster', 'wpmovielibrary' ) ?>"></span></th>
								<th scope="col" id="wpmoly-filmography-crew-item-title" class="manage-column"><?php _e( 'Title' ) ?></th>
								<th scope="col" id="wpmoly-filmography-crew-item-character" class="manage-column"><?php _e( 'Character', 'wpmovielibrary-people' ) ?></th>
								<th scope="col" id="wpmoly-filmography-crew-item-date" class="manage-column"><span class="wpmolicon icon-date" title="<?php _e( 'Release Date', 'wpmovielibrary' ) ?>"></span></th>
							</tr>
						</thead>

						<tbody id="wpmoly-filmography-crew-list">
<?php
if ( ! empty( $crew ) ) :
	$i = 0;
	foreach ( $crew as $job ) :
		$job['date'] = date( 'Y', strtotime( $job['release_date'] ) );
		if ( '' == $job['job'] )
			$job['job'] = __( 'Unknown' );

		if ( isset( $job['post'] ) ) {
			$poster = wp_get_attachment_image_src( get_post_thumbnail_id( $job['post']->ID ), 'thumbnail' );
			$poster = $poster[0];
			$poster_size = '';
			$title  = sprintf( '<a href="%s" title="%s">%s</a>', get_permalink( $job['post']->ID ), $job['original_title'], $job['post']->post_title );
		} else {
			$poster = $url . $job['poster_path'];
			$poster_size = 'thumb';
			$title  = sprintf( '<span title="%s">%s</span>', $job['original_title'], $job['title'] );
		}
?>
							<tr id="post-<?php echo $role['id'] ?>" class="post-<?php echo $role['id'] ?> type-movie status-publish hentry<?php if ( $i % 2 ) echo ' alternate' ?> iedit author-self level-0">
								<th scope="col" id="wpmoly-filmography-crew-item-<?php echo $role['id'] ?>-poster" class="manage-column wpmoly-filmography-crew-item-poster"><div class="wpmoly-filmography-crew-item-poster-container"><img class="<?php echo $poster_size ?>" src="<?php echo $poster ?>" alt="" /></div></th>
								<th scope="col" id="wpmoly-filmography-crew-item-<?php echo $role['id'] ?>-title" class="manage-column wpmoly-filmography-crew-item-title"><?php echo $title ?></th>
								<th scope="col" id="wpmoly-filmography-crew-item-<?php echo $role['id'] ?>-job" class="manage-column wpmoly-filmography-crew-item-job"><?php echo $role['job'] ?></th>
								<th scope="col" id="wpmoly-filmography-crew-item-<?php echo $role['id'] ?>-date" class="manage-column wpmoly-filmography-crew-item-date"><span title="<?php echo $role['release_date'] ?>"><?php echo $role['date'] ?></span></th>
							</tr>
<?php
		$i++;
	endforeach;
endif;
?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
