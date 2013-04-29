<?php
	/*
	Plugin Name: HH sortable ID columns
	Plugin URI: http://studio.hamburg-hram.de
	Description: Enables id columns for all in admin panel.
	Version: 1.0.0
	Author: Vladimir Sklyar
	Author URI: http://imgf.ru

	Based on "Revial IDs" plugin.
	*/


	if (   is_admin()
		&& function_exists( 'add_action' )
		&& function_exists( 'add_filter' )
		&& function_exists( 'get_taxonomies' )
		&& function_exists( 'get_post_types' )
		&& function_exists( 'version_compare' )
	) {
		add_action( 'admin_init' , 'hh_add_custom_columns' );
		add_action( 'admin_head' , 'hh_add_custom_columns_css' );
	}


	function hh_add_custom_columns () {

		global $wp_version;
		$is_wp_v3_1 = version_compare( $wp_version , '3.0.999' , '>' );


		add_filter( 'manage_media_columns' ,       'hh_custom_column_add' );
		add_action( 'manage_media_custom_column' , 'hh_custom_column_value' , 10 , 2 );

		add_filter( 'manage_link-manager_columns' , 'hh_custom_column_add' );
		add_action( 'manage_link_custom_column' , 'column_value' , 10 , 2 );

		add_action( 'manage_edit-link-categories_columns' , 'hh_custom_column_add' );
		add_filter( 'manage_link_categories_custom_column' , 'column_return_value' , 10 , 3 );


		foreach( get_taxonomies() as $taxonomy ) {
			add_action( "manage_edit-${taxonomy}_columns" ,  'hh_custom_column_add' );
			add_filter( "manage_${taxonomy}_custom_column" , 'hh_custom_column_return_value' , 10 , 3 );
			if( $is_wp_v3_1 ) {
				add_filter( "manage_edit-${taxonomy}_sortable_columns" , 'hh_custom_column_add' );
			}
		}


		foreach( get_post_types() as $ptype ) {
			add_action( "manage_edit-${ptype}_columns" ,        'hh_custom_column_add' );
			add_filter( "manage_${ptype}_posts_custom_column" , 'hh_custom_column_value' , 10 , 3 );
			if( $is_wp_v3_1 ) {
				add_filter( "manage_edit-${ptype}_sortable_columns" , 'hh_custom_column_add' );
			}
		}


		add_action( 'manage_users_columns' ,       'hh_custom_column_add' );
		add_filter( 'manage_users_custom_column' , 'hh_custom_column_return_value' , 10 , 3 );
		if( $is_wp_v3_1 ) {
			add_filter( "manage_users_sortable_columns" , 'hh_custom_column_add' );
		}


		add_action( 'manage_edit-comments_columns' ,  'hh_custom_column_add' );
		add_action( 'manage_comments_custom_column' , 'hh_custom_column_value' , 10 , 2 );
		if( $is_wp_v3_1 ) {
			add_filter( "manage_edit-comments_sortable_columns" , 'hh_custom_column_add' );
		}

	}


	function hh_custom_column_add ( $columns ) {
		$column_id = array( 'hh_id' => __( 'ID' ) );
		$columns = array_slice( $columns, 0, 1, true ) + $column_id + array_slice( $columns, 1, NULL, true );
		return $columns;
	}


	function hh_custom_column_value ( $column_name , $id ) {
		if ( $column_name === 'hh_id' ) {
			echo $id;
		}
	}


	function hh_custom_column_return_value ( $value , $column_name , $id ) {

		if ( $column_name === 'hh_id' ) {
			$value .= $id;
		}
		return $value;

	}


	function hh_add_custom_columns_css() {
		echo PHP_EOL . '<style type="text/css"> table.widefat th.column-hh_id { width: 50px; } </style>' . PHP_EOL;
	}