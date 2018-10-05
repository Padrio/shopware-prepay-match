<?php

namespace PrepayMatch\Components;

/**
 * @author Pascal Krason <p.krason@padr.io>
 */
final class Config
{
    const TYPE_LOCAL = 'local';
    const TYPE_REMOTE = 'remote';

    /**
     * @var string
     */
    public $apiType;

    /**
     * @var array
     */
    public $prepayPaymentMethods = [];

    /**
     * @var string
     */
    public $proxyHost;

    /**
     * @var string
     */
    public $proxySecret;

    /**
     * @var string
     */
    public $fintsServer;

    /**
     * @var integer
     */
    public $fintsPort;

    /**
     * @var string
     */
    public $fintsBankCode;

    /**
     * @var string
     */
    public $fintsUsername;

    /**
     * @var string
     */
    public $fintsPin;

    /**
     * @var string
     */
    public $fintsAccountToCheck;

    /**
     * @var array
     */
    public $paymentIdsToCheck = [];

    /**
     * @var array
     */
    public $statusIdsToCheck = [];

    /**
     * @var integer
     */
    public $statusOnMatch;

    /**
     * @var bool
     */
    public $sendMailOnMatch;

    public function __construct(
        $apiType,
        $prepayPaymentMethods,
        $proxyHost,
        $proxySecret,
        $fintsServer,
        $fintsPort,
        $fintsBankCode,
        $fintsUsername,
        $fintsPin,
        $fintsAccountToCheck,
        $paymentIdsToCheck,
        $statusIdsToCheck,
        $statusOnMatch,
        $sendMailOnMatch
    ) {
        $this->apiType = $apiType;
        $this->prepayPaymentMethods = $prepayPaymentMethods;
        $this->proxyHost = $proxyHost;
        $this->proxySecret = $proxySecret;
        $this->fintsServer = $fintsServer;
        $this->fintsPort = $fintsPort;
        $this->fintsBankCode = $fintsBankCode;
        $this->fintsUsername = $fintsUsername;
        $this->fintsPin = $fintsPin;
        $this->fintsAccountToCheck = $fintsAccountToCheck;
        $this->paymentIdsToCheck = $paymentIdsToCheck;
        $this->statusIdsToCheck = $statusIdsToCheck;
        $this->statusOnMatch = $statusOnMatch;
        $this->sendMailOnMatch = $sendMailOnMatch;
    }
}