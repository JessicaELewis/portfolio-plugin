<?php
/*
Plugin Name: Jlewis Portfolio Plugin
Plugin URI: http://jessicalewis.org/
Description: Declares a plugin that will create a custom post type displaying portfolio projects
Version: 1.0
Author: Jessica Lewis
Author URI: http://jessicalewis.org
License: GPLv2
*/

function jlewis_projects_init() {
    $labels = array(
        'name'               => _x( 'Projects', 'post type general name' ),
        'singular_name'      => _x( 'Project', 'post type singular name' ),
        'menu_name'          => _x( 'Projects', 'admin menu' ),
        'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
        'add_new'            => _x( 'Add New', 'project' ),
        'add_new_item'       => __( 'Add New Project' ),
        'new_item'           => __( 'New Project' ),
        'edit_item'          => __( 'Edit Project' ),
        'view_item'          => __( 'View Project' ),
        'all_items'          => __( 'All Projects' ),
        'search_items'       => __( 'Search Projects' ),
        'parent_item_colon'  => __( 'Parent Projects:' ),
        'not_found'          => __( 'No projects found.' ),
        'not_found_in_trash' => __( 'No projects found in Trash.' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'A list of projects I\'ve done.' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'with_front' => false, 'slug' => 'project' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'author', 'thumbnail', 'excerpt', 'revisions' ),
        'taxonomies' => array('post_tag'),
    );

    register_post_type( 'jlewis_project', $args );
}

add_action( 'init', 'jlewis_projects_init' );


// Create custom meta boxes for custom post type "jlewis_project"

$prefix = 'jlproj_';

$meta_box = array(
    'id' => 'project-data',
    'title' => 'Project Data',
    'page' => 'jlewis_project',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => 'Screenshot of Mobile view',
            'desc' => 'Image should be 180 x 320',
            'id' => $prefix . 'mobile',
            'type' => 'image',
            'std' => ''
        ),
        array(
            'name' => 'Screenshot of Mobile Menu',
            'desc' => 'Image should be 180 x 320',
            'id' => $prefix . 'mobile_menu',
            'type' => 'image',
            'std' => ''
        ),
        array(
            'name' => 'Website URL',
            'label' => 'URL',
            'desc' => 'The address of the live site, if applicable',
            'id' => $prefix . 'url',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Goal',
            'desc' => 'The instructions or request from the client, or the goal of the project',
            'id' => $prefix . 'description',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Resolution',
            'desc' => 'Describe how you achieved the goal, or the process of completing the project',
            'id' => $prefix . 'process',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Notes',
            'desc' => 'Any notes after the project was concluded, or comments on what you would do differently in the future',
            'id' => $prefix . 'notes',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Screenshots or other images, and descriptions to go along with them if necessary',
            'desc' => 'Before, after, in-progress, or any other images you want to display in the right column of the single-project pages',
            'id' => $prefix . 'visual',
            'type' => 'wysiwyg',
            'std' => ''
        ),
        array(
            'name' => 'Code',
            'desc' => 'Code used, in snippets or as a whole',
            'id' => $prefix . 'code',
            'type' => 'textarea',
            'std' => ''
        ),
        array(
            'name' => 'Date Completed',
            'desc' => 'The date that the project was completed, or an estimate of when you worked on it',
            'id' => $prefix . 'date',
            'type' => 'date',
            'std' =>  date("Y-m-d"),
        )
    )
);


// hook for adding fields to edit screen
add_action('admin_menu', 'jlewis_add_fields');

// function to add fields to edit screen
function jlewis_add_fields() {
    global $meta_box;

    add_meta_box($meta_box['id'], $meta_box['title'], 'jlewis_display_fields', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
}


// callback function to display the HTML that actually displays the fields on the page

function jlewis_display_fields() {
    global $meta_box;
    global $post;

    // use nonce for verification
    wp_nonce_field( 'jlewis_save_meta_box_data', 'jlewis_meta_box_nonce' );

    echo '<table class="form-table">';

    // loop through each field and print table rows depending on the input type
    foreach ($meta_box['fields'] as $field) {
        //get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        $metadata = get_metadata('post', $post->ID);

        echo '<tr>',
        '<th style="width:20%"><label for ="', $field['id'], '">', $field['name'], '</label></th>',
        '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'] , '" id="', $field['id'] , '" value="', $meta ? $meta : $field['std'] , '" size="60" style="width:97%;" />', '<br />', $field['desc'];
                break;
            case 'textarea':
                echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>', '<br />', $field['desc'];
                break;
            case 'date':
                echo '<input type="date" name="', $field['name'] , '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" />', '<br />', $field['desc'];
                break;
            case 'image':
                $image_src = $meta ? $meta : $field['std'];
                echo '<input type="text" name="', $field['id'] , '" id="', $field['id'], '" class="meta-image regular-text" value="', $meta, '">
                      <input type="button" class="button image-upload" value="Browse">
                      <div class="image-preview"><img src="'.$image_src.'" style="max-width: 250px;"></div>';
                break;
            case 'wysiwyg':
                $content = $meta ? $meta : $field['std'];
                global $prefix;
                $editor_id = $prefix . 'visual';

                wp_editor( $content, $editor_id );
        }
        echo '</td></tr>';
    }
    echo '</table>';
}


// save data of custom fields
add_action('save_post', 'jlewis_save_data');

function jlewis_save_data($post_id){
    global $meta_box;

    // Check if our nonce is set.
    if ( ! isset( $_POST['jlewis_meta_box_nonce'] ) ) {
        return $post_id;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['jlewis_meta_box_nonce'], 'jlewis_save_meta_box_data' ) ) {
        return $post_id;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DEFINED_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = wp_kses_post($_POST[$field['id']]);

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}


// remove default text field
add_action('admin_menu', 'jlewis_remove_fields');

function jlewis_remove_fields() {
    remove_meta_box( 'content' , 'jlewis_portfolio' , 'advanced' );
}




function add_admin_scripts()
{
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_register_script( 'jlport_upload_image', $plugin_url . 'scripts/image_upload.js', false, '1.0.0' );
    wp_enqueue_script( 'jlport_upload_image' );
}

add_action('admin_enqueue_scripts', 'add_admin_scripts');

function add_frontend_scripts()
{
    $plugin_url = plugin_dir_url( __FILE__ );

//    wp_register_script( 'jlport_flickity', $plugin_url . 'scripts/flickity.js', false, '1.0.0' );
//    wp_enqueue_script( 'jlport_flickity' );
}

add_action('wp_enqueue_scripts', 'add_frontend_scripts');









/*  CUSTOM TAXONOMY DECLARATION - Skills  */

function create_skills_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Skills', 'taxonomy general name' ),
        'singular_name'     => _x( 'Skill', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Skills' ),
        'all_items'         => __( 'All Skills' ),
        'parent_item'       => __( 'Parent Skill' ),
        'parent_item_colon' => __( 'Parent Skill:' ),
        'edit_item'         => __( 'Edit Skill' ),
        'update_item'       => __( 'Update Skill' ),
        'add_new_item'      => __( 'Add New Skill' ),
        'new_item_name'     => __( 'New Skill Name' ),
        'menu_name'         => __( 'Skills' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'with_front' => false, 'slug' => 'skill' ),
    );

    register_taxonomy( 'skills', array( 'jlewis_project' ), $args );

}

add_action( 'init', 'create_skills_taxonomies' );


/* CUSTOM FIELD FOR SKILLS TAXONOMY  */

// Add term page
function add_skills_level_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[skills_level]"><?php _e( 'Skill Level', 'skills' ); ?></label>
        <select name="term_meta[skills_level]" id="term_meta[skills_level]">
            <option selected disabled hidden value=''></option>
            <option value="working">Working Knowledge</option>
            <option value="experienced">Experienced</option>
            <option value="advanced">Advanced</option>
        </select>
        <p class="description"><?php _e( 'How well do you know this skill?','skills' ); ?></p>
    </div>
    <?php
}
add_action( 'skills_add_form_fields', 'add_skills_level_field' );

// Edit term page
function edit_skills_level_field( $term ) {

    // put the term ID into a variable
    $term_id = $term->term_id;
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$term_id" );
    $skill_level = $term_meta['skills_level'] ? $term_meta['skills_level'] : '';
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[skills_level]"><?php _e( 'Skill Level', 'skills' ); ?></label></th>
        <td>
            <select name="term_meta[skills_level]" id="term_meta[skills_level]">
                <option disabled hidden value=''></option>
                <option value="working"<?php echo is_current_term($skill_level, "working"); ?>>Working Knowledge</option>
                <option value="experienced"<?php echo is_current_term($skill_level, "experienced"); ?>>Experienced</option>
                <option value="advanced"<?php echo is_current_term($skill_level, "advanced"); ?>>Advanced</option>
            </select>

            <p class="description"></p>
        </td>
    </tr>
    <?php
}
add_action( 'skills_edit_form_fields', 'edit_skills_level_field' );

function is_current_term($skill_level, $value){
    if($skill_level == $value){
        return " selected";
    }
}

// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_term_$t_id" );

        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "taxonomy_$t_id", $term_meta );
    }
}
add_action( 'edited_skills', 'save_taxonomy_custom_meta' );
add_action( 'create_skills', 'save_taxonomy_custom_meta' );



/*  CUSTOM TAXONOMY DECLARATION - Tools  */

function create_tools_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Tools', 'taxonomy general name' ),
        'singular_name'     => _x( 'Tool', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Tools' ),
        'all_items'         => __( 'All Tools' ),
        'parent_item'       => __( 'Parent Tool' ),
        'parent_item_colon' => __( 'Parent Tool:' ),
        'edit_item'         => __( 'Edit Tool' ),
        'update_item'       => __( 'Update Tool' ),
        'add_new_item'      => __( 'Add New Tool' ),
        'new_item_name'     => __( 'New Tool Name' ),
        'menu_name'         => __( 'Tools' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'with_front' => false, 'slug' => 'tool' ),
    );

    register_taxonomy( 'tools', array( 'jlewis_project' ), $args );

}

add_action( 'init', 'create_tools_taxonomies' );


/* CUSTOM FIELD FOR SKILLS TAXONOMY  */

// Add term page
function add_tools_level_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[tools_level]"><?php _e( 'Tool Level', 'tools' ); ?></label>
        <select name="term_meta[tools_level]" id="term_meta[tools_level]">
            <option selected disabled hidden value=''></option>
            <option value="working">Working Knowledge</option>
            <option value="experienced">Experienced</option>
            <option value="advanced">Advanced</option>
        </select>
        <p class="description"><?php _e( 'How well do you know this tool?','tools' ); ?></p>
    </div>
    <?php
}
add_action( 'tools_add_form_fields', 'add_tools_level_field' );

// Edit term page
function edit_tools_level_field( $term ) {

    // put the term ID into a variable
    $term_id = $term->term_id;
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$term_id" );
    $tool_level = $term_meta['tools_level'] ? $term_meta['tools_level'] : '';
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[tools_level]"><?php _e( 'Tool Level', 'tools' ); ?></label></th>
        <td>
            <select name="term_meta[tools_level]" id="term_meta[tools_level]">
                <option disabled hidden value=''></option>
                <option value="working"<?php echo is_current_term($tool_level, "working"); ?>>Working Knowledge</option>
                <option value="experienced"<?php echo is_current_term($tool_level, "experienced"); ?>>Experienced</option>
                <option value="advanced"<?php echo is_current_term($tool_level, "advanced"); ?>>Advanced</option>
            </select>

            <p class="description"></p>
        </td>
    </tr>
    <?php
}
add_action( 'tools_edit_form_fields', 'edit_tools_level_field' );

function is_current_term_tools($tool_level, $value){
    if($tool_level == $value){
        return " selected";
    }
}

// Save extra taxonomy fields callback function.
function save_taxonomy_tools_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_term_$t_id" );

        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "taxonomy_$t_id", $term_meta );
    }
}
add_action( 'edited_tools', 'save_taxonomy_tools_custom_meta' );
add_action( 'create_tools', 'save_taxonomy_tools_custom_meta' );



add_image_size( 'jlproj_desktop', 500, 300, array( 'center', 'top' ) );
add_image_size( 'jlproj_mobile', 180, 320, array( 'center', 'top' ) );


function include_template_function( $template_path ) {
	if ( get_post_type() == 'jlport_project' ) {
		if ( is_single() ) {
			// checks if the file exists in the theme first,
			// otherwise serve the file from the plugin
			if ( $theme_file = locate_template( array ( 'single-project.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . '/single-project.php';
			}
		}
	}
	return $template_path;
}

add_filter( 'template_include', 'include_template_function', 1 );


function add_modal_stylesheet()
{
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_register_style( 'jlport_modal', $plugin_url . 'css/modal.css', false, '1.0.0' );
    wp_enqueue_style( 'jlport_modal' );
}

add_action('wp_enqueue_scripts', 'add_modal_stylesheet');


function jlport_display_projects( $atts ) {
    $atts = shortcode_atts( array(
        'tag' => '',
        'order' => 'DESC',
        'modal' => true,
        'mobile' => false,
    ), $atts, 'projects_list' );

    $jlewis_project_args = array(
        'post_type' => 'jlewis_project',
        'order' => $atts['order'],
        'tag' => $atts['tag'],
    );

    $jlewis_projects = new WP_Query( $jlewis_project_args );

    if ( $jlewis_projects->have_posts() ) {

        $output = '<div class="project-list-all">';
        $modal = '';

        while ( $jlewis_projects->have_posts() ) {

            $jlewis_projects->the_post();

            if ( has_post_thumbnail() ) {
                $thumbnail = '<div class="project__image">' . get_the_post_thumbnail( null, 'jlproj_desktop' ) . '</div>';
             }

            $title = get_the_title();

            if ( $atts['modal'] ) {
                $modal_id = get_the_ID();
                
                $extras = "data-toggle='modal' data-target='#{$modal_id}'";

                $metadata = get_metadata('post', $modal_id);

                $desktop_img = get_the_post_thumbnail_url( null, 'jlproj_desktop');

                $mobile_img = $metadata['jlproj_mobile'][0];
                $mobile_menu_img = $metadata['jlproj_mobile_menu'][0];

                if ( $metadata['jlproj_url'] ) {
                    $project_link = "<div class='project_link'><a class='btn' href='{$metadata['jlproj_url'][0]}' target='_blank'>Visit Site</a></div>";
                } else {
                    $project_link = null;
                }
                
                $modal .= "
                    <div class='modal fade' id='$modal_id'>
                        <div class='modal-dialog modal-dialog-centered' role='document'>
                            <div class='modal-content'>
                                <div class='modal-header flex align-items-center'>
                                    <h3 class='modal-title text-center'>$title</h3>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                      <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>
                                <div class='modal-body'>
                                    <div class='project_previews flex flex-column-xs justify-content-around align-items-center'>
                                        <div class='laptop'>
                                            <div class='content' style='background-image: url($desktop_img)'>
                                                
                                            </div>
                                            <div class='btm'></div>
                                            <div class='shadow'></div>
                                        </div>
                                        <div class='phone'>
                                            <div class='screen' style='background-image: url($mobile_img)'>
                                                <div class='menu'><img src='$mobile_menu_img' /></div>
                                            </div>
                            
                                            <div class='shadow'></div>
                                        </div>
                                    </div>
                                    $project_link
                                </div>
                                
                                <div class='modal-footer'></div>
                                
                            </div>
                        </div>
                    </div>
                ";
                
            } else {
                $extras = '';
                $modal .= '';
            }

            $output .= "
                <div class='project' $extras>
                    $thumbnail
                    <div class='project__content flex justify-content-center align-items-center'>
                        <h3 class='project__title text-center'>$title</h3>
                    </div>
                </div>
            ";


/*            $output .= '<p class="text-center"><a href="<?php the_permalink(); ?>" class="btn btn-action">Read more</a></p>';*/

        }

        $output .= '</div>';

    } else {
        $output = '<p>No projects could be found!</p>';
    }

    return $output . $modal;
}
add_shortcode( 'projects_list', 'jlport_display_projects' );


?>