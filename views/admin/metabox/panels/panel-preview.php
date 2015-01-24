
<?php if ( $empty ) : ?>
					<div id="wpmoly-people-preview-message" class="wpmoly-meta-preview-message">
						<p><em><?php _e( 'Nothing to preview yet!', 'wpmovielibrary' ) ?></em></p>
					</div>
<?php endif; ?>

					<div id="wpmoly-people-preview" class="wpmoly-meta-preview<?php if ( $empty ) echo ' empty' ?>">
						<div id="wpmoly-people-preview-poster" class="wpmoly-meta-preview-poster">
							<?php echo $thumbnail ?>
						</div>
						<h3 id="wpmoly-people-preview-name"><?php echo $preview['name']; ?></h3>
						<h5 id="wpmoly-people-preview-jobs"><?php echo $preview['jobs']; ?></h5>
						<p>
							<span id="wpmoly-people-preview-age"><?php echo $preview['age']; ?></span> − <span id="wpmoly-movie-preview-birthplace"><?php printf( __( 'Born in %s', 'wpmovielibrary-people' ), $preview['birthplace'] ); ?></span>
						</p>
						<p id="wpmoly-people-preview-biography">
							<span class="wpmolicon icon-overview"></span>&nbsp; <?php echo $preview['biography']; ?>
						</p>
						<div style="clear:both"></div>
					</div>

