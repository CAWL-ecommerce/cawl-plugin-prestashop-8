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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Registers Symfony product form hooks required for PS 8.1+ new product page.
 *
 * @param Cawlop $module
 *
 * @return bool
 */
function upgrade_module_2_0_31($module)
{
    $hooks = [
        'actionProductFormBuilderModifier',
        'actionAfterUpdateProductFormHandler',
    ];

    foreach ($hooks as $hook) {
        if (!$module->registerHook($hook)) {
            return false;
        }
    }

    return true;
}
