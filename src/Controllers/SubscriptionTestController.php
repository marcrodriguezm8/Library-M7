<?php
namespace App\Controllers;

use App\Registry;
use App\Model\Payment;
use App\Model\Subscription;
use App\Model\User;
use DateTime;
use DateInterval;

class SubscriptionTestController {
    // Constructor
    function __construct(){
        
    }

    // Display test data
    function index(){
        $data = [
            'name' => 'Marc',
            'card' => '12345678',
            'cvv' => '123',
            'user_id' => 1,
            'payment' => 'pay-trial-0',
        ];
        
        $session_sub = false;
        
        try{
            $user = new User('marc_roma', '12345', 'marc@gmail.com', 'reader', 1);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

        $this->subscribe($data, $session_sub, $user);
    }

    // Handle subscription logic
    function subscribe($data, $session_sub, $user){
        $card_fields = [
            'name' => $data['name'],
            'card' => $data['card'],
            'cvv' => $data['cvv'],
            'user_id' => $data['user_id']
        ];
        
        $userCard = Registry::get('database')
            ->selectAll('Cards')
            ->condition(['user_id'], 'Cards', [$data['user_id']], '=')
            ->get();
        
        if(sizeof($userCard) == 0){
            Registry::get('database')
                ->insert('Cards', $card_fields);
        }
        
        $type = explode('-', $data['payment']);
        if($type[0] == 'pay'){
            
            if($session_sub === false){
                $currentDate = new DateTime();
                
                if($type[1] == 'trial'){
                
                    $fields = [
                        'user_id' => $user->getId(),
                        'start_date' => $currentDate->format('Y-m-d'),
                        'finish_date' => $currentDate->add(new DateInterval('P1M'))->format('Y-m-d'),
                        'is_active' => 1,
                        'type' => 'trial',
                    ];
                    // Display the added subscription information
                    echo 'Added to the user ' . $user->getUsername() . ' the subscription ' . $fields['type'] . " ($type[2] €)";
    
                }
                else if($type[1] == 'month'){
                
                    $fields = [
                        'user_id' => $user->getId(),
                        'start_date' => $currentDate->format('Y-m-d'),
                        'finish_date' => $currentDate->add(new DateInterval('P1M'))->format('Y-m-d'),
                        'is_active' => 1,
                        'type' => 'month',
                    ];
                    // Display the added subscription information
                    echo 'Added to the user ' . $user->getUsername() . ' the subscription ' . $fields['type'] . " ($type[2] €)";
                    
                }      
                
            }

            else if($session_sub !== false){
               
                if($session_sub->getIsActive() == 0){
                    $currentDate = new DateTime();
                    
                    $fields = [
                        'user_id' => $user->getId(),
                        'start_date' => $currentDate->format('Y-m-d'),
                        'finish_date' => $currentDate->add(new DateInterval('P1M'))->format('Y-m-d'),
                        'is_active' => 1,
                        'type' => 'month',
                    ];
                    // Display the updated subscription information
                    echo 'Update the user ' . $user->getUsername() . ' the subscription ' . $fields['type'] . " ($type[2] €)";
                    
                }
                else {
                    if($type[1] == 'renew'){
                        $finish = new DateTime($session_sub->getFinishDate());
                        
                        $fields = [
                            'user_id' => $user->getId(),
                            'start_date' => $finish->format('Y-m-d'),
                            'finish_date' => $finish->add(new DateInterval('P1M'))->format('Y-m-d'),
                            'is_active' => 1,
                            'type' => 'month',
                        ];
                        // Display the updated subscription information
                        echo 'Update the user ' . $user->getUsername() . ' the subscription ' . $fields['type'] . " ($type[2] €)";
                    }
                }


            }
            $userSubscription = Registry::get('database')
                ->selectAll('Subscriptions')
                ->condition(['user_id'], 'Subscriptions', [$user->getId()], '=')
                ->get();

            
            try{
                $currentDate = new DateTime();
                $payment = new Payment($user->getId(), $type[2], $currentDate->format('Y-m-d'), $userCard[0]->card_id);
                
                $paymentFields = [
                    'user_id' => $payment->getUserId(),
                    'amount' => $payment->getAmount(),
                    'date' => $payment->getDate(),
                ];


                $subscription = new Subscription($userSubscription[0]->user_id, $userSubscription[0]->start_date, 
                $userSubscription[0]->finish_date, $userSubscription[0]->is_active, $userSubscription[0]->type);

                // Display subscription details
                echo "<br>The subscription started on " . $subscription->getStartDate() . " and ends on " . $subscription->getFinishDate();
            }
            catch(\Exception $e){
                echo $e->getMessage();
            }

        } 
    }
}

