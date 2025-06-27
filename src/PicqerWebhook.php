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
    protected int $idhook;
    protected string $name;
    protected string $event;
    protected array $data;
    protected string $event_triggered_at;
    protected array $rawPayload;

    public function __construct(array $webhookPayload)
    {
        $this->rawPayload = $webhookPayload;
        
        $fieldsToParse = ['idhook', 'name', 'event', 'data', 'event_triggered_at'];
        
        foreach ($fieldsToParse as $field) {
            if (array_key_exists($field, $webhookPayload)) {
                $this->$field = $webhookPayload[$field];
            }
        }
    }
    
    public static function retrieve(): PicqerWebhook
    {
        $webhookPayloadRaw = file_get_contents('php://input');
        
        $webhookPayloadDecoded = json_decode($webhookPayloadRaw, true);
        
        if ($webhookPayloadDecoded === false) {
            throw new WebhookException('Could not decode webhook payload');
        }
        
        return new self($webhookPayloadDecoded);
    }
    
    public static function retrieveWithSecret($secret): PicqerWebhook
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

        $webhookPayloadDecoded = json_decode($webhookPayloadRaw, true);

        return new self($webhookPayloadDecoded);
    }

    public function getIdhook(): int
    {
        return $this->idhook;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getEventTriggeredAt(): string
    {
        return $this->event_triggered_at;
    }
}
