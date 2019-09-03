<?php
/**
 * Description of authentication
 *
 * @author fragote
 */
namespace Business\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Session\Container;

class Authentication extends AbstractPlugin
{
    protected $_acl;
    protected $_privilege;
    protected $appl;
    protected $sesAcl;
    
    public function __construct($appl, $privilege)
    {
        $this->appl = $appl;
        $this->_acl = new Acl();
        $this->_privilege = $privilege;
        $this->sesAcl = new Container('acl');
    }
    
    public function isAuthtenticated()
    {
        $controller = $this->getController();
        $controllerClass = get_class($controller);
        $namespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $moduleName = 'Dashboard';

        if ($controllerClass !== $namespace . "\Controller\LoginController" && $namespace === $moduleName){
            $auth = new AuthenticationService();
            if (!$auth->hasIdentity()) {
                $redirector = $controller->getPluginManager()->get('Redirect');
                return $redirector->toRoute(strtolower($namespace) . '-login');
            }

            $this->initAcl(strtolower($moduleName), $controller);
            $this->reviewAcl(strtolower($moduleName), $controller);
        }

        $this->sesAcl->permission = $this->_acl;
    }

    public function reviewAcl($module, $controller)
    {
       $route = $controller->getEvent()->getRouteMatch()->getMatchedRouteName();
       try {
          if (!$this->_acl->isAllowed($controller->identity()->role_id, $route)) {
             // failed, due to missing privileges
             $this->redirectPrivileges($module, $controller);
          }
       } catch (\Exception $e) {
          // failed, due to missing role or resource
            $this->redirectPrivileges($module, $controller);
       }
    }

    public function redirectPrivileges($module, $controller)
    {
       $response = $controller->getResponse();
       //location to page or what ever
       $response->getHeaders()->addHeaderLine('Location', '/' . strtolower($module) . '/');
       $response->setStatusCode(404);
    }

    public function initAcl($moduleName, $controller)
    {
       $roles = $this->_privilege->getAclMenu($controller->identity()->role_id);
       $allResources = [];
       foreach ($roles as $role => $resources) {

          $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
          $this->_acl->addRole($role);

          $allResources = array_merge($resources, $allResources);

          //adding resources
          foreach ($resources as $resource) {
             // Edit 4
             if(!$this->_acl->hasResource($resource))
                $this->_acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
          }
          //adding restrictions
          foreach ($allResources as $resource) {
             $this->_acl->allow($role, $resource);
          }
       }
    }
}
