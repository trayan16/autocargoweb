<?php
function getUserVehicles($request) {
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
    foreach($posts as $post) {
        $fieldObjects = get_field_objects($post->ID);
        foreach($fieldObjects as $key => $fieldObject) {
            $post->$key = get_field($fieldObject['photos'], $post->ID);
        }
        $photos =  explode(',', get_field('photos', $post->ID)); 
        $post->images = [];
        foreach ($photos as $photo) {
            $post->images[] = wp_get_attachment_url($photo);
        }
        
    }
    if (empty($posts)) {
    return new WP_Error( 'empty_category', 'there is no post in this category', array('status' => 404) );

    }

    $response = new WP_REST_Response($posts);
    $response->set_status(200);

    return $response;
}
add_action('rest_api_init', function () {
    register_rest_route( '/wp/v2/', 'userVehicles',array(
                  'methods'  => 'GET',
                  'callback' => 'getUserVehicles'
        ));
  });
?>