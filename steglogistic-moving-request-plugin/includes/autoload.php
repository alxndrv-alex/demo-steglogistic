<?php
function fd_class_file_autoloader( string $class_name ): void {
	if ( 0 !== strncmp( $class_name, 'FD_', 3 ) ) {
		return;
	}
	$file_name            = strtolower( $class_name );
	$class_name           = 'class-' . str_replace(
			array(
				'_',
				'(',
				')',
			),
			array(
				'-',
				'',
				'',
			),
			$file_name
		);
	$trait_name           = 'trait-' . str_replace(
			array(
				'_',
				'(',
				')',
			),
			array(
				'-',
				'',
				'',
			),
			$file_name
		);
	$template_names       = array(
		'/includes/' . $class_name . '.php',
		'/includes/' . $trait_name . '.php',
	);

	foreach ( $template_names as $template ) {
		if ( file_exists( FD_SMR_PLUGIN_DIR_PATH . $template ) ) {
			include_once FD_SMR_PLUGIN_DIR_PATH . $template;
		}
	}
}

spl_autoload_register( 'fd_class_file_autoloader' );