<?php

function cardstack_theme_setup() {

    global $content_width;
    if (!isset($content_width))
        $content_width = 655;

    register_nav_menu('cardstack_nav_menu', 'Top');

    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');

    $custom_header_args = array(
        'default-text-color' => '333',
        'uploads' => true,
    );
    add_theme_support('custom-header', $custom_header_args);

    add_theme_support('custom-background');
}

add_action('after_setup_theme', 'cardstack_theme_setup');

function cardstack_widgets_setup() {
    register_sidebars(1, array(
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''));
}

add_action('widgets_init', 'cardstack_widgets_setup');


function cardstack_enqueue_stuff() {

    if (is_singular() && comments_open()) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'cardstack_enqueue_stuff');

function cardstack_favicon() {
    $faviconUrl = get_theme_mod('favicon');
    if (!empty($faviconUrl)) {

        $fileNameExtension = pathinfo($faviconUrl, PATHINFO_EXTENSION);

        if ($fileNameExtension == 'png') {
            print('<link rel="shortcut icon" type="image/png" href="' . esc_url(get_theme_mod('favicon')) . '" />');
        } else if ($fileNameExtension == 'gif') {
            print('<link rel="shortcut icon" type="image/gif" href="' . esc_url(get_theme_mod('favicon')) . '" />');
        }
    }
}

add_action('wp_head', 'cardstack_favicon');

function cardstack_customize_css() {
    $custom_css = get_theme_mod('custom_css');
    if (!empty($custom_css)) {
        print('<style type="text/css">' . $custom_css . '</style>');
    }
}

add_action('wp_head', 'cardstack_customize_css');



/* ==== Filters ==== */

function cardstack_wp_title($title) {
    if (empty($title)) {
        return get_bloginfo('name');
    } else {
        $separator = get_theme_mod('custom_title_separator');
        if (empty($separator)) {
            $separator = ' | ';
        } else {
            $separator = ' ' . $separator . ' ';
        }
        $title_buffer = get_the_title() . $separator;
        $ancestors = get_post_ancestors(get_the_ID());
        foreach ($ancestors as $ancestor) {
            $title_buffer .= (get_the_title($ancestor) . $separator);
        }
        return $title_buffer . get_bloginfo('name');
    }
}

add_filter('wp_title', 'cardstack_wp_title');

function cardstack_remove_more_jump_link($url) { //prevent the more link from jumping to the break in the post
    $startIndex = strpos($url, '#more-');
    if ($startIndex) {
        $endIndex = strpos($url, '"', $startIndex);
    }
    if ($endIndex) {
        $url = substr_replace($url, '', $startIndex, $endIndex - $startIndex);
    }
    return $url;
}

add_filter('the_content_more_link', 'cardstack_remove_more_jump_link');



/* ==== Editor styles ==== */

function cardstack_add_editor_styles() {
    add_editor_style();
}

add_action('init', 'cardstack_add_editor_styles');



/* ==== Theme Customization API ==== */

function cardstack_customize_register($wp_customize) { //All sections, settings, and controls go here

    class cardstack_Customize_Textarea_Control extends WP_Customize_Control {

        public $type = 'textarea';

        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
            </label>
            <?php
        }

    }

    $wp_customize->add_section('cardstack_section', array(
        'title' => 'Card Stack theme',
        'priority' => 1337,
    ));

    $wp_customize->add_setting('custom_home_link_title', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_setting('custom_more_link_title', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_setting('custom_title_separator', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));

    $wp_customize->add_setting('custom_footer_text', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));

    $wp_customize->add_setting('slider_image_urls', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));

    $wp_customize->add_setting('custom_welcome_section', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));

    $wp_customize->add_setting('custom_html_below_menu', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post'
    ));

    $wp_customize->add_setting('custom_css', array(
        'default' => '',
        'sanitize_callback' => 'cardstack_sanitize_custom_css'
    ));

    $wp_customize->add_setting('favicon', array(
        'default' => ''
    ));

    $wp_customize->add_setting('post_breadcrumbs', array(
        'default' => false
    ));

    $wp_customize->add_setting('page_breadcrumbs', array(
        'default' => false
    ));

    $wp_customize->add_setting('hide_author', array(
        'default' => false
    ));

    $wp_customize->add_setting('hide_categories', array(
        'default' => false
    ));

    $wp_customize->add_setting('hide_tags', array(
        'default' => false
    ));

    $wp_customize->add_setting('hide_private_posts', array(
        'default' => false
    ));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'custom_home_link_title_ctrl', array(
        'label' => 'Custom home link text',
        'section' => 'cardstack_section',
        'settings' => 'custom_home_link_title',
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'custom_more_link_title_ctrl', array(
        'label' => 'Custom "read more" link text',
        'section' => 'cardstack_section',
        'settings' => 'custom_more_link_title',
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'custom_title_separator_ctrl', array(
        'label' => 'Custom title separator (used in the <title> tag)',
        'section' => 'cardstack_section',
        'settings' => 'custom_title_separator',
    )));

    $wp_customize->add_control(new cardstack_Customize_Textarea_Control($wp_customize, 'custom_footer_text_ctrl', array(
        'label' => 'Custom footer text or HTML',
        'section' => 'cardstack_section',
        'settings' => 'custom_footer_text',
    )));

    $wp_customize->add_control(new cardstack_Customize_Textarea_Control($wp_customize, 'slider_image_urls_ctrl', array(
        'label' => 'Slider image URLs (separate with |)',
        'section' => 'cardstack_section',
        'settings' => 'slider_image_urls',
    )));

    $wp_customize->add_control(new cardstack_Customize_Textarea_Control($wp_customize, 'custom_welcome_section_ctrl', array(
        'label' => 'Welcome section text or HTML',
        'section' => 'cardstack_section',
        'settings' => 'custom_welcome_section',
    )));

    $wp_customize->add_control(new cardstack_Customize_Textarea_Control($wp_customize, 'custom_html_below_menu_ctrl', array(
        'label' => 'Additional HTML to append to the menu',
        'section' => 'cardstack_section',
        'settings' => 'custom_html_below_menu',
    )));

    $wp_customize->add_control(new cardstack_Customize_Textarea_Control($wp_customize, 'custom_css_ctrl', array(
        'label' => 'Custom CSS',
        'section' => 'cardstack_section',
        'settings' => 'custom_css',
    )));

    $wp_customize->add_control(
            new WP_Customize_Image_Control(
            $wp_customize, 'favicon_ctrl', array(
        'label' => 'Favicon (PNG or GIF)',
        'section' => 'cardstack_section',
        'settings' => 'favicon'
            )
            )
    );

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'post_breadcrumbs_ctrl', array(
        'label' => 'Enable breadcrumb links for posts',
        'section' => 'cardstack_section',
        'settings' => 'post_breadcrumbs',
        'type' => 'checkbox'
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'page_breadcrumbs_ctrl', array(
        'label' => 'Enable breadcrumb links for pages',
        'section' => 'cardstack_section',
        'settings' => 'page_breadcrumbs',
        'type' => 'checkbox'
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'hide_author_ctrl', array(
        'label' => 'Hide post author',
        'section' => 'cardstack_section',
        'settings' => 'hide_author',
        'type' => 'checkbox'
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'hide_categories_ctrl', array(
        'label' => 'Hide post categories',
        'section' => 'cardstack_section',
        'settings' => 'hide_categories',
        'type' => 'checkbox'
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'hide_tags_ctrl', array(
        'label' => 'Hide post tags',
        'section' => 'cardstack_section',
        'settings' => 'hide_tags',
        'type' => 'checkbox'
    )));

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'hide_private_posts_ctrl', array(
        'label' => 'On the posts index page, show only published posts even to logged-in users',
        'section' => 'cardstack_section',
        'settings' => 'hide_private_posts',
        'type' => 'checkbox'
    )));
}

add_action('customize_register', 'cardstack_customize_register');

function cardstack_exclude_private_posts($query) { //option to exclude private posts from the loop so that they don't appear even to logged-in users
    if (get_theme_mod('hide_private_posts')) {
        if ($query->is_home() && $query->is_main_query()) {
            $query->set('post_status', 'publish');
        }
    }
}

add_action('pre_get_posts', 'cardstack_exclude_private_posts');

function cardstack_sanitize_custom_css($custom_styles) {
    $custom_styles = strip_tags($custom_styles);
    $custom_styles = str_replace('@import', '', $custom_styles);
    $custom_styles = str_replace('behavior', '', $custom_styles);
    $custom_styles = str_replace('expression', '', $custom_styles);
    $custom_styles = str_replace('binding', '', $custom_styles);
    return $custom_styles;
}
?>
