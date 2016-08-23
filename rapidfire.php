<?php
/**
* Plugin Name: RapidFire
* Plugin URI: http://github.com/patilswapnilv/rapidfire
* Description: Plugin for displaying and managing pretty, dynamic quizzes.
* Version: 0.0.1
* Author: patilswapnilv, shefalishirodkar20
* Author URI: http://swapnilpatil.in
* License: GPLv3 or later
* License URI: http://www.gnu.org/licenses/gpl.html
*/


/*
Copyright (c) 2013 Quicken Loans

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Stop direct call
if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
    die( 'You are not allowed to call this page directly.' );
}

if ( !class_exists( 'RapidFire' ) ) {
    class RapidFire
    {

        // Constructor
        function __construct()
        {
            $this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

            // Activate for New Installs
            register_activation_hook( $this->plugin_name, array( &$this, 'activate' ) );

            // Activate for Updates
            add_action( 'plugins_loaded', array( &$this, 'activate' ) );

            // Include Quiz Helper (Shared Methods)
            include_once ( dirname ( __FILE__ ) . '/php/rapidfire-helper.php' );

            // Include Quiz Model
            include_once ( dirname ( __FILE__ ) . '/php/rapidfire-model.php' );

            // Include Quiz Widgets
            include_once ( dirname ( __FILE__ ) . '/php/rapidfire-widgets.php' );

            // Setup Quiz Shortcodes
            include_once ( dirname ( __FILE__ ) . '/php/rapidfire-front.php' );
            $rapidFireFront = new RapidFireFront();

            // Register AJAX actions
            include_once ( dirname ( __FILE__ ) . '/php/rapidfire-functions.php' );
            $rapidFireFunctions = new RapidFireFunctions();

            // Register non-menu pages
            add_action( 'admin_menu', array( &$this, 'register_aux_pages' ) );

            // Add the admin menu
            add_action( 'admin_menu', array( &$this, 'add_menu' ) );

            // Add the script and style files
            add_action( 'admin_enqueue_scripts', array( &$this, 'load_resources' ) );

            // Display Update Notice
            add_action( 'admin_notices', array( &$this, 'update_notice' ) );
            add_action( 'admin_init', array( &$this, 'ignore_notice' ) );
        }

        // On Activation - Create RapidFire Database Table And Setup Options
        function activate()
        {
            $this->create_quiz_table();
            $this->create_score_table();

            $quizHelper = new RapidFireHelper;
            $quizHelper->get_admin_options();
        }

        // Display update notice
        function update_notice()
        {
            global $current_user;

            $options = get_option( "slick_quiz_options" );
            $disabled = isset($options['disable_responses']);

            if ( $disabled && !get_user_meta( $current_user->ID, 'rapidfire_ignore_notice_disabled' ) ) {
                echo '<div class="error">';
                printf( __( '<p><strong>NOTICE: RapidFire options have changed.</strong></p>' .
                    '<p>The single option to disable response messages has been removed, ' .
                    'instead you should disable both per question and completion response message options.</p>' .
                    '<p><a href="' . admin_url( 'admin.php?page=rapidfire-options#responses' ) . '">Edit Options</a>  &nbsp; ' .
                    '<a href="%1$s">Hide This Notice</a></p>' ), add_query_arg( 'rapidfire_ignore_notice', '0' ) );
                echo "</div>";
            }
        }

        // Allow user to ignore update notice
        function ignore_notice()
        {
            global $current_user;

            if ( isset( $_GET['rapidfire_ignore_notice'] ) && '0' == $_GET['rapidfire_ignore_notice'] ) {
                add_user_meta( $current_user->ID, 'rapidfire_ignore_notice_disabled', 'true', true );
            }
        }

        // Add RapidFire Menu to Navigation
        function add_menu()
        {
            // Accessible to Authors, Editors, and Admins
            add_menu_page( 'RapidFire', 'RapidFire', 'publish_posts', 'rapidfire', array( &$this, 'direct_route' ), 'dashicons-awards' );

            // Accessible to Editors and Admins
            add_submenu_page( 'rapidfire', 'Add Quiz', 'Add Quiz', 'publish_pages', 'rapidfire-new', array( &$this, 'direct_route') );

            // Accessible to Admins
            add_submenu_page( 'rapidfire', 'Default Options', 'Default Options', 'manage_options', 'rapidfire-options', array( &$this, 'direct_route') );
        }

        // Register Non-Menu Pages
        function register_aux_pages()
        {
            global $_registered_pages;

            $hooknameEdit = get_plugin_page_hookname( 'rapidfire-edit', 'admin.php' );
            if ( !empty( $hooknameEdit ) ) {
                add_action( $hooknameEdit, array( &$this, 'direct_route' ) );
            }
            $_registered_pages[$hooknameEdit] = true;

            $hooknamePreview = get_plugin_page_hookname( 'rapidfire-preview', 'admin.php' );
            if ( !empty( $hooknamePreview ) ) {
                add_action( $hooknamePreview, array( &$this, 'direct_route' ) );
            }
            $_registered_pages[$hooknamePreview] = true;

            $hooknameScores = get_plugin_page_hookname( 'rapidfire-scores', 'admin.php' );
            if ( !empty( $hooknameScores ) ) {
                add_action( $hooknameScores, array( &$this, 'direct_route' ) );
            }
            $_registered_pages[$hooknameScores] = true;
        }

        // Basic Router
        function direct_route()
        {
            switch ( $_GET['page'] ) {
            case 'rapidfire-new' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-new.php' );
                break;
            case 'rapidfire-edit' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-edit.php' );
                break;
            case 'rapidfire-preview' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-preview.php' );
                break;
            case 'rapidfire-functions' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-functions.php' );
                break;
            case 'rapidfire-options' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-options.php' );
                break;
            case 'rapidfire-scores' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-scores.php' );
                break;
            case 'rapidfire' :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-admin.php' );
                break;
            default :
                include_once ( dirname ( __FILE__ ) . '/php/rapidfire-admin.php' );
                break;
            }
        }

        // Add Admin JS and styles
        function load_resources()
        {
            // Only load resources when in RapidFire Admin section
            preg_match( '/rapidfire/is', $_SERVER['REQUEST_URI'], $matches );
            if ( count( $matches) == 0 ) return;

            // Scripts
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'rapidfire_admin_js', plugins_url( '/js/admin.js', __FILE__ ) );

            // Styles
            wp_enqueue_style( 'rapidfire_admin_css', plugins_url( '/css/admin.css', __FILE__ ) );
        }

        // Create Quiz Database Table
        function create_quiz_table()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'plugin_rapidfire';

            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                publishedJson longtext NULL,
                workingJson longtext NULL,
                publishedQCount int(11) NULL,
                workingQCount int(11) NULL,
                hasBeenPublished tinyint(1) NOT NULL DEFAULT '0',
                publishedBy bigint(20) unsigned NOT NULL DEFAULT '0',
                publishedDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                createdBy bigint(20) unsigned NOT NULL DEFAULT '0',
                createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                lastUpdatedBy bigint(20) unsigned NOT NULL DEFAULT '0',
                lastUpdatedDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY  (id),
                KEY createdBy_index (createdBy)
            );";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

        // Create User Score Database Table
        function create_score_table()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'plugin_rapidfire_scores';

            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                email varchar(255) NOT NULL,
                score varchar(50) NOT NULL,
                quiz_id bigint(20) unsigned NOT NULL DEFAULT '0',
                createdBy bigint(20) unsigned NOT NULL DEFAULT '0',
                createdDate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY  (id),
                KEY quiz_id_index (quiz_id),
                KEY score_index (score)
            );";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

    } // End Class RapidFire
}

if ( class_exists( 'RapidFire' ) ) {
    global $rapidFire;
    $rapidFire = new RapidFire();
}

?>
