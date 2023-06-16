<?php
/*
Plugin Name: Qwel Assets Beta
Description: This is an asset when using the theme "Qwel".
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
define( 'QWEL_ASSETS_DIR', WP_PLUGIN_DIR . '/qwel-assets/' );
define( 'QWEL_ASSETS_URI', WP_PLUGIN_URL . '/qwel-assets/' );


/*
 * classのオートロード
 */
spl_autoload_register(
	function( $classname ) {
		if ( strpos( $classname, 'Qwel_Assets' ) === false ) return;
		$classname = str_replace( '\\', '/', $classname );
		$classname = str_replace( 'Qwel_Assets/', '', $classname );
		$file      = QWEL_ASSETS_DIR . '/classes/' . $classname . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

class Qwel_Assets {
  use \Qwel_Assets\Shortcodes;
		
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
      'qwel-block-styles',
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
			'qwel-style',
			plugins_url( 'style.css', __FILE__ ),
			[],
      $version
		);

    // init.js (フロントエンドのみ)
    if ( !is_admin() ) {
      wp_enqueue_script(
        'qwel-init',
        plugins_url( 'init.js', __FILE__ ),
        [],
        $version,
        true
      );
    }

  }

}

new Qwel_Assets();
