<?php

namespace MetisFW\Stripe\UI;

use MetisFW\Stripe\Payment\PaymentOperation;
use MetisFW\Stripe\StripeException;
use Nette\Application\UI\Control;
use Nette\Utils\Random;

class PaymentControl extends Control {

  /**
   * @var PaymentOperation
   */
  private $operation;

  /**
   * @var string
   */
  private $templateFilePath;

  /**
   * @var array of callbacks, signature: function(PaymentControl $control, PaymentIntent $paymentIntent)
   */
  public $onSuccess = array();

  /**
   * @var array of callbacks, signature: function(PaymentControl $control, \Exception $exception)
   */
  public $onError = array();

  /**
   * @param PaymentOperation $operation
   */
  public function __construct(PaymentOperation $operation) {
    $this->operation = $operation;
  }

  public function setTemplateFilePath($templateFilePath) {
    $this->templateFilePath = $templateFilePath;
  }
  public function getTemplateFilePath() {
    return $this->templateFilePath ? $this->templateFilePath : $this->getDefaultTemplateFilePath();
  }

  public function handleSuccess() {
    $paymentIntentId = $this->getPresenter()->getParameter('paymentIntentId') ?? $this->getPresenter()->getParameter('payment_intent');

    try {
      $paidPayment = $this->operation->handleSuccess($paymentIntentId);
    }
    catch(StripeException $exception) {
      $this->errorHandler($exception);
      return;
    }

    $this->onSuccess($this, $paidPayment);
  }

  /**
   * @param array $attrs
   * @param string $text
   */
  public function render($attrs = array(), $text = "Submit payment") {
    $template = $this->template;
    if($this->templateFilePath) {
      /** @phpstan-latte-ignore */
      $template->setFile($this->templateFilePath);
    } else {
      $template->setFile($this->getDefaultTemplateFilePath());
    }
    try {
      $template->clientSecret = $this->operation->createPaymentIntent()->client_secret;
      $template->publicApiKey = $this->operation->getPublicApiKey();
    } catch (StripeException $exception) {
      $this->errorHandler($exception);
    }
    $template->text = $text;
    $template->attrs = $attrs;
    $template->elementId = Random::generate(5);
    $template->successLink = $this->link('//success!');
    $template->render();
  }

  /**
   * @param \Exception $exception
   *
   * @throws StripeException
   *
   * @return void
   */
  protected function errorHandler(\Exception $exception) {
    if(!$this->onError) {
      throw $exception;
    }

    $this->onError($this, $exception);
  }

  protected function getDefaultTemplateFilePath() {
    return __DIR__.'/templates/PaymentControl.latte';
  }

}
