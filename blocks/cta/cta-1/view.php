<?php
	// Content Animation
	$content_animation = ( $settings['content_animation'] ) ? 'uk-scrollspy="cls:uk-animation-' . $settings['content_animation'] . ';"' : '';

	// Button Animation
	$button_animation = ( $settings['button_animation'] ) ? 'uk-scrollspy="cls:uk-animation-' . $settings['button_animation'] . '"' : '';
?>
<section id="<?php echo $id; ?>" class="fp-section cta cta-1 uk-padding-small">
	<div class="uk-section">
		<div class="uk-container">
			<div class="uk-grid-large" uk-grid>
				<div class="uk-width-expand@m">
					<div class="" <?php echo $content_animation; ?>>
						<!-- Title -->
						<?php if ( $contents['title'] ) : ?>
							<?php 
								echo op_heading(
									$contents['title'],
									$settings['heading_type'],
									'uk-heading-primary  uk-text-'.$settings['title_transformation'],
									$content_animation
								); 
							?>
						<?php endif; ?>

						<!-- Description -->
						<?php if ( $contents['description'] ) : ?>
							<p class="uk-text-lead">
								<?php echo $contents['description']; ?>	
							</p>
						<?php endif; ?>
					</div>

				</div> <!-- width-expand -->
				<!-- Link -->
				<div class="uk-width-1-4@m uk-flex uk-flex-middle" <?php echo $button_animation; ?>>
					<?php echo op_link( $contents['link'], 'uk-button uk-button-primary uk-button-large' ); ?>
				</div> <!-- end-link -->
			</div> <!-- uk-grid-large -->
		</div> <!-- uk-container -->
	</div> <!-- uk-section -->
</section> <!-- end-section -->
