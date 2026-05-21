<?php
function fd_log( $content, $type = 'log', $encode = false ): void {
	if ( $encode ) {
		$content = json_encode( $content );
	}

	$current_date = new DateTime();
	$file         = match ( $type ) {
		'log'       => 'log-' . $current_date->format( 'Y-m-d' ) . '.log',
		'error'     => 'error-log-' . $current_date->format( 'Y-m-d' ) . '.log',
		'api-error' => 'api-error-log-' . $current_date->format( 'Y-m-d' ) . '.log',
	};

	FD_Zlog::log(
		array(
			'text'     => $content,
			'filename' => $file,
		)
	);
}

function fd_echo_dbg( $data ): void {
	echo '<pre>' . var_export( $data, true ) . '</pre><br/><br/>';
}

function fd_load_template( string $template, bool $new_front_page = false ): void {
	load_template( FD_SMR_PLUGIN_DIR_PATH . '/templates/' . ( $new_front_page ? 'new-front-page/' : '' ) . $template . '.php' );
}

function fd_get_page_by_template( string $template ): WP_Post|false {
	$page = new WP_Query(
		array(
			'post_type'      => 'page',
			'meta_query'     => array(
				array(
					'key'     => '_wp_page_template',
					'value'   => $template,
					'compare' => '=',
				),
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 1,
		)
	);

	return $page->have_posts() ? $page->posts[0] : false;
}

function fd_is_active(): bool {
	return get_field( '_fd_smr_active', 'options' ) ?? false;
}
