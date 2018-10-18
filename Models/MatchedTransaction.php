<?php

namespace PrepayMatch\Models;

use Doctrine\ORM\Mapping as ORM;
use Padrio\BankingProxy\Model\Transaction;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Order\Order;

/**
 * @ORM\Entity(repositoryClass="PrepayMatch\Models\MatchedTransactionRepository")
 * @ORM\Table(name="s_matched_transactions")
 */
class MatchedTransaction extends ModelEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Order\Order")
     * @ORM\JoinTable(name="s_matched_transactions_order_mapping",
     *     joinColumns={
     *     @ORM\JoinColumn(name="matched_transaction_id", referencedColumnName="id")
     * }, inverseJoinColumns={
     *     @ORM\JoinColumn(name="order_id", referencedColumnName="id", unique=true)
     * })
     *
     * @var Order
     */
    private $order;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $matchedFirstName;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $matchedLastName;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $matchedOrderId = false;

    /**
     * @ORM\Column(type="array", nullable=false)
     * @var array
     *
     * Todo: create separate transaction entity?
     */
    private $transaction = [];

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     *
     * Todo: create separate transaction entity?
     */
    private $transactionHash;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function isMatchedFirstName()
    {
        return $this->matchedFirstName;
    }

    /**
     * @param bool $matchedFirstName
     */
    public function setMatchedFirstName($matchedFirstName)
    {
        $this->matchedFirstName = $matchedFirstName;
    }

    /**
     * @return bool
     */
    public function isMatchedLastName()
    {
        return $this->matchedLastName;
    }

    /**
     * @param bool $matchedLastName
     */
    public function setMatchedLastName($matchedLastName)
    {
        $this->matchedLastName = $matchedLastName;
    }

    /**
     * @return bool
     */
    public function isMatchedOrderId()
    {
        return $this->matchedOrderId;
    }

    /**
     * @param bool $matchedOrderId
     */
    public function setMatchedOrderId($matchedOrderId)
    {
        $this->matchedOrderId = $matchedOrderId;
    }

    /**
     * @param bool $asModel
     *
     * @return Transaction|array
     */
    public function getTransaction($asModel = true)
    {
        if($asModel) {
            return Transaction::createFromArray($this->transaction);
        }

        return $this->transaction;
    }

    /**
     * @param array $transaction
     */
    public function setTransaction($transaction)
    {
        if($transaction instanceof Transaction) {
            $transaction = $transaction->toArray();
        }

        $this->transaction = $transaction;
    }

    /**
     * @return string
     */
    public function getTransactionHash()
    {
        return $this->transactionHash;
    }

    /**
     * @param string $transactionHash
     */
    public function setTransactionHash($transactionHash)
    {
        $this->transactionHash = $transactionHash;
    }

    /**
     * Returns whether order wos completely matched
     * @return bool
     */
    public function isCompleted()
    {
        return $this->isMatchedName() || $this->isMatchedOrderId();
    }

    /**
     * Return whether order is paid
     * @return bool
     */
    public function isPartlyCompleted()
    {
        return ($this->isMatchedFirstName() || $this->isMatchedLastName()) || $this->isMatchedOrderId();
    }
}