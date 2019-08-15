<?php
/**
 * @package Opheham-Latest-Posts
 * @version dev
 */
/*
Plugin Name: Opheham Lastest Posts
Plugin URI : https://opheham.fr/latest-post/
Description: Plugin pour les multi sites. Ajoute un shortcode permettant d'ajouter un bloc affichant les 5 derniers articles du blog d'id passé en paramètres.
Author:      Olivier Fillol
Author URI:  https://opheham.fr/olivier-fillol
Version:     dev
*/

defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );

function latestPost($atts) {

    extract(shortcode_atts(
        array(
            'id' => 2
        ),
        $atts
    ));

    switch_to_blog($id);

    $current_blog = get_blog_details();

    $blogLink = '<a href="' . $current_blog -> siteurl . '" class="blog-latest-posts-title" title="Accéder au site : ' . $current_blog -> blogname . '">';

    $blogHeader = array();

    $blogHeader['title'] = '<h1 class="blog-title">'. $blogLink . $current_blog -> blogname . '</a></h1>';

    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    
    if ($image) {
        $blogHeader['logo'] = $blogLink . '<img src="' . $image[0] . '" alt="Le logo du blog" /></a>';
    }

    // fetch all the posts 
    $blog_posts = get_posts(array('posts_per_page' => -1));

    if (count($blog_posts) === 0) {
        $blogContent =  '<p class="error-msg">Il n\'y a pour l\'instant aucun article pour ce blog.</p>';
    } else {
        $blogContent = '<ul class="blog-latest-posts-content">';
        
        foreach ($blog_posts as $key => $post) {
            $blogContent .=  "<li class=\"child-list\">";
            $blogContent .=  get_the_post_thumbnail($post -> ID);
            $blogContent .=   "<a href=\"" . $current_blog -> siteurl . "/" . $post -> post_name . "\">" . get_the_title($post -> ID) . "</a>";
            $blogContent .=  "</li>";
        }

        $blogContent .= "</ul>";
    }

    restore_current_blog();
    
    return build_html($blogHeader, $blogContent);
}

function build_html($blogHeader, $blogContent)
{
    $html  = '<div class="blog-latest-posts">';
    $html .= $blogHeader['title'];
    if (isset($blogHeader['logo'])) {
        $html .= $blogHeader['logo'];
    }
    $html .= $blogContent . '</div>';

    return $html;
}

function plugin_enqueue_styles() {
    wp_enqueue_style( 'plugin-styles',  plugins_url( 'scss/styles.css', __FILE__ ), array(), null );   // The child theme stylesheet
}

// Insertion de la feuille de styles
add_action( 'wp_enqueue_scripts', 'plugin_enqueue_styles' );

// Création du shortcode
add_shortcode('showBlogLatestPosts', 'latestPost');
