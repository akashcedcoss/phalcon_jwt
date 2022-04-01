<?php 
use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use App\Listeners\getdata;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;


class PermissionController extends Controller
{
    public function addpermissionAction()
    {
        echo '<pre>';
        $role = $this->request->getPost('role');
        $controllercheck = $this->request->getPost('controller');

        $compdata = new getdata();

        echo $role.'<br>';
        // echo $controllercheck;
        $actions =  $compdata->getMethod($controllercheck);
        // print_r($actions);

        
        $this->response->redirect('permission/methods');
    }

    public function methodsaddAction()
    {
        echo '<pre>';
        print_r($this->request->getPost());
        
        $roledb = $this->request->getPost()['roledb'];
        $controllerdb = $this->request->getPost()['controllerdb'];
        $actions = $this->request->getPost()['actions'];
        // echo $roledb." ".$controllerdb.'<br>' ;
        // print_r($actions);
        $mainarr = [];
        foreach ($actions as $key => $value) {
            $newarr = array(
                'role' => $roledb,
                'controller' => lcfirst(str_replace("Controller", "", $controllerdb)),
                'action' => str_replace("Action", "", $value)
            );
            array_push($mainarr, $newarr);
        }

        // print_r($mainarr);
        // die;

        foreach ($mainarr as $key => $value) {

            $permission = new Permission();

            $permission->assign(
                $value,
                [
                    'role',
                    'controller',
                    'action'
                ]
            );

        // Store and check for errors
             $permission->save();
        }

        $permissionTable = Permission::find();

        $acl = new Memory();
        $aclFile = APP_PATH.'/security/acl.cache';

        foreach ($permissionTable as $key => $value) {
            $acl->addRole($value->role);
            $acl->addComponent(
                $value->controller,
                [
                    $value->action
                ]
            );

            $acl->allow($value->role, $value->controller, $value->action);
        }

        $acl->allow('admin', '*', '*');
        file_put_contents(
            $aclFile,
            serialize($acl)
        );

    }
   
}