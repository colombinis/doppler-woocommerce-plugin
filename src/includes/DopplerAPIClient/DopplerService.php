<?php

if( ! class_exists( 'Woo_Doppler_Service' ) ) :

class Woo_Doppler_Service
{

  private $config;

  private $resources;

  private $httpClient;

  private $errors;

  function __construct($credentials = null) {
    
    $this->config = ['credentials' => []];

    $this->error = 0;

    $usr_account = '';

    if ($credentials)
      $this->setCredentials($credentials);

    if(isset($config['credentials'][ 'user_account'])){
      $usr_account = $config['credentials'][ 'user_account'] . '/';
    }

    $this->baseUrl = 'http://newapiqa.fromdoppler.net/accounts/' . $usr_account;

    $this->resources = [
	  'home'	=> new Doppler_Service_Home_Resource(
	    $this,
		array(
		  'methods' => array(
		  	'get' => array(
				'route' => '',
				'httpMethod' => 'get',
				'parameters' => null
			)
		  )
		)
	  ),
      'lists'   => new Doppler_Service_Lists_Resource(
        $this,
        array(
          'methods' => array(
            'get' => array(
              'route'        => 'lists/:listId',
              'httpMethod'  => 'get',
              'parameters'  => array(
                'listId' => array(
                  'on_query_string' => false,
                )
              )
            ),
            'list' => array(
              'route'       => 'lists',
              'httpMethod'  => 'get',
              'state' => 'active',
              'parameters'  => array(
                'page' => array(
                  'on_query_string' => true
                ),
                'per_page' => 200
              )
            ),
            'new' => array(
              'route' => 'lists',
              'httpMethod' => 'post',
              'parameters' => array()
            ),
            'delete' => array(
              'route' => 'lists/:listId',
              'httpMethod' => 'delete',
              'parameters'  => array(
                'listId' => array(
                  'on_query_string' => false,
                )
              )
            )
          )
        )
      ),
      'fields'  => new Doppler_Service_Fields(
        $this,
        array(
          'methods' => array(
            'list' => array(
              'route'       => 'fields',
              'httpMethod'  => 'get',
              'parameters'  => null
            )
          )
        )
      ),
      'subscribers'  => new Doppler_Service_Subscribers(
        $this,
        array(
          'methods' => array(
            'post' => array(
              'route'       => 'lists/:listId/subscribers',
              'httpMethod'  => 'post',
              'parameters'  => array(
                'listId' => array(
                  'on_query_string' => false,
                )
              )
            ),
            'get' => array(
              'route'       => 'lists/:listId/subscribers',
              'httpmethod'  => 'get',
              'parameters'  => array(
                'listId' => array(
                  'on_query_string' => false,
                )
              )
            ),
            'import' => array(
              'route'   => 'lists/:listId/subscribers/import',
              'httpMethod'  => 'post',
              'parameters'  => array(
                'listId' => array(
                  'on_query_string' => false,
                )
              )
            )
          )
        )
      )
    ];
  }

  public function setCredentials($credentials) {

    $this->config['credentials'] = array_merge($credentials, $this->config['credentials'] );
    $connectionStatus = $this->connectionStatus();
    
    switch($connectionStatus['response']['code']) {
      case 200:
        return true;
        break;
      case 401:
        //TODO: Return formated error 
        return false;
        break;
      case 403:
        //TODO: Return formated error
        return false;
        break;
    }


  }

  public function connectionStatus() {
    $response = $this->call(array('route' => '', 'httpMethod' => 'get'));
	   return $response;
  }

  function call( $method, $args=null, $body=null ) {
    //$url = 'https://restapi.fromdoppler.com/accounts/'. $this->config['credentials']['user_account'] . '/';
    $url = 'http://newapiqa.fromdoppler.net/accounts/' . $this->config['credentials']['user_account'] . '/';
    $url .= $method[ 'route' ];
    $query = "";
    
    if( $args && count($args)>0 ){
      
      $resourceArg = $method[ 'parameters' ];
      
      foreach ($args as $name => $val) {
        
        isset($resourceArg[ $name ])? $parameter = $resourceArg[ $name ] : $parameter = ''; 
        
        if( $parameter && $parameter[ 'on_query_string' ] ){
          $query .= $name . "=" . $val ;
        }else{
          $url = str_replace(":".$name, $val, $url);
        }
      
      }

      if(isset($resourceArg["per_page"])){
        $url.="?per_page=".$resourceArg["per_page"];
      }

      if(isset($resourceArg["page"])){
        $url.='&'.$query;
      }

    }

    $headers=array(
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "X-Doppler-Subscriber-Origin" => "Wordpress",
            "Authorization" => "token ". $this->config["credentials"]["api_key"]
             );
    $response = "";

    try{

      switch($method['httpMethod']){
        
        case 'get':
            
            $response = wp_remote_get($url, array(
              'headers'=>$headers,
              'timeout' => 12
            ));
            break;
        
        case 'post':
           
            $response = wp_remote_post($url, array(
              'headers'=>$headers,
              'timeout' => 12,
              'body'=> json_encode($body)
            ));
            break;

        case 'delete':
           
            $response = wp_remote_request($url, array(
              'method' => 'DELETE',
              'headers'=>$headers,
              'timeout' => 12,
              'body'=> json_encode($body)
            ));
            break;

      }

      if($response['response']['code']>='400'){
        throw new Exception($response['response']['code'].' - '.$response['response']['message']);
      }

      if(empty($response)){
        throw new Exception('Error.');
      }

    }
    catch(\Exception $e){
      $this->throwConnectionErr($e->getMessage());
      return;
    }

    return $response;

  }

  function getResource( $resourceName ) {
    return $this->resources[ $resourceName ];
  }

  function throwConnectionErr($msg) {
      if($this->error == 0):
      ?>
      <div class="notice notice-error">
				<p>
					<b>Doppler Forms:</b> Connection error. <?php echo $msg ?>. Please contact support.
				</p>
			</div>
      <?php
      endif;
      $this->error = 1;
 }

}

endif;

  /**
   * These classes represent the different resources of the API.
   */

  
if( ! class_exists( 'Doppler_Service_Home_Resource' ) ) :

  class Doppler_Service_Home_Resource {
    
  	private $service;

    private $client;

    private $methods;

	function __construct( $service, $args )
    {
      $this->service = $service;
      $this->methods = isset($args['methods']) ? $args['methods'] : null;
    }

	public function getUserAccount(){
    $method = $methods['get'];
      return $this->service->call($method, array());
	  }
  }

endif;

if( ! class_exists( 'Doppler_Service_Lists_Resource' ) ) :

  class Doppler_Service_Lists_Resource {

    private $service;

    private $client;

    private $methods;

    function __construct( $service, $args )
    {
      $this->service = $service;
      $this->methods = isset($args['methods']) ? $args['methods'] : null;
    }

    public function getList( $listId ){
  
      $method = $this->methods['get'];
      return json_decode($this->service->call($method, array("listId" => $listId))['body']);
    
    }

    /**
     * Get all lists recursively
     */
    public function getAllLists( $listId = null, $lists = [], $page = 1  ) {
      
      $method = $this->methods['list'];
      $z = json_decode($this->service->call($method, array("listId" => $listId, 'page' => $page))['body']);
      $lists[] = $z->items;

      if($z->currentPage < $z->pagesCount && $page<4){
        $page = $page+1;
        return $this->getAllLists(null, $lists, $page);
      }else{
        return $lists;
      }
      
    }

    public function getListsByPage( $page = 1 ) {

      $method = $this->methods['list'];
      $z = json_decode($this->service->call($method, array("listId" => null, 'page' => $page))['body']);
      return $z->items;

    }

    public function saveList( $list_name ) {
      
      if(!empty($list_name)):
        $method = $this->methods['new'];
        return $this->service->call( $method, null, array('name'=>$list_name)  );
      endif;
    
    }

    public function deleteList($list_id) {
      
      if(!empty($list_id)):
        $method = $this->methods['delete'];
        return $this->service->call( $method, array('listId'=>$list_id) );
      endif;
    
    }
    
  }

endif;


if( ! class_exists( 'Doppler_Service_Fields' ) ) :

  class Doppler_Service_Fields {

    private $service;

    private $client;

    private $methods;

    function __construct( $service, $args )
    {
      $this->service = $service;
      $this->methods = isset($args['methods']) ? $args['methods'] : null;
    }

    public function getAllFields( $listId = null ){
      $method = $this->methods['list'];
      return json_decode($this->service->call($method, array("listId" => $listId) )['body']);
    }

  }

endif;

if( ! class_exists( 'Doppler_Service_Subscribers' ) ) :

  class Doppler_Service_Subscribers {

    private $service;

    private $client;

    private $methods;

    function __construct( $service, $args )
    {
      $this->service = $service;
      $this->methods = isset($args['methods']) ? $args['methods'] : null;
    }

    public function addSubscriber( $listId, $subscriber ){
      $method = $this->methods['post'];
      return $this->service->call( $method, array( 'listId' => $listId ),  $subscriber );
    }

    public function importSubscribers($listId, $subscribers){
      $method = $this->methods['import'];
      return $this->service->call( $method, array( 'listId' => $listId ), $subscribers);
    }

    public function getSubscribers( $listId, $page = 1 ) {
      
      /*
      $method = $this->methods['list'];
      $z = json_decode($this->service->call($method, array("listId" => $listId, 'page' => $page))['body']);
      $lists[] = $z->items;

      if($z->currentPage < $z->pagesCount && $page<4){
        $page = $page+1;
        return $this->getAllLists(null, $lists, $page);
      }else{
        return $lists;
      }*/

      $method = $this->methods['get'];
      return $this->service->call( $method, array( 'listId' => $listId ) );
    }

  }

endif;

if( ! class_exists( 'Doppler_Exception_Invalid_Account' ) ){
  class Doppler_Exception_Invalid_Account extends Exception {};
}

if( ! class_exists( 'Doppler_Exception_Invalid_APIKey' ) ){
  class Doppler_Exception_Invalid_APIKey extends Exception {};
}

?>