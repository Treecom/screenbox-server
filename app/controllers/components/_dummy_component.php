<?php
 
/**
 *  Dummy component
 *  
 *  This is demo or dummy component. You can use it for create new component. Just rename all Dummy to component/model name and save as new php file.
 *
 *  @author Martin Bucko (bucko@oneclick.sk)
 *  @copyright  Martin Bucko 2010 
 *  @version 1.0
 *  @created 30.03.2010
 */

class DummyComponent extends Object {
    
	/**
	 * Component name
	 */
	var $name = "Dummy";
	
	/**
	 * Dummy model
	 */
	var $Dummy = null;
	
	/**
	 * __construct
	 * Constructor load model page
	 * @return 
	 */
 	function __construct(){		
		$this->Dummy =& ClassRegistry::init('Dummy');
	}
	
	/**
     * init
     * Used to initialize the components for current controller.
     * @params object Controller with components to load
     * @return void
     */
    function init(&$controller){}
    
    /**
     * initilize
     * The initialize method is called before the controller's beforeFilter method.
     * @params object $controller with components to initialize
     * @return void
     */
    function initialize(&$controller){}
    
    /**
     *  startup 
     *  The startup method is called after the controller's beforeFilter method but before the controller executes the current action handler.
     *  @params object $controller
     *  @return void
     */
    function startup(&$controller){}
    
    /**
     *  beforeRender 
     *  The beforeRender method is called after the controller executes the requested action's logic but before the controller's renders views and layout.
     *  @params object $controller
     *  @return void
     */
    function beforeRender(&$controller){}
 
	/**
	* beforeRedirect
	* The beforeRedirect method is invoked when the controller's redirect method is called but before any further action. 
	* If this method returns false the controller will not continue on to redirect the request. 
	* The $url, $status and $exit variables have same meaning as for the controller's method.
	* 
	* @param object $controller
	* @param string $url
	* @param mixed $status [optional]
	* @param boolean $exit [optional]
	* @return void 
	*/
	function beforeRedirect(&$controller, $url, $status=null, $exit=true){}
	   
 	/**
	 * shutdown
	 * The shutdown method is called before output is sent to browser.
	 * @param object $controller
	 * @return void
	 */
    function shutdown(&$controller){}
	

    
	/**
     *  getDummy
     *  [Element method]
     *  @params object Controller with components to shutdown
     *  @return array
     */
    function getDummy(&$controller, &$element) {   
      	return array(); 		
    }
  
 
    /* ------------ Admin functions --------------------- */
  
	/**
	 * admin_getDummy
	 * @param object $controller
	 * @return array
	 */
    function admin_getDummys(&$controller){
 		$opt = array();	 	
		$opt = $this->Dummy->setLimit($controller->params['form']);
			
		if (!empty($controller->params['form']['q'])){
			$opt['conditions'] = array('lower(Dummy.title) like'=>'%'.low($controller->params['form']['q']).'%');
		} else {
			$opt['conditions'] = array();
		}
		
		$data = $this->Dummy->find('all', $opt);
 		$data = $this->Dummy->itemUsers($data);
		
     	return array(
    			'count' => $this->Page->find('count', array('conditions' => $opt['conditions'])), /// ! only conditions can pass
     			'Dummys' => Set::extract($data, '{n}.Dummy')
    	);
    }
 
	/**
	 * admin_getDummyById
	 * @param object $controller
	 * @return array
	 */
    function admin_getDummyById(&$controller){
 		if ($controller->params['form']['id']>0){
 			$result =  $this->Dummy->findById(intval($controller->params['form']['id']));
 			if (isset($controller->params['form']['contexts'])){
				$CCRModel =& ClassRegistry::init('ContextCpRelation');
				$result['Dummy']['contexts'] = $CCRModel->find(
								'all',
								array('conditions'=>
										array('table_id'=>$controller->params['form']['id'], 'table'=>'Dummy')
								)
				);
				
				if (!empty($result['Dummy']['contexts'])){
					$result['Dummy']['contexts'] = Set::extract($result['Dummy']['contexts'], '{n}.ContextCpRelation.context_id' );
				}
			}
    		return array('success'=>true,'data'=>$result['Page']);
		} else {
			 return array('success'=>false);
		}
    }
 
	/**
	 * admin_setDummy
	 * @param object $controller
	 * @return array
	 */
    function admin_setDummy(&$controller){
     	$out = array('succes' => false, 'msg'=>__('There was an error saving data to server...',true));
     	return $out;    	
    }
	
	/**
	 * admin_deleteDummy
	 * @param object $controller
	 * @return array
	 */
	function admin_deleteDummy(&$controller){
		$out = array('success'=>false);	 
		$controller->loadModel('ContextCpRelation');
		$id = intval($controller->params['form']['id']);
		if ($id>0){
			$out['success'] = $this->Dummy->delete($id);
			$controller->ContextCpRelation->deleteAll(array('table'=>'Dummy','table_id'=>$id));
		}
		return $out;
	}
}
