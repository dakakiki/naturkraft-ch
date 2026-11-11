<?php
/**
 * layout_hero.php
 */

$nk_anchor   = nk_anchor( get_sub_field( 'anchor' ) );
$nk_title    = (string) get_sub_field( 'title' );
$nk_subtitle = (string) get_sub_field( 'subtitle' );
$nk_text     = (string) get_sub_field( 'text' );
$nk_buttons  = (array) get_sub_field( 'buttons' );
$nk_image    = get_sub_field( 'image' );
$nk_ticker   = trim( (string) get_sub_field( 'ticker' ) );
?>

<section class="hero"<?= $nk_anchor ? ' id="' . esc_attr( $nk_anchor ) . '"' : ''; ?> data-animate-now>

	<div class="hero__stage">

		<?php if ( $nk_image ) : ?>
			<div class="hero__media">
				<?= nk_image( $nk_image, 'full', array(
					'class'         => 'hero__img',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
					'sizes'         => '100vw',
				) ); ?>
			</div>
		<?php endif; ?>

		<div class="hero__inner">

			<div class="hero__card nk-glass group-fade-up">

				<?php if ( $nk_title ) : ?>
					<h1 class="hero__title fade-up"><?= nk_lines( $nk_title ); ?></h1>
				<?php endif; ?>

				<?php if ( $nk_subtitle ) : ?>
					<p class="hero__subtitle fade-up"><?= nk_lines( $nk_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $nk_text ) : ?>
					<p class="hero__text fade-up"><?= nk_lines( $nk_text ); ?></p>
				<?php endif; ?>

				<?php if ( $nk_buttons ) : ?>
					<div class="hero__buttons fade-up">
						<?php foreach ( $nk_buttons as $nk_button ) : ?>
							<?= nk_link( $nk_button['link'] ?? null, 'nk-btn' ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div>

		</div>

	</div>

	<?php if ( '' !== $nk_ticker ) : ?>
		<div class="ticker">
			<p class="screen-reader-text"><?= esc_html( $nk_ticker ); ?></p>
			<div class="ticker__track" aria-hidden="true">
				<?php for ( $nk_half = 0; $nk_half < 2; $nk_half++ ) : ?>
					<div class="ticker__group">
						<?php for ( $nk_i = 0; $nk_i < 8; $nk_i++ ) : ?>
							<span class="ticker__item"><?= esc_html( $nk_ticker ); ?></span>
						<?php endfor; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>

</section>
