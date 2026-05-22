<?php
class FD_Hooks {
	private static array $routes = array(
		'sammanfattning' => 'sammanfattning.php',
		'accept-payment' => 'accept-payment.php',
		'feedback'       => 'feedback.php',
	);

	public static function init(): void {
		add_action( 'after_setup_theme', array( self::class, 'after_setup_theme' ) );
		add_action( 'plugins_loaded', array( FD_Updater::class, 'init' ) );

		add_filter( 'acf/settings/save_json/key=group_669d0cfc749ad', array( self::class, 'acf_save_json' ) );
		add_filter( 'acf/settings/save_json/key=group_66d0c11be5472', array( self::class, 'acf_save_json' ) );
		add_filter( 'acf/settings/save_json/key=group_6903363426423', array( self::class, 'acf_save_json' ) );
		add_filter( 'acf/settings/load_json', array( self::class, 'acf_json_load' ) );

		if ( fd_is_active() ) {
			add_filter( 'frontpage_template', array( self::class, 'apply_front_page_template' ) );
			add_filter( 'pre_do_shortcode_tag', array( self::class, 'replace_old_form_shortcode' ), accepted_args: 4 );
			add_filter( 'query_vars', array( self::class, 'add_query_var' ) );
			add_filter( 'template_include', array( self::class, 'template_loader' ) );

			add_action( 'init', array( self::class, 'add_rewrites' ) );
			add_action( 'rest_api_init', array( self::class, 'rest_api_register_routes' ) );
			add_action( 'wp_enqueue_scripts', array( self::class, 'fd_enqueue' ), PHP_INT_MAX - 1 );
			add_action( 'wp_print_footer_scripts', array( self::class, 'print_footer_scripts' ) );
		}
	}

	public static function add_rewrites(): void {
		foreach ( array_keys( self::$routes ) as $slug ) {
			add_rewrite_rule( "^{$slug}/?$", 'index.php?fd_route=' . $slug, 'top' );
		}

		flush_rewrite_rules();
	}

	public static function add_query_var( array $vars ): array {
		$vars[] = 'fd_route';

		return $vars;
	}

	public static function template_loader( string $template ): string {
		global $wp_query;

		$route = get_query_var( 'fd_route' );

		if ( ! $route || ! isset( self::$routes[ $route ] ) ) {
			return $template;
		}

		$wp_query->is_page     = true;
		$wp_query->is_singular = true;
		$wp_query->is_home     = false;
		$wp_query->is_404      = false;

		$plugin_template = FD_SMR_PLUGIN_DIR_PATH . '/pages/' . self::$routes[ $route ];

		return file_exists( $plugin_template ) ? $plugin_template : $template;
	}

	public static function after_setup_theme(): void {
		self::add_image_sizes();
		self::add_plugin_settings();
		self::register_post_types();
	}

	public static function acf_save_json(): string {
		return FD_SMR_PLUGIN_DIR_PATH . '/acf-json';
	}

	public static function acf_json_load( $paths ): array {
		unset( $paths[0] );
		$paths[] = FD_SMR_PLUGIN_DIR_PATH . '/acf-json';

		return $paths;
	}

	public static function rest_api_register_routes(): void {
		$controller = new FD_Request_Rest_API();
		$controller->register_routes();
	}

	private static function add_image_sizes(): void {
		add_image_size( 'fd-smr-home-page-banner-desktop', 2560, 9999, true );
		add_image_size( 'fd-smr-home-page-banner-mobile', 1024, 9999, true );
	}

	private static function register_post_types(): void {
		register_post_type(
			'moving-request',
			array(
				'labels'              => array(
					'name'               => 'Moving requests',
					'singular_name'      => 'Moving request',
					'add_new'            => 'Add New',
					'add_new_item'       => 'Add request',
					'edit_item'          => 'Edit request',
					'new_item'           => 'New request',
					'view_item'          => 'View request',
					'search_items'       => 'Search request',
					'not_found'          => 'No requests found',
					'not_found_in_trash' => 'No requests found in trash',
					'parent_item_colon'  => '',
				),
				'singular_label'      => 'Moving request',
				'public'              => false,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'show_in_rest'        => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'supports'            => array(
					'comments',
					'editor',
					'page-attributes',
					'thumbnail',
					'title',
				),
				'menu_icon'           => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gVXBsb2FkZWQgdG86IFNWRyBSZXBvLCB3d3cuc3ZncmVwby5jb20sIEdlbmVyYXRvcjogU1ZHIFJlcG8gTWl4ZXIgVG9vbHMgLS0+DQo8c3ZnIGZpbGw9IiMwMDAwMDAiIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIA0KCSB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCAyNTYgMTgyIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyNTYgMTgyIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxwYXRoIGQ9Ik0xMjgsMTA0djYxaDE5LjkyYzAuMzUtMC4wOCwwLjYyLTAuMzYsMC42OS0wLjdjMS4yMi02Ljk1LDcuMjctMTIuMjQsMTQuNTgtMTIuMjRzMTMuMzYsNS4yOSwxNC41NywxMi4yNA0KCWMwLjA4LDAuNDQsMC44LDAuNywxLjI0LDAuN2gxMHYtNjFIMTI4eiBNMTM0LDQ0LjM5VjVoLTIydjE5LjQyTDg2Ljc4LDEuMzdMMi4wNiw3Ny4yNWwxMi44OCwxNC41MkwyOCw3OS45N1YxODBoMTZ2LTI2aDEwdjhoNnYtOA0KCWgxMHYyNmg5di0yNmgxMHY4aDZ2LThoMTB2MjZoNDB2LTloLTIzVjk4aDIzVjgwLjdsMTIuNjIsMTEuOGwxMi45OS0xNC4xNkwxMzQsNDQuMzl6IE0xMDUsMTQ1SDc5di0yNmgxMHY4aDZ2LThoMTBWMTQ1eiBNMjUzLDE1Nw0KCWgtMnYtMTFjMC0zLjU1LTIuNDEtNy02LTdoLTE2Yy0wLjI0LDAtMC45OC0wLjM4LTEuMTQtMC41NGwtMTcuNDQtMTguODFjLTAuOC0wLjgxLTIuMjUtMS42MS0zLjQyLTEuNjVoLTE0djQ3aDI1DQoJYzAuNDQsMCwwLjctMC4zLDAuNzgtMC43YzEuMjEtNi45NSw3LjI3LTEyLjI0LDE0LjU3LTEyLjI0YzcuMzEsMCwxMy4zNyw1LjI5LDE0LjU4LDEyLjI0YzAuMDgsMC40NCwwLjYzLDAuNywxLjA3LDAuN2gxDQoJYzIuMywwLDQtMS43NCw0LTR2LTNDMjU0LDE1Ny41NiwyNTMuNDgsMTU3LDI1MywxNTd6IE0yMTkuNzUsMTM5LjMxaC0yMS4zNGMtMC40OSwwLTAuODktMC40MS0wLjg5LTAuODl2LTE0LjM3DQoJYzAtMC40OSwwLjQtMC44OSwwLjg5LTAuODloOC4zNGMwLjI0LDAsMC40OCwwLjEyLDAuNjksMC4yOGwxMywxNC4zM0MyMjAuOTIsMTM4LjM4LDIyMC41MiwxMzkuMzEsMjE5Ljc1LDEzOS4zMXogTTIzMy4zNSwxNTUuODYNCgljLTYuMTMsMC0xMS4xLDQuOTctMTEuMSwxMS4xYzAsNi4xNCw0Ljk3LDExLjEsMTEuMSwxMS4xYzYuMTQsMCwxMS4xMS00Ljk2LDExLjExLTExLjFDMjQ0LjQ2LDE2MC44MywyMzkuNDksMTU1Ljg2LDIzMy4zNSwxNTUuODZ6DQoJIE0yMzMuMzUsMTcxLjIxYy0yLjMzLDAtNC4yNS0xLjkyLTQuMjUtNC4yNWMwLTIuMzMsMS45Mi00LjI1LDQuMjUtNC4yNWMyLjM0LDAsNC4yNiwxLjkyLDQuMjYsNC4yNQ0KCUMyMzcuNjEsMTY5LjI5LDIzNS42OSwxNzEuMjEsMjMzLjM1LDE3MS4yMXogTTE2My4xOSwxNTUuODZjLTYuMTQsMC0xMS4xLDQuOTctMTEuMSwxMS4xYzAsNi4xNCw0Ljk2LDExLjEsMTEuMSwxMS4xDQoJczExLjEtNC45NiwxMS4xLTExLjFDMTc0LjI5LDE2MC44MywxNjkuMzMsMTU1Ljg2LDE2My4xOSwxNTUuODZ6IE0xNjMuMTksMTcxLjIxYy0yLjMzLDAtNC4yNS0xLjkyLTQuMjUtNC4yNQ0KCWMwLTIuMzMsMS45Mi00LjI1LDQuMjUtNC4yNXM0LjI1LDEuOTIsNC4yNSw0LjI1QzE2Ny40NCwxNjkuMjksMTY1LjUyLDE3MS4yMSwxNjMuMTksMTcxLjIxeiIvPg0KPC9zdmc+',
			)
		);
	}

	private static function add_plugin_settings(): void {
		acf_add_options_page(
			array(
				'page_title' => __( 'Moving Request Settings', 'fastdev' ),
				'menu_title' => __( 'Moving Request Settings', 'fastdev' ),
				'menu_slug'  => 'fd-smr-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);
	}

	public static function fd_enqueue(): void {
		if ( ! is_admin() & ! is_login() ) {
			wp_enqueue_style(
				'fd-smr-flatpickr',
				'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
			);

			wp_enqueue_style(
				'fd-smr-styles',
				FD_SMR_PLUGIN_URL . 'css/style.css',
				ver: filemtime( FD_SMR_PLUGIN_DIR_PATH . '/css/style.css' )
			);

			wp_enqueue_script(
				'google-maps',
				'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY.'&callback=initService&libraries=places&v=weekly&language=en',
				array( 'jquery' ),
				args: array(
					'strategy'  => 'async',
					'in_footer' => true,
				),
			);

			wp_enqueue_script(
				'fd-smr-flatpickr',
				'https://cdn.jsdelivr.net/npm/flatpickr',
				array( 'jquery' ),
				true
			);

			wp_enqueue_script(
				'fd-smr-js',
				FD_SMR_PLUGIN_URL . 'js/app.js',
				array( 'jquery' ),
				filemtime( FD_SMR_PLUGIN_DIR_PATH . '/js/app.js' ),
				true
			);
		}
	}

	public static function allowed_redirect_hosts( array $hosts ): array {
		$hosts[] = 'steglogistic.nets-pay.link';
		return $hosts;
	}

	public static function print_footer_scripts(): void {
		if ( fd_is_active() && ! is_admin() && ! is_login() ) {
			fd_load_template( 'task-modal' );
			fd_load_template( 'success-task-modal' );
		}
	}

	public static function replace_old_form_shortcode( $output, $tag, $atts, $m ): false|string {
		global $post;

		if ( 'fluentform' !== $tag || 3 !== (int) $atts['id'] ) {
			return false;
		}

		if ( is_front_page() && get_field( '_fd_smr_replace_form_on_front_page', 'options' ) ) {
			return FD_Shortcodes::moving_request_form();
		}

		if ( 'pages/flyttfirma.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
			if ( get_field( '_fd_smr_replace_form_on_seo_pages', 'options' ) || get_field( '__fd_smr_use_the_new_form' ) ) {
				return FD_Shortcodes::moving_request_form();
			}
		}

		return false;
	}

	public static function apply_front_page_template( string $template ): string {
		if ( get_field( '_fd_smr_use_new_front_page_template', 'options' ) ) {
			$template = FD_SMR_PLUGIN_DIR_PATH . '/pages/new-front-page.php';
		}

		return $template;
	}
}
