<?php
/**
 * Mail taxonomies creator.
 *
 * This class defines Mail taxonomies.
 *
 * @link       padresenlanube.com/
 * @since      1.0.0
 * @package    MAILPN
 * @subpackage MAILPN/includes
 * @author     Padres en la Nube <info@padresenlanube.com>
 */
class MAILPN_Taxonomies_Mail { 
	/**
	 * Register taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function mailpn_register_taxonomies() {
		$taxonomies = [
			'mailpn_mail_category' => [
				'name'               	=> _x('Mail categories', 'Taxonomy general name', 'mailpn'),
				'singular_name'      	=> _x('Mail category', 'Taxonomy singular name', 'mailpn'),
				'search_items'      	=> esc_html(__('Search Mail categories', 'mailpn')),
        'all_items'         	=> esc_html(__('All Mail categories', 'mailpn')),
        'parent_item'       	=> esc_html(__('Parent Mail category', 'mailpn')),
        'parent_item_colon' 	=> esc_html(__('Parent Mail category:', 'mailpn')),
        'edit_item'         	=> esc_html(__('Edit Mail category', 'mailpn')),
        'update_item'       	=> esc_html(__('Update Mail category', 'mailpn')),
        'add_new_item'      	=> esc_html(__('Add New Mail category', 'mailpn')),
        'new_item_name'     	=> esc_html(__('New Mail category', 'mailpn')),
        'menu_name'         	=> esc_html(__('Mail categories', 'mailpn')),
				'manage_terms'      	=> 'manage_mailpn_mail_category',
	      'edit_terms'        	=> 'edit_mailpn_mail_category',
	      'delete_terms'      	=> 'delete_mailpn_mail_category',
	      'assign_terms'      	=> 'assign_mailpn_mail_category',
	      'archive'			      	=> false,
	      'slug'			      		=> 'mail',
			],
		];;

	  foreach ($taxonomies as $taxonomy => $options) {
	  	$labels = [
				'name'          		=> $options['name'],
				'singular_name' 		=> $options['singular_name'],
			];

			$capabilities = [
				'manage_terms'      => $options['manage_terms'],
				'edit_terms'      	=> $options['edit_terms'],
				'delete_terms'      => $options['delete_terms'],
				'assign_terms'      => $options['assign_terms'],
	    ];

			$args = [
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => false,
				'show_ui' 					=> false,
				'query_var'         => false,
				'rewrite'           => false,
				'show_in_rest'      => true,
	    	'capabilities'      => $capabilities,
			];

			if ($options['archive']) {
				$args['public'] = true;
				$args['publicly_queryable'] = true;
				$args['show_in_nav_menus'] = true;
				$args['query_var'] = $taxonomy;
				$args['show_ui'] = true;
				$args['rewrite'] = [
					'slug' => $options['slug'],
				];
			}

			register_taxonomy($taxonomy, 'mailpn_mail', $args);
			register_taxonomy_for_object_type($taxonomy, 'mailpn_mail');
		}
	}
}