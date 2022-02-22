<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Getter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Tool;

class BrickGetter implements GetterInterface
{
    public function __construct(protected TranslationLocaleProviderInterface $localeProvider)
    {
    }

    public function get(IndexableInterface $object, IndexColumnInterface $config): mixed
    {
        $columnConfig = $config->getConfiguration();
        $getterConfig = $config->getGetterConfig();

        if (!isset($getterConfig['brickField']) || !isset($columnConfig['className']) || !isset($columnConfig['key'])) {
            return null;
        }

        $brickField = $getterConfig['brickField'];

        $brickContainerGetter = 'get' . ucfirst($brickField);

        if (!method_exists($object, $brickContainerGetter)) {
            return null;
        }

        $brickContainer = $object->$brickContainerGetter();

        $brickGetter = 'get' . ucfirst($columnConfig['className']);

        if (!$brickContainer) {
            return null;
        }
        $brick = $brickContainer->$brickGetter();

        if (!$brick) {
            return null;
        }

        $fieldGetter = 'get' . ucfirst($columnConfig['key']);

        $field = $brick->$fieldGetter();

        if (!$field instanceof Localizedfield) {
            return $field;
        }

        $getter = 'get' . ucfirst($config->getObjectKey());

        $fallbackMemory = Localizedfield::getGetFallbackValues();
        Localizedfield::setGetFallbackValues(true);

        $values = [];
        foreach ($this->localeProvider->getDefinedLocalesCodes() as $locale) {
            $values[$locale] = $brick->$getter($locale);
        }

        Localizedfield::setGetFallbackValues($fallbackMemory);

        return $values;
    }
}
