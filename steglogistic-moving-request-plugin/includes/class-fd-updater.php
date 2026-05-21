<?php
/**
 * Plugin updater.
 *
 * Uses PUC filters to inject changelog (Markdown → HTML) and
 * to force the download URL to the CI ZIP from GitLab Release assets.
 */

use YahnisElsts\PluginUpdateChecker\v5p6\Vcs\PluginUpdateChecker;
use YahnisElsts\PluginUpdateChecker\v5p6\Vcs\GitLabApi;
use YahnisElsts\PluginUpdateChecker\v5p6\DebugBar\Panel;

class FD_Updater {

	/**
	 * GitLab repository URL.
	 *
	 * @var string
	 */
	private static string $repo_url = '';

	/**
	 * Private token for GitLab API.
	 *
	 * @var string
	 */
	private static string $repo_token = '';

	/**
	 * PUC update checker instance.
	 *
	 * @var PluginUpdateChecker
	 */
	private static $update_checker;

	/**
	 * Bootstrap updater.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( ! defined( 'FD_SMR_PLUGIN_UPDATE_TOKEN' ) || ! FD_SMR_PLUGIN_UPDATE_TOKEN ) {
			return;
		}

		// Ensure checks run from admin, cron, or WP-CLI.
		if ( ! is_admin() && ! wp_doing_cron() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}

		self::$repo_url   = FD_SMR_PLUGIN_UPDATE_REPO;
		self::$repo_token = FD_SMR_PLUGIN_UPDATE_TOKEN;
		self::setup();
	}

	/**
	 * Configure PUC and attach filters.
	 *
	 * @return void
	 */
	private static function setup(): void {
		$api = new GitLabApi( self::$repo_url, self::$repo_token );

		self::$update_checker = new PluginUpdateChecker(
			$api,
			FD_SMR_PLUGIN_DIR_PATH . '/steglogistic-moving-request-plugin.php',
			'steglogistic-moving-request-plugin',
			1 // Check interval (hours).
		);

		// Optional DebugBar panel (avoid is_plugin_active() in cron contexts).
		if ( class_exists( Panel::class ) ) {
			new Panel( self::$update_checker );
		} else {
			$panel_file = __DIR__ . '/../plugin-update-checker/Puc/v5p6/DebugBar/Panel.php';
			if ( file_exists( $panel_file ) ) {
				require_once $panel_file;
				if ( class_exists( Panel::class ) ) {
					new Panel( self::$update_checker );
				}
			}
		}

		self::$update_checker->setBranch( 'master' );

		/**
		 * Add PRIVATE-TOKEN header for both info and update HTTP requests.
		 * Using the PUC wrapper ensures proper "puc_" prefix and "-$slug" suffix.
		 */
		self::$update_checker->addFilter(
			'request_info_options',
			static function ( $options ) {
				$options['headers']['PRIVATE-TOKEN'] = self::$repo_token;
				return $options;
			}
		);

		self::$update_checker->addFilter(
			'request_update_options',
			static function ( $options ) {
				$options['headers']['PRIVATE-TOKEN'] = self::$repo_token;
				return $options;
			}
		);

		/**
		 * Populate "View version … details" (PluginInfo).
		 * Convert Markdown changelog → HTML and (optionally) ensure download_url.
		 *
		 * Signature may receive extra args; set accepted args to 2 for safety.
		 */
		self::$update_checker->addFilter(
			'request_info_result',
			static function ( $info ) use ( $api ) {
				if ( ! is_object( $info ) || empty( $info->version ) ) {
					return $info;
				}

				$release = self::get_release_for_version( $api, (string) $info->version );
				if ( ! $release ) {
					return $info;
				}

				if ( ! isset( $info->sections ) || ! is_array( $info->sections ) ) {
					$info->sections = array();
				}

				$md                          = (string) ( $release->apiResponse->description ?? '' );
				$info->sections['changelog'] = self::md_to_html( $md );

				// Fallback: if PUC didn't pick a URL, try the Release assets.
				if ( empty( $info->download_url ) ) {
					$zip = self::pick_zip_url( $release );
					if ( $zip ) {
						$info->download_url = $zip;
					}
				}

				return $info;
			},
			10,
			2
		);

		/**
		 * Force the actual update package (PluginUpdate) to use our CI ZIP.
		 * This replaces the non-existent addUpdateFilter().
		 */
		self::$update_checker->addFilter(
			'request_update_result',
			static function ( $update ) use ( $api ) {
				// $update is an instance of PluginUpdate or null.
				if ( empty( $update ) || ! is_object( $update ) || empty( $update->version ) ) {
					return $update;
				}

				$release = self::get_release_for_version( $api, (string) $update->version );
				if ( ! $release ) {
					return $update;
				}

				$zip = self::pick_zip_url( $release );
				if ( $zip ) {
					$update->download_url = $zip;
				}

				return $update;
			},
			10,
			2
		);
	}

	/**
	 * Get release matching version tag "vX.Y.Z". Fallback to latest.
	 *
	 * @param GitLabApi $api     API client.
	 * @param string    $version Version string (without leading 'v').
	 * @return object|null
	 */
	private static function get_release_for_version( GitLabApi $api, string $version ): ?object {
		$tag = 'v' . ltrim( $version, 'v' );

		try {
			if ( method_exists( $api, 'getRelease' ) ) {
				$rel = $api->getRelease( $tag );
				if ( $rel && isset( $rel->apiResponse ) ) {
					return $rel;
				}
			}

			$rel = $api->getLatestRelease();
			return ( $rel && isset( $rel->apiResponse ) ) ? $rel : null;

		} catch ( Throwable $e ) {
			return null;
		}
	}

	/**
	 * Pick ZIP URL from release assets.
	 * Prefers a link named "Plugin ZIP", then any Generic Package Registry zip.
	 *
	 * @param object $release GitLabApi release object.
	 * @return string|null
	 */
	private static function pick_zip_url( $release ): ?string {
		$links = $release->apiResponse->assets->links ?? array();

		if ( is_array( $links ) ) {
			foreach ( $links as $link ) {
				$name = (string) ( $link->name ?? '' );
				$url  = (string) ( $link->url ?? '' );

				if ( '' !== $name && false !== stripos( $name, 'Plugin ZIP' ) && '' !== $url ) {
					return $url;
				}
			}

			foreach ( $links as $link ) {
				$url = (string) ( $link->url ?? '' );

				if ( '' !== $url && preg_match( '~/(packages/generic)/.+\.zip$~i', $url ) ) {
					return $url;
				}
			}
		}

		return null;
	}

	/**
	 * Convert Markdown to HTML safely.
	 *
	 * @param string $md Markdown string.
	 * @return string HTML.
	 */
	private static function md_to_html( string $md ): string {
		// Lazy-load Parsedown.
		if ( ! class_exists( '\Parsedown' ) ) {
			$pd_file = FD_SMR_PLUGIN_DIR_PATH . '/inc/Parsedown.php';
			if ( file_exists( $pd_file ) ) {
				require_once $pd_file;
			}
		}

		if ( class_exists( '\Parsedown' ) ) {
			$pd   = \Parsedown::instance();
			$html = $pd->text( $md );
			return wp_kses_post( $html );
		}

		// Fallback: escape text and wrap with paragraphs.
		return wpautop( esc_html( $md ) );
	}
}
