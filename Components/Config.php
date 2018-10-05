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
     * @var string
     */
    public $prepayPaymentMethod;

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

    public function __construct(
        $apiType,
        $prepayPaymentMethod,
        $proxyHost,
        $proxySecret,
        $fintsServer,
        $fintsPort,
        $fintsBankCode,
        $fintsUsername,
        $fintsPin,
        $fintsAccountToCheck
    ) {
        $this->apiType = $apiType;
        $this->prepayPaymentMethod = $prepayPaymentMethod;
        $this->proxyHost = $proxyHost;
        $this->proxySecret = $proxySecret;
        $this->fintsServer = $fintsServer;
        $this->fintsPort = $fintsPort;
        $this->fintsBankCode = $fintsBankCode;
        $this->fintsUsername = $fintsUsername;
        $this->fintsPin = $fintsPin;
        $this->fintsAccountToCheck = $fintsAccountToCheck;
    }
}