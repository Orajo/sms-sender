<?php
/**
 * SmsZilla
 * PHP library for sending SMS through various gateways.
 * @link https://github.com/Orajo/sms-zilla Homepage
 * @copyright Copyright (c) 2016 Jarosław Wasilewski <orajo@windowslive.com>
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */

namespace SmsZilla;

use SmsZilla\Adapter\AdapterInterface;
use SmsZilla\MessageModel;

/**
 * Main worker class.
 * 
 * It sends SMS using given adapter class for handling choosen gateway.
 * Message should be configured using this class, becouse it provide additionl
 * validation and error handling. But if you want to, you can set your own 
 * message class implementing {@see MessageInterface}.
 * 
 * @see MessageInterface, Adapter\AdapterInterface
 * @author Jarosław Wasilewski
 */
class SmsSender implements SmsSenderInterface {

    protected $message = null;
    protected $adapter = null;
    
    protected $countryCode = '48';

    /**
     * Initialize Sender
     * @param AdapterInterface $adapter
     * @param array $params Adapter configuration
     */
    public function __construct(AdapterInterface $adapter, array $params = null) {
        $this->message = new MessageModel();

        $this->adapter = $adapter;
        if (is_array($params) && !empty($params)) {
            $this->adapter->setParams($params);
        }
    }

    /**
     * Sets message content
     * @param string $message
     * @param string $encoding Encoding name
     * @return SmsSender
     * @throws \InvalidArgumentException
     */
    public function setText($message, $encoding = 'UTF-8') {
        if (empty($message)) {
            throw new \InvalidArgumentException('SMS message cannot be empty');
        }
        if (!empty($encoding)) {
            if (extension_loaded('mbstring')) {
                $message = mb_convert_encoding((string) $message, $encoding);
            }
        }
        $this->message->setText($message);
        return $this;
    }

    /**
     * Gets message content
     * @return string
     */
    public function getText() {
        return $this->getMessage()->getText();
    }

    /**
     * Sets current message object
     * @param MessageInterface $message
     * @return SmsSender
     */
    public function setMessage(MessageInterface $message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Gets current message object
     * @return MessageModel
     */
    public function getMessage() {
        if ($this->message instanceof MessageInterface) {
            return $this->message;
        }
        return $this->mesage = new MessageModel();
    }

    /**
     * Add one recipient phone number
     * If $ignoreErrors flag is true then wrong numbers will be ommited and others will be added.
     * Phone number must be \d{9} or \d{11}
     * @param string|array $phoneNo Phone number or list of phone numbers
     * @param bool $ignoreErrors Flag to ignore errors in phone number
     * @return SmsSender
     */
    public function setRecipient($phoneNo, $ignoreErrors = true) {
        if (!is_array($phoneNo)) {
            $phoneNo = array($phoneNo);
        }

        foreach ($phoneNo as $number) {
            $number = trim($number);
            $number = preg_replace('/\s\-\+/', '', $number);
            if (preg_match('/^(\d{9}|\d{11})$/', $number)) {
                $number = strlen($number) == 9 ? $this->countryCode . $number : $number;
                $this->message->addRecipient($number);
            }
            elseif ($ignoreErrors) {
                continue;
            }
            else {
                throw new \BadMethodCallException('Phone number has incorrect format. It should be 9 or 11 digits');
            }
        }
        return $this;
    }

    /**
     * Gets recipients list
     * @return array
     */
    public function getRecipients() {
        return $this->getMessage()->getRecipients();
    }

    /**
     * Send message through given adapter (SMS gateway)
     * 
     * @uses Adapter\AdapterInterface Using specific adapter class
     * @return bool
     */
    public function send() {
        return $this->getAdapter()->send($this->getMessage());
    }

    /**
     * Gets adapter object
     * @return \SmsZilla\GeatewayInterface
     */
    public function getAdapter() {
        return $this->adapter;
    }
}
