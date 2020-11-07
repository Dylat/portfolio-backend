<?php


Class Blog {


	/**
	 * 
	 * MysqliDB Instance 
	 * 
	 * @var MysqliDB
	 * 
	 */

	private $DB;


	/**
	 * 
	 * MySQL Table Name
	 * 
	 * @var String
	 * 
	 */


	private $table = "blog";



	/**
	 * 
	 * Allowed Fields 
	 * 
	 * @var Array
	 * 
	 */

	private $_allowed = array('id','title','slug','image','body', 'date');


	/**
	 * 
	 * Required Fields 
	 * 
	 * @var Array
	 * 
	 */

	private $_required = array('title', 'slug', 'body');



	public  function __construct(MysqliDB $DB) {
		$this->DB = $DB;
	}




	/**
	 * 
	 * Filters the Provided fields against $_allowed
	 * 
	 * @param 	Array 	$data Fields
	 * @return 	Array 
	 * 
	 **/

	private  function fields(Array $data): Array {

		$new_data = array();
		foreach($data as $key => $item) {
			if (in_array($key, $this->_allowed)) {
				$new_data[$key] = $item;
			}
		}


		return $new_data;
	}


	/**
	 * 
	 * Check for all required fields 
	 * 
	 * @param 	Array	$data Fields
	 * @return 	bool 
	 * 
	 **/

	private  function check_required_fields(Array $data): bool {

		$has_all_required_fields = count($data)  >= count($this->_required);


		if ($has_all_required_fields) {
			foreach($this->_required as $required) {
				if (!array_key_exists($required, $data)) {
					$has_all_required_fields = false;
				}
			}
		}

		return $has_all_required_fields;

	}


	/**
	 *
	 * Create Blog Post
	 *
	 *
	 * @param 	Array 	$data Post Data
	 * @return 	bool
	 *
	 */

	public function create(Array $data) {


		if (!array_key_exists('date', $data)) {
			$data['date'] = time();
		}


		$data = $this->fields($data);



		if ( $this->check_required_fields($data) ) {
			return $this->DB->insert($this->table, $data);
		}

		return false;
	}



	/**
	 *
	 * Return a list of Blog Posts
	 *
	 * @param 	Array 	$options Paramaeters(options for pagination/sorting) Data
	 * @return 	Array
	 *
	 */

	public function list($options = array()) {

		$posts = array();

		$default_options  = array(
			'pagination' => array(),
			'order' =>  'DESC',
			'orderby' => 'date'
		);
		$options = array_merge($default_options, $options);



		if ( in_array(strtoupper($options['order']), array('DESC', "ASC")) 
			 && count($this->fields(array( $options['orderby'] ))) > 0) {
			$this->DB->orderBy($options['orderby'], $options['order']);
		}


		if ( is_array($options['pagination']) && count($options['pagination']) > 0) {

			if (array_key_exists('limit', $options['pagination']) && array_key_exists('page', $options['pagination']) ) {
				$this->DB->pageLimit = intval($options['pagination']['limit']);
				$posts = $this->DB->arraybuilder()->paginate($this->table, $options['pagination']['page']);
			}

		}  else {
		    $posts = $this->DB->get($this->table);
		}

		return $posts;
	}


	/**
	 *
	 * Read a single Blog Post
	 *
	 * @param 	int 	$id Post ID
	 * @return 	array|MysqliDB
	 *
	 */

	public function read(int $id) {

		$this->DB->where('id', $id);
		return $this->DB->get($this->table);

	}



	/**
	 *
	 * Delete Post
	 *
	 * @param 	int 	$id Post ID
	 * @return 	bool
	 *
	 */

	public function delete(int  $id) {
		$this->DB->where('id', $id);
		return $this->DB->delete($this->table);
	}




	/**
	 *
	 * Read a single Blog Post
	 *
	 * @param 	int 	$id Post ID
	 * @param 	Array  		$data New Data
	 * @return 	bool|int
	 *
	 */
	public function update(int $id, Array $data) {

		$data = $this->fields($data);

		if ( $this->check_required_fields($data) ) {

			$this->DB->where('id', $id);
			return $this->DB->update($this->table, $data);
		}

		return false;
	}

}