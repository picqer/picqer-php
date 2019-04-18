<?php

namespace Picqer\Api;

/**
 * Picqer PHP Webhook helper
 *
 * @author Casper Bakker <info@picqer.com>
 * @license http://creativecommons.org/licenses/MIT/ MIT
 */
class PicqerWebhook
{
    protected $idhook;
    protected $name;
    protected $event;
    protected $data;
    protected $event_triggered_at;

    public function __construct($webhookPayload)
    {
        $this->rawPayload = $webhookPayload;
        
        $fieldsToParse = ['idhook', 'name', 'event', 'data', 'event_triggered_at'];
        
        foreach ($fieldsToParse as $field) {
            if (array_key_exists($field, $webhookPayload)) {
                $this->$field = $webhookPayload[$field];
            }
        }
    }
    
    public static function retrieve()
    {
        $webhookPayloadRaw = file_get_contents('php://input');
        
        $webhookPayloadDecoded = json_decode($webhookPayloadRaw, true);
        
        if ($webhookPayloadDecoded === false) {
            throw new WebhookException('Could not decode webhook payload');
        }
        
        return new self($webhookPayloadDecoded);
    }
    
    public static function retrieveWithSecret($secret)
    {
        if (! isset($_SERVER) || ! array_key_exists('HTTP_X_PICQER_SIGNATURE', $_SERVER)) {
            throw new WebhookSignatureMismatchException('Could not find signature header in webhook');
        }
       
        $webhookPayloadRaw = file_get_contents('php://input');
        
        $signatureHeader = $_SERVER['HTTP_X_PICQER_SIGNATURE'];
        
        $calculatedSignature = base64_encode(hash_hmac('sha256', $webhookPayloadRaw, $secret, true));
        
        if (! hash_equals($calculatedSignature, $signatureHeader)) {
            throw new WebhookSignatureMismatchException('Signatures do not match');
        }
        
        return self::retrieve();
    }

    public function getIdhook()
    {
        return $this->idhook;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getEventTriggeredAt()
    {
        return $this->event_triggered_at;
    }
}
