<?php

namespace MetisFW\Stripe\Payment;

use Stripe\PaymentIntent;

interface PaymentOperation {

  /**
   * @return PaymentIntent
   */
  public function createPaymentIntent();

  /**
   * @return string
   */
  public function getPublicApiKey();

  /**
   * @param string $paymentIntentId
   *
   * @return PaymentIntent
   */
  public function handleSuccess($paymentIntentId);

}