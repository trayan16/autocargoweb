<?php
function get_latest_posts_by_category($request) {
    $currentUser  = wp_get_current_user();

    $posts = get_posts(array(
        'numberposts'	=> -1,
        'post_type'		=> 'vehicles',
        'meta_query'	=> array(
            'relation'		=> 'AND',
            array(
                'key'	 	=> 'assigned_user',
                'value'	  	=> array($currentUser->id),
                'compare' 	=> 'IN',
            ),
        ),

    ));
    if (empty($posts)) {
    return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );

    }

    $response = new WP_REST_Response($posts);
    $response->set_status(200);

    return $response;
}
add_action('rest_api_init', function () {
    register_rest_route( '/wp/v2/', 'users-posts',array(
                  'methods'  => 'GET',
                  'callback' => 'get_latest_posts_by_category'
        ));
  });
?>