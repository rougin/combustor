<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @name    	MY_Pagination.php
 * @version 	1.0
 * @author  	Joost van Veen www.accentinteractive.nl
 * @created 	Sun Jul 27 16:27:26 GMT 2008 16:27:26
 *
 * A simple Pagination extension to make working with pagination a bit easier.
 * I created this lib because I had URIs in my app in which the paging element 
 * was not always in the same segment, which makes it a pain if you work with 
 * the default pagination class.
 * 
 * This simple lib accomplishes the following:
 * - It determines waht the 'base_url' is, so you don't have to set it yourself
 * - It removes the need for you setting the infamous 'uri_segment' setting
 * 
 * Basically,it sets paging at the end of the uri, without having to pass a uri 
 * segment. The library relies on a unique pagination selector, which it uses to 
 * determine if and where the pagnition offset is located in the URI. 
 * 
 * E.g. /example/pagination/Page/3
 * 
 * The lib searches for the pagination_selector ('Page', in the above example) 
 * and retracts the proper offset value (in this case 3)
 * 
 * The pagination links are automatically created, just as in CI's default 
 * pagination lib. 
 *
 * Requirements
 * Codeigniter 2+
 * PHP 5
 * A *unique* pagination selector (default is 'Page') - unique meaning a string 
 * you are sure will never appear in the uri, except for pagination.
 * 
 * If there we use pagination, it must ALWAYS follow the following syntax and be
 * located at the END of the URI:
 * PAGINATION_SELECTOR/offset
 *
 * The PAGINATION_SELECTOR is a special string which we know will ONLY be in the
 * URI when paging is set. Let's say the PAGINATION_SELECTOR is 'Page' (since most
 * coders never use any capitals in the URI, most of the times any string with
 * a single capital character in it will suffice). 
 *
 * Example use (in controller):
 * 
 * // Initialize pagination
 * $config['total_rows'] = $this->db->count_all_results('my_table');
 * $config['per_page'] = 10; // You'd best set this in a config file, but hey
 * $this->pagination->initialize($config);
 * $this->data['pagination'] = $this->pagination->create_links();
 *
 * // Retrieve paginated results, using the dynamically determined offset
 * $this->db->limit($config['per_page'], $this->pagination->offset);
 * $query = $this->db->get('my_table');
 *
 */
class MY_Pagination extends CI_Pagination
{

	public $index_page;
	public $offset = 0;
	public $pagination_selector = 'page';

	/**
	 * Initialize the extension
	 */
	public function __construct()
	{
		parent::__construct();

		log_message('debug', "MY_Pagination Class Initialized");
		
		$this->index_page = config_item('index_page') != '' ? config_item('index_page') . '/' : '';
		$this->_set_pagination_offset();
	}

	/**
	 * Set dynamic pagination variables in $CI->data['pagvars']
	 */
	public function _set_pagination_offset()
	{
		/**
		 * Instantiate the CI super object so we have access to the uri class
		 */
		
		$CI = & get_instance();
		
		/**
		 * Store pagination offset if it is set
		 */

		if (strstr($CI->uri->uri_string(), $this->pagination_selector))
		{
			/**
			 * Get the segment offset for the pagination selector
			 */

			$segments = $CI->uri->segment_array();
			
			/**
			 * Loop through segments to retrieve pagination offset
			 */

			foreach ($segments as $key => $value)
			{
				/**
				 * Find the pagination_selector and work from there
				 */
				
				if ($value == $this->pagination_selector)
				{
					/**
					 * Store pagination offset
					 */
					
					$this->offset = $CI->uri->segment($key + 1);
					
					/**
					 * Store pagination segment
					 */
					
					$this->uri_segment = $key + 1;
					
					/**
					 * Set base url for paging. This only works if the
					 * pagination_selector and paging offset are AT THE END of
					 * the URI!
					 */
					
					$uri = $CI->uri->uri_string();
					$pos = strpos($uri, $this->pagination_selector);

					$this->base_url = config_item('base_url') .
						$this->index_page .
						substr($uri, 0, $pos + strlen($this->pagination_selector));
				}
			
			}
		
		}
		else
		{
			/**
			 * Pagination selector was not found in URI string. So offset is 0
			 */
			
			$this->offset      = 0;
			$this->uri_segment = 0;

			$this->base_url = config_item('base_url') .
				$this->index_page .
				$CI->uri->uri_string() . '/' .
				$this->pagination_selector;		
		}
	}

}