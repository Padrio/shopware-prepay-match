<?php

namespace PrepayMatch\Models;

use Doctrine\ORM\Mapping as ORM;
use Padrio\BankingProxy\Shared\Model\Transaction;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Order\Order;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_matched_transactions")
 */
final class MatchedTransaction extends ModelEntity
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
     * @ORM\Column(type="array", nullable=false)
     * @var array
     */
    private $transaction = [];

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
}