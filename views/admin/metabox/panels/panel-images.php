
		<div id="wpmoly-images" class="wpmoly-images">

			<div class="no-js-alert hide-if-js"><?php _e( 'It seems you have JavaScript deactivated; the import feature will not work correctly without it, please check your browser\'s settings.', 'wpmovielibrary' ); ?></div>

			<?php echo wpmoly_nonce_field( 'upload-movie-image', $referer = false ); ?>
			<?php echo wpmoly_nonce_field( 'load-movie-images', $referer = false ); ?>
			<!--<input type="hidden" id="wp-version" value="<?php echo $version ?>" />-->

			<div id="wpmoly-backdrops-preview" class="hide-if-no-js">
				<textarea id="wpmoly-imported-backdrops-json" style="display:none"><?php echo $data ?></textarea>
				<ul id="wpmoly-imported-backdrops" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1">

<?php foreach ( $images as $image ) : ?>
					<li class="wpmoly-backdrop wpmoly-imported-backdrop">
						<a class="open-editor" href="<?php echo $image['sizes']['medium']['url'] ?>" data-id="<?php echo $image['id'] ?>">
							<div class="js--select-attachment type-image <?php echo $image['type'] . ' ' . $image['orientation'] ?>">
								<div class="thumbnail">
									<div class="centered"><img src="<?php echo $image['image'][0] ?>" draggable="false" alt="<?php echo $image['title'] ?>" alt="<?php echo $image['alt'] ?>"></div>
								</div>
							</div>
						</a>
					</li>

<?php endforeach; ?>

					<li class="wpmoly-backdrop wpmoly-imported-backdrop"><a href="#" id="wpmoly-load-backdrops"><?php _e( 'Load Images', 'wpmovielibrary' ); ?></a></li>

				</ul>
			</div>
			<div style="clear:both"></div>

		</div>
