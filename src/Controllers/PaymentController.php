<?php
namespace App\Controllers;
use App\Request;
use App\Session;
use App\Controller;
use App\View;
use App\Registry;
use App\FormHandler;
use App\Model\User;
use DateTime;
use DateInterval;
use App\Model\Card;
use Exception;

class PaymentController extends Controller {
  
    // Constructor
    function __construct($session, $request)
    {
        parent::__construct($session, $request);
    }        
    
    // Display payment view
    function showPayment(){
        echo View::render('payment', [
            'subscription' => $this->session::getSession('subscription_type'),
            'type' => 'pay',
        ]);
    }

    // Cancel subscription and redirect to subscriptions page
    function cancel(){
        Registry::get('database')
            ->update('Subscriptions', ['is_active' => 0])
            ->condition(['user_id'], 'Subscriptions', [$this->session::getSession('user_data')->getId()], '=')
            ->get();
            
        $this->session::getSession('user_subscription')->setIsActive(0);
        header('Location:/subscriptions');
    }
}
