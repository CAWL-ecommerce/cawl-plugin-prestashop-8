<?php
/**
 * 2021 CAWL Online Payments
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    PrestaShop partner
 * @copyright 2021 CAWL Online Payments
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
namespace WorldlineOP\PrestaShop\Presenter;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Cart;
use Cawlop;
use OnlinePayments\Sdk\Domain\RefundResponse;
use Order;
use Validate;
use WorldlineOP\PrestaShop\Logger\LoggerFactory;
use WorldlineOP\PrestaShop\Repository\TransactionRepository;
use WorldlineOP\PrestaShop\Utils\Tools;

/**
 * Class GetRefundPresenter
 */
class GetRefundPresenter
{
    /** @var Cawlop */
    private $module;

    /** @var \Monolog\Logger */
    private $logger;

    /** @var TransactionPresented */
    protected $presentedData;

    /**
     * GetRefundPresenter constructor.
     *
     * @param Cawlop $module
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(
        Cawlop $module,
        LoggerFactory $loggerFactory
    ) {
        $this->module = $module;
        $this->logger = $loggerFactory->setChannel('GetPaymentPresenter');
        $this->presentedData = new TransactionPresented();
    }

    /**
     * @param RefundResponse $refundResponse
     * @param int $idShop
     *
     * @return TransactionPresented
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function present(RefundResponse $refundResponse, int $idShop): TransactionPresented
    {
        $merchantReferenceFull = $refundResponse->getRefundOutput()->getReferences()->getMerchantReference();
        $merchantReferenceParts = explode('-', $merchantReferenceFull);
        $cart = new Cart((int) $merchantReferenceParts[0]);
        if (!Validate::isLoadedObject($cart)) {
            $this->logger->error('Cart cannot be loaded', ['merchantReference' => $merchantReferenceFull]);

            return $this->presentedData;
        }
        if ($cart->id_shop != $idShop) {
            $this->logger->error('Cart shop does not match webhook event shop', ['id_shop' => $idShop]);

            return $this->presentedData;
        }
        $idOrder = Order::getIdByCartId($cart->id);
        $order = new Order((int) $idOrder);
        if (!Validate::isLoadedObject($order)) {
            $this->logger->error('Cart cannot be loaded', ['merchantReference' => $merchantReferenceFull]);

            return $this->presentedData;
        }
        /** @var TransactionRepository $transactionRepository */
        $transactionRepository = $this->module->getService('cawlop.repository.transaction');
        /** @var \WorldlineopTransaction|false $transaction */
        $transaction = $transactionRepository->findByIdOrder($order->id);
        if (false === $transaction) {
            $this->logger->error('Could not find transaction', ['merchantReference' => $merchantReferenceFull]);

            return $this->presentedData;
        }
        $merchantReference = substr($refundResponse->getId(), 0, -3);
        if ('' === $merchantReference) {
            $merchantReference = $refundResponse->getId();
        }
        $transactionReference = substr($transaction->reference, 0, -3);
        if ('' === $transactionReference) {
            $transactionReference = $transaction->reference;
        }
        if ($transactionReference !== $merchantReference && '' !== $merchantReference) {
            $this->logger->error('Could not find transaction', ['merchantReference' => $merchantReferenceFull]);

            return $this->presentedData;
        }

        $idOrderStateRefund = (int) \Configuration::get('PS_OS_REFUND');
        $this->presentedData->updateStatus = true;
        $this->presentedData->order['ids'] = Tools::getOrderIdsByIdCart($order->id_cart);
        $this->presentedData->idOrderState = $idOrderStateRefund;
        $this->presentedData->sendMail = \Configuration::getGlobalValue('WOP_AWAITING_CAPTURE_STATUS_ID') == $idOrderStateRefund;
        $this->presentedData->payments['hasPayments'] = $order->getOrderPayments();
        $this->presentedData->payments['merchantReference'] = $merchantReference;
        $this->logger->debug('Refund event. Update order state to ID ' . $idOrderStateRefund);

        return $this->presentedData;
    }
}
