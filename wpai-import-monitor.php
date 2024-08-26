<?php
/**
 * Plugin Name: WP All Import - Import Monitor
 * Plugin URI: https://github.com/gutewebsites/wpai-import-monitor
 * Description: Monitors when an import was last started and outputs this information as JSON for each import job ID, including the number of updated, deleted and skipped entries.
 * Version: 1.0.0
 * Author: Büro für gute Websites, André Goldmann
 * Author URI: https://www.gutewebsites.de
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class WP_Import_Monitor {
    
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        add_action( 'pmxi_before_xml_import', array( $this, 'set_last_import_time' ), 10, 1 );
        add_action( 'pmxi_after_xml_import', array( $this, 'set_import_summary' ), 10, 1 );
        add_action( 'rest_api_init', function () {
            register_rest_route( 'wpai/v1', '/last_import_times', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_last_import_times' ),
            ));
        });
    }

    public function set_last_import_time( $import_id ) {
        $last_import_times = get_option( 'wpai_last_import_times', array() );
        if ( ! is_array( $last_import_times ) ) {
            $last_import_times = array();
        }
        
        if ( ! isset( $last_import_times[ $import_id ] ) || ! is_array( $last_import_times[ $import_id ] ) ) {
            $last_import_times[ $import_id ] = array();
        }

        $last_import_times[ $import_id ]['last_import_time'] = current_time( 'mysql' );
        update_option( 'wpai_last_import_times', $last_import_times );
    }

    public function set_import_summary( $import_id ) {
        global $wpdb;

        $summary = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT updated, deleted, skipped FROM {$wpdb->prefix}pmxi_imports WHERE id = %d",
                $import_id
            )
        );

        if ( $summary ) {
            $last_import_times = get_option( 'wpai_last_import_times', array() );
            if ( ! is_array( $last_import_times ) ) {
                $last_import_times = array();
            }
            
            if ( ! isset( $last_import_times[ $import_id ] ) || ! is_array( $last_import_times[ $import_id ] ) ) {
                $last_import_times[ $import_id ] = array();
            }

            $last_import_times[ $import_id ]['updated'] = isset($summary->updated) ? $summary->updated : 0;
            $last_import_times[ $import_id ]['deleted'] = isset($summary->deleted) ? $summary->deleted : 0;
            $last_import_times[ $import_id ]['skipped'] = isset($summary->skipped) ? $summary->skipped : 0;
            update_option( 'wpai_last_import_times', $last_import_times );
        }
    }

    public function get_last_import_times() {
        $last_import_times = get_option( 'wpai_last_import_times', array() );
        
        if ( ! is_array( $last_import_times ) ) {
            return new WP_Error( 'invalid_data', 'Invalid data found in wpai_last_import_times option.', array( 'status' => 500 ) );
        }

        if ( ! empty( $last_import_times ) ) {
            return rest_ensure_response( $last_import_times );
        } else {
            return new WP_Error( 'no_imports', 'No imports have been recorded.', array( 'status' => 404 ) );
        }
    }
}

new WP_Import_Monitor();