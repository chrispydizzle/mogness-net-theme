<?php
#build: premium, offline

/**
 * Returns name suggestions for a given image in json format.
 */
function imgevr_load_suggestions() { global $clipImages;
if ( in_array( $clipImages->license->type, array( 'free' ) ) ) {
 return; 
}

    if ( !current_user_can('edit_posts') ) return;
    
    // post id that the images is added to
    $postId = isset( $_POST['imgPostId'] ) ? intval($_POST['imgPostId']) : 0;
    // image attachment id, may be missed
    $imgId = isset( $_POST['imgId'] ) ? intval($_POST['imgId']) : 0;
    // current post title
    $postTitle = isset( $_POST['imgPostTitle'] ) ? trim( $_POST['imgPostTitle'] ) : null;
    
    if ( empty($postId) ) exit;
    
    $post = get_post($postId);
    $result = array();
    
    // if an image attachment id is specified then returns suggestions that include it,
    // otherwise the suggestions includes random label to make the ones unique
    if ( !empty($imgId) ) {
        
        // post title + image id
        if ( !empty($postTitle) ) {
            $result[] = sanitize_title( $postTitle ) . '-' . $imgId;
        }
        
        // attachemnt title + image id
        $attachment = get_post($imgId);
        $attacmentTitle = trim( $attachment->post_title );
        if ( !empty($attacmentTitle) && !factory_325_starts_with($attacmentTitle, 'img_') ) {
            $result[] = sanitize_title( $attacmentTitle ) . '-' . $imgId;
        };
        
        // categories + image id
        $categories = get_the_category( $postId );
        if ( $categories ) {
            foreach($categories as $category) {
                if ( !empty($postTitle) ) {
                    $result[] = sanitize_title( $postTitle ) . '-' . sanitize_title( $category->name ) . '-' . $imgId;  
                }
                $result[] = sanitize_title( $category->name ) . '-' . $imgId;
            }
        }
        
    } else {
        $rand = dechex( rand(10000,99999) );

        // post title + image id
        if ( !empty($postTitle) ) {
            $result[] = sanitize_title( $postTitle ) . '-' . $rand;
        }
        
        // categories + image id
        $categories = get_the_category( $postId );
        if ( $categories ) {
            foreach($categories as $category) {
                if ( !empty($postTitle) ) {
                    $result[] = sanitize_title( $postTitle ) . '-' . sanitize_title( $category->name ) . '-' . $rand;  
                } 
                $result[] = sanitize_title( $category->name ) . '-' . $rand;
            }
        }
    }
    
    while(count($result) > 7) {
        array_pop($result);
    }
    
    echo json_encode(array('items' => $result));
    exit;
}

 
/**
 * Renames the given image. May return a confirmation request.
 */
function imgevr_rename_image() { global $clipImages;
if ( in_array( $clipImages->license->type, array( 'free' ) ) ) {
 return; 
}

    if ( !current_user_can('edit_posts') ) return;
    
    // value of the 'src' attribute of a given image
    $imgUrl = trim( $_POST['imgUrl'] );
    // a new image name specified by a user
    $imgName = sanitize_title( trim( $_POST['imgName'] ) );
    // image attachment id, may be missed
    $imgId = isset( $_POST['imgId'] ) ? intval($_POST['imgId']) : 0;
    // post id where a given image is
    $postId = isset( $_POST['imgPostId'] ) ? intval($_POST['imgPostId']) : 0;
     // post id where a given image is
    $overwrite = ( isset( $_POST['imgOverwrite'] ) && $_POST['imgOverwrite'] ) ? true : false;   
    
    if ( empty($imgUrl) || empty($imgName) ) exit;
        
    // extracts relative image path from url
    $uploadData = wp_upload_dir();
    $siteUrl = trailingslashit( site_url() );
    
    // default 'wp-content/uploads/'
    $term = trailingslashit( str_replace($siteUrl, '', $uploadData['baseurl']) );
    $partPos = strpos($imgUrl, $term);

    if ( $partPos === false ) 
        factory_325_json_error('Sorry, the file for renaming has been not found on your server.');
    
    $relPath = substr($imgUrl, $partPos + strlen($term), strlen($imgUrl));
    $absPath = $uploadData['basedir'] . '/' . $relPath;
    $orgData = factory_325_pathinfo($relPath);
    
    if ( !is_file($absPath) ) 
        factory_325_json_error('Sorry, the file for renaming is not found on your server.');
    
    // if original file already has a given name
    if ( $orgData['basename'] == $imgName ) return;
    
    $newRelPath = $orgData['dirname'] . '/' . $imgName . '.' . $orgData['extension'];
    $newAbsPath = $uploadData['basedir'] . '/' . $newRelPath;
    $newAbsUrl = $uploadData['baseurl'] . '/' . $newRelPath;
    
    // checks if the file with the specified name already exist
    if ( is_file($newAbsPath) && !$overwrite ) {
        echo json_encode(array(
            'confirm' => 'The file <strong>' . $imgName . '.' . $orgData['extension'] . '</strong> already exists on the server. What to do?',
            'src' => $newAbsUrl
        ));
        exit;
    }
    
    // updates a post content to avoid the situation 
    // when the a user have renamed an images, but did't not save the post
    if ( !empty($postId) ) {
        $post = get_post( $postId );
        $content = str_replace($relPath, $newRelPath, $post->post_content);
        wp_update_post(array(
            'ID' => $post->ID,
            'post_content' => $content
        ));
    }
    
    // deletes the existing file if a user asked to overwrite it
    if ( is_file($newAbsPath) && $overwrite ) unlink($newAbsPath);
    
    // renames the file
    rename($absPath, $newAbsPath);

    // updates attachemnt data if the image id is specified
    if ( !empty($imgId) ) { 
        $data = array(
            'ID' => $imgId,
            'guid' => $newAbsUrl,
            'post_name' => $imgName,
            'post_title' => $imgName
        );
        wp_update_post( $data );
        $attach_data = wp_generate_attachment_metadata( $imgId, $newAbsPath );
        wp_update_attachment_metadata( $imgId, $attach_data );
        update_post_meta($imgId, '_wp_attached_file', $newRelPath);
    }
    
    echo json_encode(array('src' => $newAbsUrl));
    exit;
}

add_action('wp_ajax_imgevr_load_suggestions', 'imgevr_load_suggestions');
add_action('wp_ajax_imgevr_rename_image', 'imgevr_rename_image');