<?php
/**
 * Plugin Name: House and Land
 * Description: Allows users to search House and Land packages via AJAX.
 * Version: 1.1.0
 * Author: Coopso
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

/**
 * Enqueue frontend script with AJAX parameters.
 */
add_action( 'wp_enqueue_scripts', 'handl_enqueue_scripts' );
function handl_enqueue_scripts() {
	wp_enqueue_script(
		'handl-ajax',
		plugins_url( 'script.js', __FILE__ ),
		array( 'jquery' ),
		'1.1.0',
		true
	);

	wp_localize_script(
		'handl-ajax',
		'handl',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'handl_nonce' ),
		)
	);
}

/**
 * AJAX hook for both logged in and guest users.
 */
add_action( 'wp_ajax_nopriv_house_and_land_post', 'house_and_land_post' );
add_action( 'wp_ajax_house_and_land_post', 'house_and_land_post' );

/**
 * Handle AJAX request for house and land search.
 */
function house_and_land_post() {
	check_ajax_referer( 'handl_nonce', 'nonce' );

	// Sanitize and parse request parameters
	$price_range       = isset( $_GET['price'] ) ? array_map( 'floatval', explode( ',', sanitize_text_field( $_GET['price'] ) ) ) : [ 0, 700000 ];
	$lot_width_range   = isset( $_GET['lotwidth'] ) ? array_map( 'floatval', explode( ',', sanitize_text_field( $_GET['lotwidth'] ) ) ) : [ 0, 100 ];
	$bedroom_range     = isset( $_GET['bedroomrange'] ) ? array_map( 'intval', explode( ',', sanitize_text_field( $_GET['bedroomrange'] ) ) ) : [ 0, 10 ];
	$bathroom_range    = isset( $_GET['bathroomrange'] ) ? array_map( 'intval', explode( ',', sanitize_text_field( $_GET['bathroomrange'] ) ) ) : [ 0, 10 ];
	$orderby           = sanitize_text_field( $_GET['orderby'] ?? 'meta_value_num' );
	$order             = strtoupper( sanitize_text_field( $_GET['order'] ?? 'ASC' ) );
	$paged             = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;

	// Query arguments
	$args = array(
		'post_type'      => 'house_and_land',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'paged'          => $paged,
		'orderby'        => $orderby,
		'order'          => in_array( $order, [ 'ASC', 'DESC' ], true ) ? $order : 'ASC',
		'meta_key'       => 'price',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'price',
				'value'   => array( $price_range[0], $price_range[1] ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key'     => 'lot_width',
				'value'   => array( $lot_width_range[0], $lot_width_range[1] ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key'     => 'bedroom',
				'value'   => array( $bedroom_range[0], $bedroom_range[1] ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key'     => 'bathroom',
				'value'   => array( $bathroom_range[0], $bathroom_range[1] ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
		),
	);

	$query = new WP_Query( $args );

	ob_start();
	?>
	<div class="results">
		<div class="results-list">
			<div class="grid-container">
				<div class="row">
					<div class="col-12 search_result">
						<div class="search_result_title">Search Results</div>
						<div class="user_lot_result">
							<?php
							printf(
								'Price Range: <strong>$%s - $%s</strong>, Lot Width: <strong>%sm - %sm</strong>, Bedroom: <strong>%s-%s</strong>, Bathroom: <strong>%s-%s</strong>',
								number_format_i18n( $price_range[0] ),
								number_format_i18n( $price_range[1] ),
								esc_html( $lot_width_range[0] ),
								esc_html( $lot_width_range[1] ),
								esc_html( $bedroom_range[0] ),
								esc_html( $bedroom_range[1] ),
								esc_html( $bathroom_range[0] ),
								esc_html( $bathroom_range[1] )
							);
							?>
						</div>
					</div>

					<?php if ( $query->have_posts() ) : ?>
						<?php while ( $query->have_posts() ) : $query->the_post(); ?>
							<?php
							$thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
							$price = get_field( 'price' );
							$price = $price ? number_format_i18n( $price ) : '';
							$lot_text = get_field( 'lot_text' );
							?>
							<div class="col-4 results-list-view">
								<div class="results-list-item grid">
									<a href="<?php echo esc_url( get_field( 'brochure' ) ); ?>" target="_blank" rel="noopener">
										<div class="results-list-item-image" style="background-image: url('<?php echo esc_url( $thumb ); ?>')"></div>
									</a>

									<div class="results-list-item-desc equal">
										<div class="results-list-item-desc-head house text-center">
											<div class="title"><?php the_title(); ?></div>
											<div class="price">
												<span class="from">From</span> $<?php echo esc_html( $price ); ?>
											</div>
										</div>

										<?php if ( $lot_text ) : ?>
											<div class="results-list-item-desc-lot">
												<div class="results-list-item-desc-lot-text text-center">
													<?php echo esc_html( $lot_text ); ?>
												</div>
											</div>
										<?php endif; ?>

										<div class="results-list-item-desc-icons text-center">
											<div class="cell">
												<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-4.png' ); ?>" alt="Bed">
												<div class="cell-label"><?php echo esc_html( get_field( 'bedroom' ) ); ?> Bed</div>
											</div>
											<div class="cell">
												<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-5.png' ); ?>" alt="Bath">
												<div class="cell-label"><?php echo esc_html( get_field( 'bathroom' ) ); ?> Bath</div>
											</div>
											<div class="cell">
												<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-3.png' ); ?>" alt="Car">
												<div class="cell-label"><?php echo esc_html( get_field( 'garage' ) ); ?> Car</div>
											</div>
										</div>

										<div class="house_buttons">
											<div class="find_land">
												<a href="<?php echo esc_url( get_permalink( 94 ) . '?lotwidth=0,' . get_field( 'lot_width' ) ); ?>">
													<button class="button">
														<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/button_home.png' ); ?>" alt=""> 
														Find Land <i class="fa fa-arrow-right" aria-hidden="true"></i>
													</button>
												</a>
											</div>
											<div class="enquire">
												<a href="#" data-content="<?php echo esc_attr( $lot_text ? 'Lot: ' . $lot_text . ' House: ' . get_the_title() : get_the_title() ); ?>" class="js-open-enquire button">
													<i class="fa fa-commenting-o" aria-hidden="true"></i> Enquire <i class="fa fa-arrow-right" aria-hidden="true"></i>
												</a>
											</div>
										</div>

										<?php if ( get_field( 'builder_logo' ) ) : ?>
											<div class="results-list-item-desc-links">
												<img src="<?php echo esc_url( get_field( 'builder_logo' ) ); ?>" alt="Builder Logo">
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>

					<?php else : ?>
						<h2 class="no-match">Nothing Found — Sorry, no posts matched your criteria.</h2>
					<?php endif; ?>

				</div>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
	<?php
	wp_send_json_success( ob_get_clean() );
}
