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

namespace WorldlineOP\PrestaShop\Configuration\Loader;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Yaml\Parser;
use WorldlineOP\PrestaShop\OptionsResolver\AccountSettingsResolver;
use WorldlineOP\PrestaShop\OptionsResolver\AdvancedSettingsResolver;
use WorldlineOP\PrestaShop\OptionsResolver\PaymentMethodsSettingsResolver;

/**
 * Class DefaultSettingsProvider
 */
class DefaultSettingsProvider
{
    /** @var array|null */
    private $yaml;

    /**
     * DefaultSettingsProvider constructor.
     */
    public function __construct()
    {
        $this->yaml = null;
    }

    /**
     * @return array
     */
    public function getAccountSettings()
    {
        $settings = $this->getConfigurationSection('accountSettings');
        $settings['webhookMode'] = 'automatic';
        $settings['additionalWebhookUrls'] = [];
        $resolver = new AccountSettingsResolver();

        return $resolver->resolve($settings);
    }

    /**
     * @return array
     */
    public function getAdvancedSettings()
    {
        $settings = $this->getConfigurationSection('advancedSettings');
        $paymentSettings = isset($settings['paymentSettings']) && is_array($settings['paymentSettings'])
            ? $settings['paymentSettings']
            : [];
        $paymentSettings['successOrderStateId'] = \Configuration::get('PS_OS_PAYMENT');
        $paymentSettings['pendingOrderStateId'] = \Configuration::getGlobalValue('WOP_PENDING_ORDER_STATUS_ID');
        $paymentSettings['errorOrderStateId'] = \Configuration::get('PS_OS_ERROR');
        $settings['paymentSettings'] = $paymentSettings;
        $resolver = new AdvancedSettingsResolver();

        return $resolver->resolve($settings);
    }

    /**
     * @return array
     */
    public function getPaymentMethodsSettings()
    {
        $resolver = new PaymentMethodsSettingsResolver();

        return $resolver->resolve($this->getConfigurationSection('paymentMethodsSettings'));
    }

    /**
     * @param string $section
     *
     * @return array
     */
    private function getConfigurationSection($section)
    {
        $yaml = $this->getYaml();
        if (!isset($yaml['configuration'][$section]) || !is_array($yaml['configuration'][$section])) {
            return [];
        }

        return $yaml['configuration'][$section];
    }

    /**
     * @return array
     */
    private function getYaml()
    {
        if (null !== $this->yaml) {
            return $this->yaml;
        }
        $parser = new Parser();
        $yaml = $parser->parse(\Tools::file_get_contents(dirname(__DIR__, 3) . '/install/defaults.yml'));
        $this->yaml = is_array($yaml) ? $yaml : [];

        return $this->yaml;
    }
}
