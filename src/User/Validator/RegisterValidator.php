<?php
namespace User\Validator;
use Zend\Validator\EmailAddress;

class RegisterValidator {
    public static function validate($form,$input)
    {
        $messages = [];
        $validator = new EmailAddress();
        if(!$validator->isValid($input['email'])){
            $messages[] = "Your Email Is Not Valid";
        }else{
            $form->getChildrenByCaptureTo('email')[0]->setValue($input['email']);
        }
        if($input['companyName'] == ''){
            $messages[] = "Your Company Name is required";
        }else{
            $form->getChildrenByCaptureTo('name')[0]->setValue($input['companyName']);
        }
        if($input['country'] == ''){
            $messages[] = "Your Country is required";
        }else{
            $form->getChildrenByCaptureTo('country')[0]->setValue($input['country']);
        }
        if($input['city'] == ''){
            $messages[] = "Your City is required";
        }
        if($input['phone'] == ''){
            $messages[] = "Your Phone Number is required";
        }
        if($input['country'] == ''){
            $messages[] = "Your Mobile Number is required";
        }
        return $messages;
    }
}