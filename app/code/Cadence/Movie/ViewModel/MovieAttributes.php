<?php
namespace Cadence\Movie\ViewModel;

use Magento\Framework\Registry;
use Magento\Catalog\Api\Data\ProductInterface;

class MovieAttributes implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Get Attributes
     *
     * @param ProductInterface $product
     * @param array $attributeCodes
     * @return array
     */
    public function getAttributes($product, $attributeCodes)
    {
        $displayValues = [];
        foreach ($attributeCodes as $attributeCode) {
            $attribute = $product->getResource()->getAttribute($attributeCode);
            if ($attribute) {
                $attributeValue = $product->getData($attributeCode);
                if (!empty($attributeValue)) {
                    $displayValues[] = [
                        'label' => $attribute->getStoreLabel(),
                        'value' => $attributeValue,
                    ];
                }
            }
        }

        return $displayValues;
    }
}
