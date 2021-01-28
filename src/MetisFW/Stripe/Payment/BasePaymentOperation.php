<?php

namespace MetisFW\Stripe\Payment;

use MetisFW\Stripe\StripeContext;
use MetisFW\Stripe\StripeException;
use Nette\SmartObject;
use Stripe\PaymentIntent;
use Tracy\Debugger;

abstract class BasePaymentOperation implements PaymentOperation {

  use SmartObject;

  /** @var StripeContext */
  protected $context;

  /**
   * @var array array of callbacks, signature: function ($this) {...}
   */
  public $onError;

  /**
   * @var array array of callbacks, signature: function ($this, PaymentIntent $payment) {...}
   */
  public $onSuccess;

  /**
   * @param StripeContext $context
   */
  public function __construct(StripeContext $context) {
    $this->context = $context;
  }

  /**
   * @return PaymentIntent
   */
  public function createPaymentIntent() {
    return $this->context->createPaymentIntent($this->getPaymentIntentParams());
  }

  public function getPublicApiKey() {
    return $this->context->getPublicApiKey();
  }

  /**
   * @return array
   */
  abstract protected function getPaymentIntentParams();

  /**
   * @param string $paymentIntentId
   * @return PaymentIntent
   */
  public function handleSuccess($paymentIntentId) {
    try {
      $payment = $this->context->retrievePaymentIntent($paymentIntentId);
      if($payment->status != PaymentIntent::STATUS_SUCCEEDED) {
        throw new StripeException("Unexpected PaymentIntent status " + $payment->status + " for $paymentIntentId");
      }
      $this->onSuccess($this, $payment);
      return $payment;
    }
    catch(\Exception $exception) {
      Debugger::log($exception, Debugger::EXCEPTION);
      throw $exception;
    }
  }

}
