<?php
/*
 * This file was automatically generated.
 */
namespace OnlinePayments\Sdk\Domain;

use UnexpectedValueException;

/**
 * @package OnlinePayments\Sdk\Domain
 */
class CardPaymentMethodSpecificInputForHostedCheckout extends DataObject
{
    /**
     * @var bool
     */
    public $clickToPay = null;

    /**
     * @var bool
     */
    public $groupCards = null;

    /**
     * @var int[]
     */
    public $paymentProductPreferredOrder = null;

    /**
     * LOCAL ADDITION - not part of vendored SDK 5.7.0.
     *
     * The platform accepts this field, but the SDK only exposes it from 8.0 onward. It is added
     * here rather than by upgrading the SDK, because the 8.0+ line type-hints every setter and
     * splits the request payloads into X / XBase pairs, which is an API migration for the whole
     * payment flow. Re-apply this property if this SDK is ever replaced wholesale.
     *
     * @var string
     */
    public $tokenizationMode = null;

    /**
     * @return bool
     */
    public function getClickToPay()
    {
        return $this->clickToPay;
    }

    /**
     * @param bool
     */
    public function setClickToPay($value)
    {
        $this->clickToPay = $value;
    }

    /**
     * @return bool
     */
    public function getGroupCards()
    {
        return $this->groupCards;
    }

    /**
     * @param bool
     */
    public function setGroupCards($value)
    {
        $this->groupCards = $value;
    }

    /**
     * @return int[]
     */
    public function getPaymentProductPreferredOrder()
    {
        return $this->paymentProductPreferredOrder;
    }

    /**
     * @param int[]
     */
    public function setPaymentProductPreferredOrder($value)
    {
        $this->paymentProductPreferredOrder = $value;
    }

    /**
     * @return string
     */
    public function getTokenizationMode()
    {
        return $this->tokenizationMode;
    }

    /**
     * @param string
     */
    public function setTokenizationMode($value)
    {
        $this->tokenizationMode = $value;
    }

    /**
     * @return object
     */
    public function toObject()
    {
        $object = parent::toObject();
        if (!is_null($this->clickToPay)) {
            $object->clickToPay = $this->clickToPay;
        }
        if (!is_null($this->groupCards)) {
            $object->groupCards = $this->groupCards;
        }
        if (!is_null($this->paymentProductPreferredOrder)) {
            $object->paymentProductPreferredOrder = [];
            foreach ($this->paymentProductPreferredOrder as $element) {
                if (!is_null($element)) {
                    $object->paymentProductPreferredOrder[] = $element;
                }
            }
        }
        if (!is_null($this->tokenizationMode)) {
            $object->tokenizationMode = $this->tokenizationMode;
        }
        return $object;
    }

    /**
     * @param object $object
     * @return $this
     * @throws UnexpectedValueException
     */
    public function fromObject($object)
    {
        parent::fromObject($object);
        if (property_exists($object, 'clickToPay')) {
            $this->clickToPay = $object->clickToPay;
        }
        if (property_exists($object, 'groupCards')) {
            $this->groupCards = $object->groupCards;
        }
        if (property_exists($object, 'paymentProductPreferredOrder')) {
            if (!is_array($object->paymentProductPreferredOrder) && !is_object($object->paymentProductPreferredOrder)) {
                throw new UnexpectedValueException('value \'' . print_r($object->paymentProductPreferredOrder, true) . '\' is not an array or object');
            }
            $this->paymentProductPreferredOrder = [];
            foreach ($object->paymentProductPreferredOrder as $element) {
                $this->paymentProductPreferredOrder[] = $element;
            }
        }
        if (property_exists($object, 'tokenizationMode')) {
            $this->tokenizationMode = $object->tokenizationMode;
        }
        return $this;
    }
}
