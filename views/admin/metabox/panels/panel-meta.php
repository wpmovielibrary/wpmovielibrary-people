

					<div id="wpmoly-meta-search" class="wpmoly-meta-search">

						<div id="wpmoly-people-meta-search" class="wpmoly-people-meta-search">

							<?php wpmoly_nonce_field( 'empty-people-meta' ) ?>
							<?php wpmoly_nonce_field( 'save-people-meta' ) ?>
							<?php wpmoly_nonce_field( 'search-people' ) ?>

							<p><strong><?php _e( 'Find people on TMDb:', 'wpmovielibrary-people' ); ?></strong></p>

							<div>
								<select id="wpmoly-search-lang">
<?php foreach ( $languages as $code => $lang ) : ?>
									<option value="<?php echo $code ?>" <?php selected( wpmoly_o( 'api-language' ), $code ); ?>><?php echo $lang ?></option>
<?php endforeach; ?>
								</select>
								<input id="wpmoly-search-query" type="text" value="" size="30" maxlength="32" placeholder="<?php _e( 'ex: Matthew McConaughey', 'wpmovielibrary' ); ?>" />
								<a id="wpmoly-search" title="<?php _e( 'Search', 'wpmovielibrary' ); ?>" href="#" class="button button-secondary button-icon"><span class="wpmolicon icon-search"></span></a>
								<a id="wpmoly-update" title="<?php _e( 'Update', 'wpmovielibrary' ); ?>" href="#" class="button button-secondary button-icon"><span class="wpmolicon icon-update"></span></a>
								<span id="wpmoly-meta-search-spinner"><span class="spinner"></span></span>
								<a id="wpmoly-empty" title="<?php _e( 'Empty Results', 'wpmovielibrary' ); ?>" href="#" class="button button-secondary button-empty button-icon hide-if-no-js"><span class="wpmolicon icon-erase"></span></a>
							</div>

							<div id="wpmoly_status"></div>

							<div id="wpmoly-meta-search-results"></div>

						</div>

						<div id="wpmoly-people-meta" class="wpmoly-meta">
<?php 
foreach ( $metas as $slug => $meta ) :
	$value = '';
	if ( ! $empty && isset( $metadata[ $slug ] ) )
		$value = apply_filters( 'wpmoly_stringify_array', $metadata[ $slug ] );
?>
							<div class="wpmoly-meta-edit wpmoly-meta-edit-<?php echo $slug; ?> <?php echo $meta['size'] ?>">
								<div class="wpmoly-meta-label">
									<label for="meta_data_<?php echo $slug; ?>"><?php _e( $meta['title'], 'wpmovielibrary' ) ?></label>
								</div>
								<div class="wpmoly-meta-value">
<?php if ( 'textarea' == $meta['type'] ) : ?>
									<textarea id="meta_data_<?php echo $slug; ?>" name="wpmoly[meta][<?php echo $slug; ?>]" class="meta-data-field" rows="6"><?php echo $value ?></textarea>
<?php elseif ( in_array( $meta['type'], array( 'text', 'hidden' ) ) ) : ?>
									<input type="<?php echo $meta['type']; ?>" id="meta_data_<?php echo $slug; ?>" name="wpmoly[meta][<?php echo $slug; ?>]" class="meta-data-field" value="<?php echo $value ?>" />
<?php endif; ?>
								</div>
							</div>
<?php endforeach; ?>

						</div>

					</div>
