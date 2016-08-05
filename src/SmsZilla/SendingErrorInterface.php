<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmsZilla;

/**
 *
 * @author Jarek
 */
interface SendingErrorInterface {
    
    public function __construct($recipient, $code, $message = '');
            
    public function getRecipient();
    
    public function getCode();
    
    public function getMessage();
}