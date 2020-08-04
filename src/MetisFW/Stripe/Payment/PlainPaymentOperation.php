<?php

namespace MetisFW\Stripe\Payment;

use MetisFW\Stripe\StripeContext;

class PlainPaymentOperation extends BasePaymentOperation {

  /** @var array */
  private $params;

  /**
   * @param StripeContext $context
   * @param array $params
   */
  public function __construct(StripeContext $context, array $params) {
    parent::__construct($context);
    $this->params = $params;
  }

  /**
   * @return array
   */
  protected function getPaymentIntentParams() {
    return $this->params;
  }

}
