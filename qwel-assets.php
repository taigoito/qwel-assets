<?php
/*
Plugin Name: Discover Echizen Assets
Description: This is an asset when using the theme "Discover Echizen".
Version: 1.0
Requires PHP: 7.4
Author: Taigo Ito
Author URI: https://qwel.design/
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */


defined( 'ABSPATH' ) || exit;


/*
 * プラグインのパス, URI
 */
define( 'DISCOVERECHIZEN_ASSETS_DIR', WP_PLUGIN_DIR . '/discoverechizen-assets/' );
define( 'DISCOVERECHIZEN_ASSETS_URI', WP_PLUGIN_URL . '/discoverechizen-assets/' );


/*
 * classのオートロード
 */
spl_autoload_register(
	function( $classname ) {
		if ( strpos( $classname, 'DiscoverEchizen_Assets' ) === false ) return;
		$classname = str_replace( '\\', '/', $classname );
		$classname = str_replace( 'DiscoverEchizen_Assets/', '', $classname );
		$file      = DISCOVERECHIZEN_ASSETS_DIR . '/classes/' . $classname . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

class DiscoverEchizen_Assets {
  use \DiscoverEchizen_Assets\Shortcodes;
		
	public function __construct() {
    // ブロックスタイルを追加 (エディター)
    add_action( 'enqueue_block_editor_assets', [ $this, 'add_block_styles' ] );

    // CSSファイルを読み込み (エディター)
    add_action( 'enqueue_block_assets', [ $this, 'enqueue_scripts' ] );

		// CSS, JSファイルを読み込み (フロント)
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    
    // ショートコード登録
    add_action( 'init', [ $this, 'register_shortcode' ] );

	}

  public function add_block_styles() {
    // blockStyles.js
    wp_enqueue_script(
      'discoverechizen-block-styles',
      plugins_url( 'blockStyles.js', __FILE__ ),
      [ 'wp-blocks' ]
    );

  }

  public function enqueue_scripts() {
    // バージョン情報
    if( !function_exists( 'get_plugin_data' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $plugin_data = get_plugin_data( __FILE__ );
    $version     = $plugin_data['Version'];

		// style.css
		wp_enqueue_style(
			'discoverechizen-style',
			plugins_url( 'style.css', __FILE__ ),
			[],
      $version
		);

    // init.js (フロントエンドのみ)
    if ( !is_admin() ) {
      wp_enqueue_script(
        'discoverechizen-init',
        plugins_url( 'init.js', __FILE__ ),
        [],
        $version,
        true
      );
    }

  }

}

new DiscoverEchizen_Assets();
