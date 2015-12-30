<?php

namespace User\EventHandler;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class EventHandler implements EventManagerAwareInterface
{

    protected $eventManager;
    protected $module;
    public function __construct($module)
    {
        $this->module = $module;
        $this->setListeners();
    }

    public function createLog($title,$text)
    {
        $this->getEventManager()->trigger('createLog',$this,array("title"=>$title,"text"=>$text));
    }

    public function activate()
    {
        $this->getEventManager()->trigger('activation.enable');
    }

    public function deactive()
    {
        $this->getEventManager()->trigger('activation.disable');
    }

    public function SMSService(){
        echo 'SmS was sent';
    }

    public function EmailService(){
        $message = new Message();
        $message->addTo('mohammadreza.jafari89@gmail.com')
            ->addFrom('info@samanezayeat.ir')
            ->setSubject('Greetings and Salutations!')
            ->setBody("Your account was activated successfully
                       User : Mreza
                       Password : 123456");

        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $transport->send($message);
    }

    private function setListeners(){
        $this->getEventManager()->attach('activation.enable', function ($e){
            $this->SMSService();
        });

        $this->getEventManager()->attach('activation.enable', function ($e){
            $this->EmailService();
        });

        $this->getEventManager()->attach('activation.disable', function ($e){
            $this->SMSService();
        });

        $this->getEventManager()->attach('activation.disable', function ($e){
            $this->EmailService();
        });

        $this->getEventManager()->attach('registration.registered', function ($e){
            var_dump(1);die;
            $message = new Message();
            $message->addTo('mohammadreza.jafari89@gmail.com')
                ->addFrom('info@samanezayeat.ir')
                ->setSubject('Greetings and Salutations!')
                ->setBody("Your are  registered successfully");

            // Setup SMTP transport using LOGIN authentication
            $transport = new SmtpTransport();
            $transport->send($message);
        });

    }

    public function registered()
    {
        $this->getEventManager()->trigger('registration.registered');
    }

    /**
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(
            get_called_class()
        ));

        $this->eventManager = $eventManager;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }
}