<?php

namespace Averta\WordPress\File;


class FileSystem
{

    protected $wpFilesystem;


    public function __construct()
    {
		global $wp_filesystem;

		if ( ! function_exists( 'get_filesystem_method' ) || empty( $wp_filesystem ) ) {
			require_once ( ABSPATH.'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$this->wpFilesystem = $wp_filesystem;
    }

    /**
     * Validates filesystem credentials.
     */
    public function validate( $url = null )
    {
        if ( get_filesystem_method() === 'direct' ) {}
		return true;
    }

    /**
     * Reads a file if exists.
	 *
     * @param string $filename File name.
     *
     * @return string
     */
    public function read( $filename )
    {
        if ( ! $this->validate() ){
			return false;
		}
        return $this->wpFilesystem->get_contents( $filename );
    }


	/**
	 * Creates and stores content in a file
	 *
	 * @param  string $file_location  The address that we plan to create the file in.
	 * @param  string $content    The content for writing in the file
	 *
	 * @return boolean            Returns true if the file is created and updated successfully, false on failure
	 */
    public function write( $file_location = '', $content, $chmod = 0644 )
    {
        if ( ! $this->validate() ){
			return false;
		}

		$_chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : $chmod;
		// Write the content, if possible
		if ( wp_mkdir_p( dirname( $file_location ) ) && ! $this->wpFilesystem->put_contents( $file_location, $content, $_chmod ) ) {
			// If writing the content in the file was not successful
			return false;
		} else {
			return true;
		}
    }

    /**
     * Whether the file/path exists or not.
     *
     * @param string $file   File name or file path.
     *
     * @return bool
     */
    public function exists( $file )
    {
        if ( ! $this->validate() ){
			return false;
		}

        return $this->wpFilesystem->exists( $file );
    }

    /**
     * Whether the path is a file or not.
     *
     * @return bool
     */
    public function isFile( $file )
    {
    	if ( ! $this->validate() ){
			return false;
		}
        return $this->wpFilesystem->is_file( $file );
    }

    /**
     * Whether the path is a directory or not.
     *
     * @return bool
     */
    public function isDir( $path )
    {
        if ( ! $this->validate() ){
			return false;
		}

		return $this->wpFilesystem->is_dir( $path );
    }

    /**
     * Creates a folder path recursively.
     *
     * @param string $path Path
     */
    public function mkdir( $path )
    {
        if ( ! $this->validate() ){
			return false;
		}

        return $this->wpFilesystem->mkdir( $path );
    }

    /**
     * Removes folder path and contents.
     * @since 0.9.0
     *
     * @global $wp_filesytem
     */
    public function rmdir( $path )
    {
		if ( ! $this->validate() ){
			return false;
		}

		return $this->wpFilesystem->rmdir( $path );
    }

}
