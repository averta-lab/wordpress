<?php
namespace Averta\WordPress\Database\Entity;

use Averta\Core\Utility\Arr;
use Averta\WordPress\Database\ORM\Connection;
use Averta\WordPress\Database\ORM\Query;
use TypeRocket\Models\Model as BaseModel;

class Model extends BaseModel
{
	/**
	 * Determines what fields should be updated automatically.
	 *
	 * @var array
	 */
	protected $autoFill = [];

	/**
     * Update resource fields
     *
     * @param array $fields
     *
     * @return mixed
     */
    public function update( $fields = [] )
    {
        $fields = $this->formatProperties( $fields );
        return parent::update( $fields );
    }

    /**
     * Create resource by fields
     *
     * When a resource is created the Model ID should be set to the
     * resource's ID.
     *
     * @param array $fields
     *
     * @return mixed
     */
    public function create( $fields = [] )
    {
        $fields = $this->formatProperties( $fields );
        return parent::create( $fields );
    }

    /**
     * Format properties
     *
     * @param array $fields
     *
     * @return array
     */
    public function formatProperties( $fields )
	{
    	$fields = Arr::merge( $fields, $this->autoFill );

        foreach( $fields as $name => $value ) {
            if( ! empty( $this->format[ $name ] ) ){
				if( method_exists( $this, $this->format[ $name ] ) ){
					$fields[ $name ] = call_user_func( [ $this, $this->format[ $name ] ], $value );
				} elseif( is_callable( $this->format[ $name ] ) ) {
            	    $fields[ $name ] = call_user_func( $this->format[ $name ], $value );
				}
			}
        }

        return $fields;
    }


    /**
     * Get Date Time
     *
     * @return bool|string
     */
    public function getDateTime()
    {
        return gmdate('Y-m-d H:i:s', time());
    }

    /**
     * @return \wpdb
     */
    protected function establishConnection()
    {
        $connection = Connection::getFromContainer();
        $name = $this->connection;

        if(!$name) {
            return $connection->default();
        }

        return $connection->get($name);
    }


    /**
     * @param \wpdb $wpdb
     * @return Query
     */
    public function setupQueryConnectionForModel(\wpdb $wpdb)
    {
        return (new Query)->setWpdb($wpdb);
    }

}