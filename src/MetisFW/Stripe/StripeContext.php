<?php

namespace MetisFW\Stripe;

use Nette\SmartObject;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class StripeContext {

  use SmartObject;

  /** @var string */
  private $publicApiKey;

  /** @var string */
  private $secretApiKey;

  /** @var array */
  private $paymentIntentDefaults = [];

  /**
   * @param string $publicApiKey
   * @param string $secretApiKey
   */
  public function __construct($publicApiKey, $secretApiKey) {
    $this->publicApiKey = $publicApiKey;
    $this->secretApiKey = $secretApiKey;
  }

  /**
   * @param array $params
   */
  public function setPaymentIntentDefaults(array $params) {
    $this->paymentIntentDefaults = $params;
  }

  /**
   * @return PaymentIntent
   * @throws StripeException
   */
  public function createPaymentIntent(array $params) {
    try {
      Stripe::setApiKey($this->secretApiKey);
      $params = array_merge_recursive($this->paymentIntentDefaults, $params);
      return PaymentIntent::create($params);
    } catch (ApiErrorException $exception) {
      throw new StripeException("Create PaymentIntent failed", 0, $exception);
    }
  }

  /**
   * @return PaymentIntent
   * @throws StripeException
   */
  public function retrievePaymentIntent(string $paymentIntentId) {
    try {
      Stripe::setApiKey($this->secretApiKey);
      return PaymentIntent::retrieve($paymentIntentId);
    } catch (ApiErrorException $exception) {
      throw new StripeException("Retrieve PaymentIntent failed", 0, $exception);
    }
  }

  /**
   * @return string
   */
  public function getPublicApiKey() {
    return $this->publicApiKey;
  }

}
