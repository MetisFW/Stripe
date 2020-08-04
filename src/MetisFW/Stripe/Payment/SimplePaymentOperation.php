<?php

namespace MetisFW\Stripe\Payment;

use MetisFW\Stripe\StripeContext;

class SimplePaymentOperation extends BasePaymentOperation {

  /** @var string */
  private $name;

  /** @var int */
  private $quantity;

  /** @var int|float|string */
  private $price;

  /** @var string|null */
  private $currency;

  /**
   * @param StripeContext $context
   * @param string $name
   * @param int|float|string $price
   * @param int $quantity
   * @param string|null $currency
   */
  public function __construct(StripeContext $context, $name, $price, $quantity = 1, $currency = null) {
    parent::__construct($context);
    $this->name = $name;
    $this->quantity = $quantity;
    $this->price = $price;
    $this->currency = $currency;
  }

  /**
   * @return array
   */
  protected function getPaymentIntentParams() {
    $params = [
      'amount' => $this->price * $this->quantity,
      'description' => $this->name
    ];
    if($this->currency) {
      $params['currency'] = $this->currency;
    }
    return $params;
  }

}