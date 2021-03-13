<?php


/**
 * Plugin Name: WP Custom Post Type
 * Description: Magic Plugin performs magic. 
 * Plugin URI: http://example.com/magic-plugin
 * Version: 1.0.0
 * Author: saleh ghezelbash
 * Text Domain: magic-plugin 
 * 
 * @package Magic Plugin
 */

register_activation_hook( __FILE__, 'pf_rb_install' );
function pf_rb_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . "books_info";
    $pf_parts_db_version = '1.0.0';
    $charset_collate = $wpdb->get_charset_collate();

    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {

        $sql = "CREATE TABLE $table_name (
                        id mediumint(9) NOT NULL AUTO_INCREMENT,
                        post_id INT,
                        isbn BIT DEFAULT 0,
                        PRIMARY KEY  (id)
                        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        add_option( 'pf_parts_db_version', $pf_parts_db_version );
    }
}




function my_plugin_remove_database() {
    global $wpdb;
    $table_name = $wpdb->prefix . "books_info";
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
    delete_option("my_plugin_db_version");
}

register_deactivation_hook( __FILE__, 'my_plugin_remove_database' );




function wpdocs_codex_book_init() {
    $labels = array(
        'name'                  => __( 'Books' ),
        'singular_name'         => __( 'Book' ),
        'menu_name'             => __( 'Books' ),
        'name_admin_bar'        => __( 'Book' ),
        'add_new'               => __( 'Add New'),
        'add_new_item'          => __( 'Add New Book'),
        'new_item'              => __( 'New Book'),
        'edit_item'             => __( 'Edit Book'),
        'view_item'             => __( 'View Book'),
        'all_items'             => __( 'All Books'),
        'search_items'          => __( 'Search Books'),
        'parent_item_colon'     => __( 'Parent Books:'),
        'not_found'             => __( 'No books found.'),
        'not_found_in_trash'    => __( 'No books found in Trash.')
 
    );
 
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-book',
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'book' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
    );
 
    register_post_type( 'book', $args );
}
add_action( 'init', 'wpdocs_codex_book_init' );

add_action("add_meta_boxes","my_add_meta_box");
function my_add_meta_box(){
    add_meta_box("cpt-id","My Meta Box","wp_meta_box_book","book","side","high");
}
function wp_meta_box_book($post){
    ?>
    <p>
    <label for="isbn1">Isbn:</label>
    <input id="isbn1" type="checkbox" name="isbn" value="1">
    </p>
    <?php
}

function my_add_meta_box_save($post_id,$post){

    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : "";
  
    global $wpdb;
    $table_name = $wpdb->prefix . "books_info";
    $wpdb->insert( $tablename, array(
        'post_id' => $post_id,
        'isbn' => $isbn, 
    ));
}
add_action("publish_post","my_add_meta_box_save",10,2);



function wpdocs_create_book_taxonomies() {
    $labels = array(
        'name'              => _x( 'Authors', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Author', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Authors', 'textdomain' ),
        'all_items'         => __( 'All Authors', 'textdomain' ),
        'parent_item'       => __( 'Parent Author', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Author:', 'textdomain' ),
        'edit_item'         => __( 'Edit Author', 'textdomain' ),
        'update_item'       => __( 'Update Author', 'textdomain' ),
        'add_new_item'      => __( 'Add New Author', 'textdomain' ),
        'new_item_name'     => __( 'New Author Name', 'textdomain' ),
        'menu_name'         => __( 'Author', 'textdomain' ),
    );
 
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'author' ),
    );
 
    register_taxonomy( 'author', array( 'book' ), $args );
 
 
    $labels = array(
        'name'                       => _x( 'Publisher', 'taxonomy general name', 'textdomain' ),
        'singular_name'              => _x( 'Publisher', 'taxonomy singular name', 'textdomain' ),
        'search_items'               => __( 'Search Publisher', 'textdomain' ),
        'popular_items'              => __( 'Popular Publisher', 'textdomain' ),
        'all_items'                  => __( 'All Publisher', 'textdomain' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Publisher', 'textdomain' ),
        'update_item'                => __( 'Update Publisher', 'textdomain' ),
        'add_new_item'               => __( 'Add New Publisher', 'textdomain' ),
        'new_item_name'              => __( 'New Publisher Name', 'textdomain' ),
        'separate_items_with_commas' => __( 'Separate Publisher with commas', 'textdomain' ),
        'add_or_remove_items'        => __( 'Add or remove Publisher', 'textdomain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Publisher', 'textdomain' ),
        'not_found'                  => __( 'No Publisher found.', 'textdomain' ),
        'menu_name'                  => __( 'Publisher', 'textdomain' ),
    );
 
    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'publisher' ),
    );
 
    register_taxonomy( 'publisher', 'book', $args );
}
add_action( 'init', 'wpdocs_create_book_taxonomies', 0 );




?> 