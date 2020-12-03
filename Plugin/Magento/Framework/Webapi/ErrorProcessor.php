<?php
namespace Coyote\Core\Plugin\Magento\Framework\Webapi;
use Exception as E;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\App\State;
use Magento\Framework\Exception\AggregateExceptionInterface as IAgE;
use Magento\Framework\Exception\AuthenticationException as AE;
use Magento\Framework\Exception\AuthorizationException as AE2;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\Phrase as Ph;
use Magento\Framework\Webapi\ErrorProcessor as Sb;
use Magento\Framework\Webapi\Exception as WE;
# 2020-12-02 «Enhance SOAP response messages»: https://github.com/coyoteaccessories/site/issues/11
final class ErrorProcessor {
	/**
	 * 2020-12-02
	 * @see \Magento\Framework\Webapi\ErrorProcessor::maskException()
	 * @used-by \Magento\Webapi\Controller\Soap::_prepareErrorResponse()
	 * @param Sb $sb
	 * @param E|LE|WE|IAgE $e
	 * @return WE[]
	 */
	function beforeMaskException(Sb $sb, E $e) {
		/** @var WE $r */
		if ($e instanceof WE) {
			$r = $e;
		}
		else {
			list($e, $wrapper) = !$e->getPrevious() ? [$e, null] : [$e->getPrevious(), $e]; /** @var E|null $wrapper */
			$isLE = $e instanceof LE; /** @var bool $isLE */
			$om = OM::getInstance(); /** @var OM $om */
			$state = $om->get(State::class); /** @var State $state */
			/** @var string $m */
			$m = (!$wrapper ? '' : $wrapper->getMessage() . "\n") . ($isLE ? $e->getRawMessage() : $e->getMessage());
			$trace = str_replace(BP . DIRECTORY_SEPARATOR, '', $e->getTraceAsString()); /** @var string $trace */
			/**
			 * 2020-12-03
			 * It is useless to override the @see \Magento\Webapi\Model\Soap\Fault::toXml() method with a plugin
			 * because the @see \Magento\Webapi\Model\Soap\Fault class is instantiated without Object Manager
			 * in the @see \Magento\Webapi\Controller\Soap::_prepareErrorResponse() method.
			 */
			if (State::MODE_DEVELOPER !== $state->getMode()) {
				$m .= "\n$trace";
			}
			$r = new WE(
				new Ph($m)
				,$e->getCode()
				,!$isLE ? WE::HTTP_INTERNAL_ERROR : ($e instanceof NSE ? WE::HTTP_NOT_FOUND : (
					$e instanceof AE || $e instanceof AE2 ? WE::HTTP_UNAUTHORIZED : WE::HTTP_BAD_REQUEST
				))
				,!$isLE ? [] : $e->getParameters()
				,!$isLE ? '' : get_class($e)
				,!$isLE || !$e instanceof IAgE ? null : $e->getErrors()
				,$trace
			);
		}
		return [$r];
	}
}