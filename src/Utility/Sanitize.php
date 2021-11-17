<?php
namespace Averta\WordPress\Utility;

class Sanitize
{
    /**
     * Sanitize title.
     *
     * @param string $input
     *
     * @return string
     */
    public static function title( $input )
    {
        return sanitize_title( $input );
    }

    /**
     * Sanitize slug.
     *
     * @param string $input
     *
     * @return string
     */
    public static function slug( $input )
    {
        return sanitize_title( static::dash( $input ) );
    }

	/**
     * Sanitize a textarea input field. Removes bad html like <script> and <html>.
     *
     * @param string $input
     *
     * @return string
     */
    public static function textarea( $input )
    {
        global $allowedposttags;
        return wp_kses( $input, $allowedposttags );
    }

    /**
     * Sanitize nothing.
     *
     * @param string $input
     *
     * @return string
     */
    public static function raw( $input )
    {
        return $input;
    }

    /**
     * Sanitize URL
	 *
	 * Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].
     *
     * @param string $url
     *
     * @return string
     */
    public static function url( $url )
    {
        return filter_var( $url, FILTER_SANITIZE_URL );
    }

    /**
     * Sanitize Number int
	 *
	 * Remove all characters except digits, plus and minus sign.
     *
     * @param string $input
     *
     * @return string
     */
    public static function int( $input )
    {
        return absint( filter_var( $input, FILTER_SANITIZE_NUMBER_INT ) );
    }

    /**
     * Sanitize Attribute.
     *
     * @param string $input
     *
     * @return string
     */
    public static function attribute( $input )
    {
        return esc_attr( $input );
    }
    /**
     * Sanitize SQL
     *
     * @param string $input
     *
     * @return string
     */
    public static function sql( $input )
    {
        return esc_sql( $input );
    }

    /**
     * Sanitize text as plaintext.
     *
     * @param string $input
     *
     * @return string
     */
    public static function plaintext( $input )
    {
        return wp_kses( $input, [] );
    }

    /**
     * Sanitizes a string from user input or from the database.
     *
     * @param string $input
     *
     * @return string
     */
    public static function textfield( $input )
    {
        return sanitize_text_field( $input );
    }

    /**
     * Strips out all characters that are not allowable in an email.
     *
     * @param string $input
     *
     * @return string
     */
    public static function email( $input )
    {
        return sanitize_email( $input );
    }

    /**
	 * Sanitizes an HTML classname to ensure it only contains valid characters.
     *
     * @param string $input
     *
     * @return string
     */
    public static function htmlClass( $input )
    {
        return sanitize_html_class( $input );
    }

    /**
     * Sanitizes a string key.
	 *
	 * Keys are used as internal identifiers. Lowercase alphanumeric characters, dashes, and underscores are allowed.
     *
     * @param string $input
     *
     * @return string
     */
    public static function key( $input )
    {
        return sanitize_key( $input );
    }

    /**
     * Sanitize editor data. Much like textarea remove <script> and <html>.
     * However, if the user can create unfiltered HTML allow it.
     *
     * @param string $input
     * @param bool $force_filter
     * @param bool $auto_p
     * @param null $allowed_tags
     *
     * @return string
     */
    public static function editor( $input, $force_filter = false, $auto_p = false, $allowed_tags = null )
    {
        if (current_user_can( 'unfiltered_html' ) && !$force_filter) {
            $output = trim( $input );
        } else {
            global $allowedtags;
            $output =  wp_kses( trim($input), apply_filters('averta/wordpress/sanitize/editor/tags', $allowed_tags ?? $allowedtags) );
        }

        if( $auto_p ) {
            $output = wpautop($output);
        }

        return $output;
    }

    /**
     * Sanitizes content for allowed HTML tags for post content.
     *
     * @param string $input Post content to filter.
     * @return string Filtered post content with allowed HTML tags and attributes intact.
     */
    public static function post($input)
    {
        return wp_kses_post($input);
    }

    /**
     *
     *
     * @param string $input HTML input
     * @param null|array $allowed_tags allowed tags for wp_kses
     * @param null|string $namespace
     * @param bool $auto_p
     *
     * @return string
     */
    public static function html( $input, $allowed_tags = null, $namespace = null, $auto_p = false ) {
        $tags = apply_filters('averta/wordpress/sanitize/html/tags/' . ($namespace ? $namespace : 'default'), $allowed_tags ? $allowed_tags : [
            'em' => [],
            'strong' => [],
            'small' => [],
            'sub' => [],
            'sup' => [],
            'b' => [],
            'i' => [],
            'ul' => [],
            'ol' => [],
            'hgroup' => [],
            'h1' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
            'table' => [],
            'tbody' => [],
            'tfoot' => [],
            'thead' => [],
            'dd' => [],
            'dt' => [],
            'dl' => [],
            'tr' => [],
            'th' => [],
            'td' => [],
            'figure' => [],
            'figcaption' => [],
            'caption' => [],
            'div' => [],
            'img' => [
                'src' => true,
                'alt' => true,
                'data-src'		=> true,
                'data-srcset'	=> true
            ],
            'video' => [
                'autoplay'    => true,
                'controls'    => true,
                'height'      => true,
                'loop'        => true,
                'muted'       => true,
                'playsinline' => true,
                'poster'      => true,
                'preload'     => true,
                'src'         => true,
                'width'       => true,
            ],
            'a' => [
                'id'	=> true,
                'class'	=> true,
                'href' => true,
                'title' => true,
                'rev'      => true,
                'rel' => true,
                'target' => true,
                'download' => ['valueless' => 'y'],
            ],
            'li' => [],
            'blockquote' => [],
            'cite' => [],
            'code' => [],
            'hr' => [],
            'p' => [],
            'br' => [],
            'link'	=> [
                'rel'	=> true,
                'id'	=> true,
                'href'	=> true,
                'media'	=> true
            ],
            'script'	=> [
                'id'	=> true,
                'src'	=> true
            ],
            'style'	=> [
                'type'	=> true
            ],
            'meta'	=> [
                'charset' 	=> true,
                'name'		=> true,
                'content'	=> true
            ],
            'body'	=> [
                'class'	=> true,
            ]
        ]);

        $output = trim(wp_kses(trim($input), $tags));

        if( $auto_p ) {
            $output = wpautop( $output );
        }

        return $output;
    }

    /**
     * Sanitize Hex Color Value
     *
     * If the hex does not validate return a default instead.
     *
     * @param string $hex
     * @param string $default
     *
     * @return string
     */
    public static function hex( $hex, $default = '#000000' )
    {
        if ( preg_match("/^\#?([a-fA-F0-9]{3}){1,2}$/", $hex ) ) {
            return $hex;
        }

        return $default;
    }

    /**
     * Sanitize Underscore
     *
     * Remove all special characters and replace spaces and dashes with underscores
     * allowing only a single underscore after trimming whitespace form string and
     * lower casing
     *
     * ` --"2_ _e''X  AM!pl'e-"-1_@` -> _2_ex_ample_1_
     *
     * @param string $name
     * @param bool $keep_dots
     *
     * @return mixed|string
     */
    public static function underscore( $name, $keep_dots = false )
    {
        if (is_string( $name )) {

            if($keep_dots) {
                $name = preg_replace( '/[\.]+/', '.', $name );
                $name = preg_replace("/[^A-Za-z0-9\.\\s\\-\\_?]/",'', strtolower(trim($name)) );
            } else {
                $name = preg_replace( '/[\.]+/', '_', $name );
                $name = preg_replace("/[^A-Za-z0-9\\s\\-\\_?]/",'', strtolower(trim($name)) );
            }


            $name = preg_replace( '/[-\\s]+/', '_', $name );
            $name = preg_replace( '/_+/', '_', $name );
        }

        return $name;
    }

    /**
     * Sanitize Dash
     *
     * Remove all special characters and replace spaces and underscores with dashes
     * allowing only a single dash after trimming whitespace form string and
     * lower casing
     *
     * ` --"2_ _e\'\'X  AM!pl\'e-"-1_@` -> -2-ex-ample-1-
     *
     * @param string $name
     *
     * @return mixed|string
     */
    public static function dash( $name )
    {
        if (is_string( $name )) {
            $name = preg_replace( '/[\.]+/', '_', $name );
            $name = preg_replace("/[^A-Za-z0-9\\s\\-\\_?]/",'', strtolower(trim($name)) );
            $name = preg_replace( '/[_\\s]+/', '-', $name );
            $name = preg_replace( '/-+/', '-', $name );
        }

        return $name;
    }

    /**
	 * Sanitizes a filename, replacing whitespace with dashes.
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	public static function fileName( $filename ){
    	return sanitize_file_name( $filename );
    }

}
