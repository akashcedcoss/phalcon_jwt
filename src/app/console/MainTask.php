<?php 
// declare(strict_types=1);

namespace App\Console;
include(APP_PATH . '/vendor/autoload.php');
use Firebase\JWT\JWT;
use Phalcon\Cli\Task;
use Phalcon\Http\Request;
use Phalcon\Escaper;
use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
// use Phalcon\Security\JWT\Validator;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }
    public function logAction()
    {
        unlink('../app/logs/log.log');
    }
    public function adminAction()
    {
        $signer  = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = new \DateTimeImmutable;
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $key = "key";

        $payload = array(
            "iss" => 'iss',
            "aud" => 'aud',
            "iat" => $issued,
            "nbf" => $notBefore,
            "exp" => $expires,
            "role" => 'admin'
        );
        // print_r($payload);
        $token = JWT::encode($payload, $key, 'HS256');
        echo $token;
    }
    public function stockAction($key=null, $val = null){
        if (isset($key)) {
            $setting = \Settings::findFirst(1) ?? new \Settings();
            if (strtolower($key) == "price") {
                $setting->price = $val;
                $setting->save();
                echo "Default price set to: " . \Settings::findFirst(1)->price;
                echo PHP_EOL;
            } elseif (strtolower($key) == "stock") {
                $setting->stock = $val;
                $setting->save();
                echo "Default stock set to: " . \Settings::findFirst(1)->stock;
                echo PHP_EOL;
            } else {
                echo "Error: key must be one of [price, stock] but '$key' found" . PHP_EOL;
                echo "Usage: settings [price/stock] [value]" . PHP_EOL;
            }
        } else {
            echo "Syntax Error" . PHP_EOL;
            echo "Usage: settings [price/stock] [value]" . PHP_EOL;
        }
    }
    public function lessAction(){
        $pro = \Products::find([
            'conditions'=>'stock < 10'
        ]);
        echo count($pro);
        echo PHP_EOL;
    }
    public function removecacheAction(){
        unlink('../app/security/acl.cache');
    }
    public function neworderAction(){
        $order = \Orders::findFirst([
            "order"=>'order_id desc'
        ]);
        echo $order;
        echo PHP_EOL;
    }

    
}