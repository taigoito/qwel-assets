<?php
namespace OkuetuManmaru_Assets;

trait Shortcodes {
  // ショートコード登録
  public function register_shortcode() {

    // タイトルを作成
    add_shortcode( 'title', [ $this, 'get_title' ] );

    // パンくずリストを作成
    add_shortcode( 'breadcrumb', [ $this, 'get_breadcrumb' ] );

    // コピーライトに現在年を添える
    add_shortcode( 'copyright', [ $this, 'get_copyright' ] );
    
  }

  public function get_title( $atts ) {

    $wp_obj  = get_queried_object();
    $title = '';

    // 固定ページ
    if ( is_home() || is_page() ) {
      $title = $wp_obj->post_title;

    // 個別投稿ページ
    } else if ( is_single() ) {
      $title = 'ブログ';

    // 日付別
    } else if ( is_date() ) {
      $year  = get_query_var( 'year' );
      $month = get_query_var( 'monthnum' );
      $day   = get_query_var( 'day' );
      if ( $day > 0 ) $title = $year . ' / ' . sprintf( '%02d', $month ) . ' / ' . sprintf( '%02d', $day );
      else if ( $month > 0 ) $title = $year . ' / ' . sprintf( '%02d', $month );
      else $title = $year;

    // 投稿者アーカイブ
    } else if ( is_author() ) {
      $title = '著者:' . $wp_obj->display_name;
    
    // タームアーカイブ
    } else if ( is_archive() ) {
      $title = $wp_obj->name;

    // 検索結果ページ
    } else if ( is_search() ) {
      $title = '検索:' . get_search_query();

    // 404ページ
    } else if ( is_404() ) {
      $title = '記事が見つかりません';

    // その他
    } else {
      $title = 'ブログ';
    }

    return '<h1>' . $title . '</h1>';
    
  }

  public function get_breadcrumb( $atts ) {
    $html = '<ul id="breadcrumb" class="breadcrumb">';
    $html .= '<li class="breadcrumb__item"><a href="' . home_url( '/' ) . '">top</a></li>';

    $wp_obj = get_queried_object();
    /**
     * 投稿一覧ページ
     */
    if (is_home()) {

      $html .= '<li class="breadcrumb__item">' . get_post_type_object( 'post' )->label . '</li>';

    /**
     * 個別投稿ページ
     */
    } else if ( is_single() ) {
      $post_id     = $wp_obj->ID;
      $post_type   = $wp_obj->post_type;
      $post_title  = $wp_obj->post_title;

      // カスタム投稿タイプの場合、投稿タイプ一覧を表示
      if ( $post_type !== 'post' ) {

        $html .= '<li class="breadcrumb__item"><a href="' . get_post_type_archive_link($post_type) . '">' . get_post_type_object($post_type)->label . '</a></li>';

      }

      // カスタム投稿タイプから、タクソノミーを取得
      if ( $post_type == 'post' ) {
        // 「投稿」の場合、「カテゴリー」を取得
        $tax_name = 'category';

        /**
         * カスタムタクソノミーを作成した場合は、ここに処理を追加
         */

      } else {
        $tax_name = '';
      }

      // タクソノミーが紐づいていれば表示
      if ( $tax_name != '' ) {
        $terms = get_the_terms( $post_id, $tax_name ); // 投稿に紐づく全タームを取得

        if ( !empty( $terms )) {
          $term = $terms[0];

          /* Custom */
          if ( $term->slug !== 'feature' ) {

            // 親タームがあれば表示
            if ( $term->parent > 0 ) {
              $parent_array = array_reverse(get_ancestors( $term->term_id, $tax_name ));
              foreach ( $parent_array as $parent_id ) {
                $parent_term = get_term( $parent_id, $tax_name );

                $html .= '<li class="breadcrumb__item"><a href="' . get_term_link( $parent_id, $tax_name ) . '">' . $parent_term->name . '</a></li>';

              }
            }

            // 最下層タームを表示

            $html .= '<li class="breadcrumb__item"><a href="' . get_term_link( $term->term_id, $tax_name ) . '">' . $term->name . '</a></li>';

          }
        }
      }

      // 自身

      $html .= '<li class="breadcrumb__item">' . $post_title . '</li>';
    
    /**
     * 固定ページ
     */
    } else if ( is_page() ) {
      $page_id     = $wp_obj->ID;
      $page_title  = $wp_obj->post_title;

      // 親ページがあれば表示
      if ( $wp_obj->post_parent > 0 ) {
        $parent_array = array_reverse(get_post_ancestors( $page_id ));
        foreach ( $parent_array as $parent_id ) {

          $html .= '<li class="breadcrumb__item"><a href="' . get_permalink( $parent_id ) . '">' . get_the_title( $parent_id ) . '</a></li>';

        }
      }

      // 自身

      $html .= '<li class="breadcrumb__item">' . $page_title . '</li>';

    /**
     * カスタム投稿アーカイブ
     */
    } else if ( is_post_type_archive() ) {

      $html .= '<li class="breadcrumb__item">' . $wp_obj->label . '</li>';

    /**
     * 日付別
     */
    } else if ( is_date() ) {
      $year    = get_query_var( 'year' );
      $month   = get_query_var( 'monthnum' );
      $day     = get_query_var( 'day' );

      // 日別アーカイブ
      if ( $day > 0 ) {

        $html .= '<li class="breadcrumb__item"><a href="' . get_year_link( $year ) . '">' . $year . '</a></li>';
        $html .= '<li class="breadcrumb__item"><a href="' . get_month_link( $year, $month ) . '">' . sprintf( '%02d', $month) . '</a></li>';
        $html .= '<li class="breadcrumb__item">' . sprintf( '%02d', $day ) . '</li>';

      // 月別アーカイブ
      } else if ( $month > 0 ) {

        $html .= '<li class="breadcrumb__item"><a href="' . get_year_link( $year ) . '">' . $year . '</a></li>';
        $html .= '<li class="breadcrumb__item">' . sprintf( '%02d', $month ) . '</li>';

      // 年別アーカイブ
      } else {

        $html .= '<li class="breadcrumb__item">' . $year . '</li>';

      }
    /**
     * 投稿者アーカイブ
     */
    } else if ( is_author() ) {

      $html .= '<li class="breadcrumb__item">著者: ' . $wp_obj->display_name . '</li>';

    /**
     * タームアーカイブ
     */
    } else if ( is_archive() ) {
      $post_type = '';
      $term      = $wp_obj;
      $term_id   = $wp_obj->term_id;
      $term_name = $wp_obj->name;
      $tax_name  = $wp_obj->taxonomy;

      // 「カテゴリー」、「タグ」の場合、「投稿」を取得
      if ( $tax_name == 'category' || $tax_name == 'post_tag' ) {
        $post_type = 'post';
      }

      /**
       * カスタムタクソノミーを作成した場合は、ここに処理を追加
       */

      // カスタム投稿タイプの場合、投稿タイプ一覧を表示
      if ( $post_type !== 'post' ) {

        $html .= '<li class="breadcrumb__item"><a href="' . get_post_type_archive_link( $post_type ) . '">' . get_post_type_object( $post_type )->label . '</a></li>';

      }
      
      // 親タームがあれば表示
      if ( $term->parent > 0 ) {
        $parent_array = array_reverse(get_ancestors( $term->term_id, $tax_name ));
        foreach ( $parent_array as $parent_id ) {
          $parent_term = get_term( $parent_id, $tax_name );

          $html .= '<li class="breadcrumb__item"><a href="' . get_term_link( $parent_id, $tax_name ) . '">' . $parent_term->name . '</a></li>';

        }
      }

      // 自身
      $html .= '<li class="breadcrumb__item">' . $term_name . '</li>';

    /**
     * 検索結果ページ
     */
    } else if ( is_search() ) {

      $html .= '<li class="breadcrumb__item">検索: ' . get_search_query() . '</li>';

    /**
     * 404ページ
     */
    } else if ( is_404() ) {

      $html .= '<li class="breadcrumb__item">404 記事が見つかりません</li>';
    
    }
    
    return $html . '</ul>';

  }

  public function get_copyright( $atts ) {
    // デフォルト値
    $atts = shortcode_atts(
      [
        'year' => '2019',
        'text' => 'OkuetuManmaru'
      ],
      $atts
    );

    // コピーライト文字列を作成
    $copyright = '&copy; ' . $atts[ 'year' ];
    $year = getdate()[ 'year' ];
    if ( $atts[ 'year' ] == $year ) {
      $copyright .= ' ' . $atts[ 'text' ];
    } else {
      $copyright .= ' - ' . $year . ' ' . $atts[ 'text' ];
    }

    return '<small class="copyright">' . $copyright . '</small>';

  }

}
