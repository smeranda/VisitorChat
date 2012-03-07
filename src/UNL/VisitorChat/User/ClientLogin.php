<?php 
namespace UNL\VisitorChat\User;

class ClientLogin extends \UNL\VisitorChat\User\Record
{
    function __construct($options = array())
    {
        if (\UNL\VisitorChat\User\Record::getCurrentUser()) {
            \Epoch\Controller::redirect(\UNL\VisitorChat\Controller::$URLService->generateSiteURL("conversation", true, true));
        }
    }
    
    function getEditURL()
    {
        return \UNL\VisitorChat\Controller::$url . "clientLogin";
    }
    
    function handlePost($post = array())
    {
        if (!isset($post['initial_url']) || empty($post['initial_url'])) {
            throw new \Exception("No initial url was found", '400');
        }
        
        if (!isset($post['email']) || empty($post['email'])) {
            $post['email'] = null;
        }
        
        if (!isset($post['name']) || empty($post['name'])) {
            $post['name'] = "Anonymous";
        }
        
        if (!isset($post['message']) || empty($post['message'])) {
            throw new \Exception("No message was provided", '400');
        }
        
        if (!isset($post['email_fallback']) || empty($post['email_fallback'])) {
            $post['email_fallback'] = 0;
        } else {
            $post['email_fallback'] = 1;
        }
        
        $user = new self();
        $user->name         = $post['name'];
        $user->email        = $post['email'];
        $user->date_created = \UNL\VisitorChat\Controller::epochToDateTime();
        $user->type         = 'client';
        $user->max_chats    = 3;
        $user->status       = 'BUSY';
        $user->date_updated = \UNL\VisitorChat\Controller::epochToDateTime();
        $user->ip           = $_SERVER['REMOTE_ADDR'];
        $user->user_agent   = $_SERVER['HTTP_USER_AGENT'];
        
        $user->save();
        
        //Append a unique ID to the end of an annon user's name
        if ($user->name == "Anonymous") {
            $user->name = $user->name . $user->id;
            $user->save();
        }
        
        //Start up a new conversation for the user.
        $conversation = new \UNL\VisitorChat\Conversation\Record();
        $conversation->users_id       = $user->id;
        $conversation->initial_url    = $post['initial_url'];
        $conversation->status         = "SEARCHING";
        $conversation->email_fallback = $post['email_fallback'];
        $conversation->save();
        
        //Save the first message.
        $message = new \UNL\VisitorChat\Message\Record();
        $message->users_id = $user->id;
        $message->date_created = \UNL\VisitorChat\Controller::epochToDateTime();
        $message->conversations_id = $conversation->id;
        $message->message = $post['message'];
        $message->save();
        $user->ping();
        
        $_SESSION['id'] = $user->id;
    }
}