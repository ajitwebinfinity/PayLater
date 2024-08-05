<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Cron;

use Spotii\Spotiipay\Model\Gateway;

/**
 * Class MerchantData
 * @package Spotii\Spotiipay\Model\Cron
 */
class MerchantData
{
    /**
     * @var Gateway\Transaction
     */
    protected $transaction;
    /**
     * @var Gateway\Heartbeat
     */
    protected $heartbeat;

    /**
     * MerchantData constructor.
     * @param Gateway\Transaction $transaction
     * @param Gateway\Heartbeat $heartbeat
     */
    public function __construct(
        Gateway\Transaction $transaction,
        Gateway\Heartbeat $heartbeat
    ) {
        $this->transaction = $transaction;
        $this->heartbeat = $heartbeat;
    }

    /**
     * Jobs for Spotii handshake
     */
    public function execute()
    {
        $this->transaction->sendOrdersToSpotii();
        $this->heartbeat->send();
    }
}
