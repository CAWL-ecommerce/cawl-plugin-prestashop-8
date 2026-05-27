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
namespace WorldlineOP\PrestaShop\Serializer;

if (!defined('_PS_VERSION_')) {
    exit;
}
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use WorldlineOP\PrestaShop\Configuration\Entity\PaymentMethod;
use WorldlineOP\PrestaShop\Configuration\Entity\PaymentMethodsSettings;

/**
 * Class PaymentMethodsSettingsDenormalizer
 */
class PaymentMethodsSettingsDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /** @var ObjectNormalizer */
    private $objectNormalizer;

    /**
     * @param ObjectNormalizer $objectNormalizer
     */
    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        $this->objectNormalizer = $objectNormalizer;
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     *
     * @return object|array
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;
        $obj = $this->objectNormalizer->denormalize($data, $type, $format, $context);
        if (!is_object($obj)) {
            return $obj;
        }
        if (isset($data['redirectPaymentMethods'])) {
            $array = [];
            foreach ($data['redirectPaymentMethods'] as $redirectPaymentMethod) {
                $array[] = $this->denormalizer->denormalize(
                    $redirectPaymentMethod,
                    PaymentMethod::class,
                    $format,
                    $context
                );
            }
            $obj->redirectPaymentMethods = $array;
        }
        if (isset($data['iframePaymentMethods'])) {
            $array = [];
            foreach ($data['iframePaymentMethods'] as $iframePaymentMethod) {
                $array[] = $this->denormalizer->denormalize(
                    $iframePaymentMethod,
                    PaymentMethod::class,
                    $format,
                    $context
                );
            }
            $obj->iframePaymentMethods = $array;
        }

        return $obj;
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === PaymentMethodsSettings::class;
    }

    /**
     * @param string|null $format
     *
     * @return array
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            PaymentMethodsSettings::class => true,
        ];
    }
}
