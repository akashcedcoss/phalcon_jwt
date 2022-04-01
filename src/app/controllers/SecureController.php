<?php
use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Role;
use Phalcon\Acl\Component;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class SecureController extends Controller {
public function createTokenAction() {
    $signer  = new Hmac();
    print_r($this->dispatcher->getParam());
    die;

// Builder object
    $builder = new Builder($signer);

    $now        = new DateTimeImmutable();
    $issued     = $now->getTimestamp();
    $notBefore  = $now->modify('-1 minute')->getTimestamp();
    $expires    = $now->modify('+1 day')->getTimestamp();
    $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';

// Setup
    $builder
    ->setAudience('https://target.phalcon.io')  // aud
    ->setContentType('application/json')        // cty - header
    ->setExpirationTime($expires)               // exp 
    ->setId('abcd123456789')                    // JTI id 
    ->setIssuedAt($issued)                      // iat 
    ->setIssuer('https://phalcon.io')           // iss 
    ->setNotBefore($notBefore)                  // nbf
    ->setSubject('my ')   // sub
    ->setPassphrase($passphrase)                // password 
;

// Phalcon\Security\JWT\Token\Token object
    $tokenObject = $builder->getToken();

// The token
echo $tokenObject->getToken();
die();

}

public function BuildACLAction() {
    $aclFile = APP_PATH. '/security/acl/cache';
    if(true !== is_file($aclFile)) {
        
        $acl = new Memory();

        $acl->addRole('manager');
        $acl->addRole('accounting');
        $acl->addRole('guest');
        $acl->addRole('admin');


        $acl->addComponent(
            'index',
            [
                'event'
                
            ]
        );

        $acl->allow('manager', 'index', 'event');
        $acl->allow('admin', '*', '*');

        $acl->deny('guest', '*', '*');

        file_put_contents(
            $aclfile,
            serialize($acl)
        );

    }else {
        $acl = unserialize(
            file_get_contents($aclFile)
        );
    }
    if (true === $acl->isAllowed('manager', 'index', 'event')) {
        echo "Access Granted :)";
    } else {
        echo "Access Denied :(";
    }
}
}