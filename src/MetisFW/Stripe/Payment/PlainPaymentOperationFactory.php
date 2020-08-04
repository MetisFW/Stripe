<?php

namespace MetisFW\Stripe\Payment;

interface PlainPaymentOperationFactory {

  /**
   * @param array $params
   * @return PlainPaymentOperation
   */
  public function create(array $params);

}
