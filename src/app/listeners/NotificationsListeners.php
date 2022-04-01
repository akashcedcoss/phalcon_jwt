<?php

namespace App\Listeners;
include(APP_PATH . '/vendor/autoload.php');
use IndexController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Orders;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Products;
use Settings;
use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class NotificationsListeners extends Injectable
{
    public function setDefault(
        Event $event,
        IndexController $component,
        $id
    ) {
        $product = Products::find($id);
        $settings = Settings::findFirst();

        if ($settings->title == 1) {
            $product[0]->name = $product[0]->name . $product[0]->tags;
            $product[0]->update();
        }

        if ($settings->price != null) {
            if ($product[0]->price == null || $product[0]->price == 0) {
                $product[0]->price = $settings->price;
                $product[0]->update();
            }
        }

        if ($settings->stock != null && ($product[0]->stock == null || $product[0]->stock == 0)) {
            $product[0]->stock = $settings->stock;
            $product[0]->update();
        }
    }

    public function setDefaultZipcode(
        Event $event,
        IndexController $component,
        $id
    ) {
        $settings = Settings::findFirst();
        $order = Orders::findFirst($id);

        if ($settings->zipcode != null && ($order->zipcode == null || $order->zipcode == 0)) {
            $order->zipcode = $settings->zipcode;
            $order->update();
        }
    }
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $aclFile = APP_PATH.'/security/acl.cache';

        if (true === is_file($aclFile)) {
            // $controller = $this->router->getControllerName();
            // $action = $this->router->getActionName();
            // echo $controller.' '.$action;
            // die;
            $acl = unserialize(file_get_contents($aclFile));
            // $role = $application->request->getQuery('role');
            // $controller = $application->router->getControllerName() ?? 'index';
            // $action = $application->router->getActionName() ?? 'index';

            // $role = $application->request->getQuery('role');
            
            if ($this->router->getControllerName()=="") {
                $controller = "index";
            } else {
                $controller = $this->router->getControllerName();
            }

            if ($this->router->getActionName()=="") {
                $action = "index";
            } else {
                $action = $this->router->getActionName();
            }
            $bearer = $application->request->get('bearer');
            if ($bearer) {
                try {
                    // $parser = new parser();
                    // $tokenobject = $parser->parse($bearer);
                    // $now        = new \DateTimeImmutable();
                    // $expires    = $now->getTimestamp();
                    // $validator = new Validator($tokenobject,100);
                    // $claims = $tokenobject->getClaims()->getPayload();
                    // $r = $claims['sub'];
                    $key = "example_key";
                    $decoded = JWT::decode($bearer, new Key($key, 'HS256'));
                    // print_r($decoded);
                    // die;
                    $decoded_array = (array) $decoded;
                    $r = $decoded_array['role'];
                    if (!$bearer || true !== $acl->isAllowed($r, $controller, $action)) {
                        echo 'Access Denied :( ';
                        die;
                    }

                }catch (\Exception $e) {
                    echo "Try Valid Token Please";
                    die;
                }
            } else {
                echo 'Token not found';
                die;
            }

                       
            // if (!$bearer || true !== $acl->isAllowed($r, $controller, $action)) {
            //     echo 'Access Denied :( ';
            //     die;
            // }
        } else {
            $acl = new Memory();

            $acl->addRole('admin');

            $acl->addComponent(
                'test',
                [
                    'eventtest'
                ]
            );

            $acl->allow('admin', '*', '*');
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        }
    }
}
