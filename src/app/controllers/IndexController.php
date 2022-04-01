<?php

// include(APP_PATH . '/vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Http\Request;
use Phalcon\Escaper;
use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class IndexController extends Controller
{
    public function indexAction()
    {
        //
    }
    public function addproductAction()
    {
        //
    }
    public function addproductdetailsAction() {
        $products = new Products();
        $data = array(
            'name' => $this->escaper->escapeHtml($this->request->getPost('name')),
            'description' => $this->escaper->escapeHtml($this->request->getPost('description')),
            'tags' => $this->escaper->escapeHtml($this->request->getPost('tags')),
            'price' => $this->escaper->escapeHtml($this->request->getPost('price')),
            'stock' => $this->escaper->escapeHtml($this->request->getPost('stock')),

        );
        
        $products->assign(
            $data,
            [
                'name',
                'description',
                'tags',
                'price',
                'stock',
            ]
        );
        $products->save();

        $id = json_decode(json_encode($products))->id;
        $eventsManager = $this->di->get('EventsManager');
        $eventsManager->fire('notifications:setDefault', $this, $id);
        $this->response->redirect('/index');
    }

    public function listproductAction() {
        $this->view->productsfind = Products:: find();
    }
    public function orderplaceAction() {
        $this->view->productsfind = Products:: find();
    }

    public function placeorderAction() {
        $orders = new Orders();
        $data = array(
            'name' => $this->escaper->escapeHtml($this->request->getPost('name')),
            'address' => $this->escaper->escapeHtml($this->request->getPost('address')),
            'zipcode' => $this->escaper->escapeHtml($this->request->getPost('zipcode')),
            'product' => $this->escaper->escapeHtml($this->request->getPost('product')),
            'quantity' => $this->escaper->escapeHtml($this->request->getPost('quantity')),

        );

        $orders->assign(
            $data,
            [
                'name',
                'address',
                'zipcode',
                'product',
                'quantity',
            ]
        );
        $orders->save();
        $id = json_decode(json_encode($orders))->order_id;

        $eventsManager = $this->di->get('EventsManager');
        $eventsManager->fire('notifications:setDefaultZipcode', $this, $id);
        
        $this->response->redirect('/index');
    }
    public function listorderAction() {
        $this->view->listorder = Orders:: find();

    }
    public function settingsAction() {
        
    }

    public function settingsdataAction() {
        $settings = new Settings();
        $data = array(
            'title' => $this->escaper->escapeHtml($this->request->getPost('title')),
            'price' => $this->escaper->escapeHtml($this->request->getPost('price')),
            'stock' => $this->escaper->escapeHtml($this->request->getPost('stock')),
            'zipcode' => $this->escaper->escapeHtml($this->request->getPost('zipcode')),
        );

        $settings->assign(
            $data,
            [
            'title',
            'price',
            'stock',
            'zipcode',
            
             ]
        );
       
        $settingsupdate = Settings:: find();
        $settingsupdate[0]->title = $data['title'];
        $settingsupdate[0]->price = $data['price'];
        $settingsupdate[0]->stock = $data['stock'];
        $settingsupdate[0]->zipcode = $data['zipcode'];
        $settingsupdate[0]->save();
        $this->response->redirect('/index');
    }
    public function eventAction() {
        echo "Hello";
    }
    public function addroleAction() {

    }
    public function addroledataAction() {
        $role = new Role();
        $data = array(
            'role' => $this->escaper->escapeHtml($this->request->getPost('newrole')),
            
        );
        $role->assign(
            $data,
            [
            'role',
            ]
        );
        $crole = Role::find();
        if (count($crole)<3) {
            $role->save();
        }
        $this->response->redirect('/index');

        
    }
    public function addcomponentAction() {
        $components = array
        (
            'index' => ['listproduct', 'listorder'],
            'secure' => ['BuildACL'],
        );
        echo "<pre>";
        print_r($components);
        $this->view->component = $components;
        
    }
    public function adduserAction(){
        //
    }
    public function addusersAction() {
        // $request = new Request();
        // if (true === $request->isPost('submit')) {
            //     $username = $request->get('name');
        //     $check = $request->get('email');
        //     $password = $request->get('password');
        //     $newrole = $request->get('newrole');
        // }
        // echo $check;
        // $data = $this->model('Users')::find_by_username($username);
        // $data = Users::query()
        // ->insert
        // ->where("username = '$username'")
        // ->andWhere("password = '$password'")
        // ->execute();
        // print_r($data);
        // --------------- JWT Token Generator --------------------------------------///\
        // print_r($this->request->getPost());
        $newrole = $this->request->getPost()['newrole'];
        // echo $newrole;
        // die;
        

        // $signer  = new Hmac();

        // Builder object
        // $builder = new Builder($signer);
        
        $now        = new DateTimeImmutable();
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        
        // Setup
        // $builder
        // ->setAudience('https://target.phalcon.io')  // aud
        // ->setContentType('application/json')        // cty - header
        // ->setExpirationTime($expires)               // exp 
        // ->setId('abcd123456789')                    // JTI id 
        // ->setIssuedAt($issued)                      // iat 
        // ->setIssuer('https://phalcon.io')           // iss 
        // ->setNotBefore($notBefore)                  // nbf
        // ->setSubject($newrole)   // sub
        //     ->setPassphrase($passphrase)                // password 
        //     ;
            
        // // Phalcon\Security\JWT\Token\Token object
        // $tokenObject = $builder->getToken();

        $key = "example_key";

        $payload = array(
            "iss" => $this->url->getBaseUri(),
            "aud" => $this->url->getBaseUri(),
            "iat" => $issued,
            "nbf" => $notBefore,
            "exp" => $expires,
            "role" => $newrole
        );

        $jwt = JWT::encode($payload, $key, 'HS256');
        // print_r($jwt);
        // die();

        // // -----------------------generated -----------------------------------------------------
        
        
        // $escaper = new Escaper();
        
        $inputdata = array(
            "name" => $this->request->getPost('name'),
            "email" => $this->request->getPost('email'),
            "password" => $this->request->getPost('password'),
            "role" => $this->request->getPost('newrole'),
            'jwt' => $jwt
            
        );
       
        $user = new Users();
        $user->assign(
            $inputdata,
            [
                'name',
                'email',
                'password',
                'role',
                'jwt'
            ]
        );
        $user->save();
        echo '<pre>';      
        $this->response->redirect('index');
    }
    public function jwtAction() {

    }
}
