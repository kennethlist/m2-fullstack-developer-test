<?php
namespace Cadence\Movie\Model;

use Magento\Framework\App\ResourceConnection;

class MovieInfo
{
    /**
     * @var ResourceConnection
     */
    public $resource;

    /**
     * __construct
     *
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Get product details by sku
     *
     * @param string $sku
     * @return array
     */
    public function get($sku)
    {
        $db  = $this->resource->getConnection();

        $productTableName = $db->getTableName('catalog_product_entity');
        $eavAttributeTableName = $db->getTableName('eav_attribute');
        $eavVarcharTableName = $db->getTableName('catalog_product_entity_varchar');
        $eavDatetimeTableName = $db->getTableName('catalog_product_entity_datetime');
        $eavDecimalTableName = $db->getTableName('catalog_product_entity_decimal');
        $eavIntTableName = $db->getTableName('catalog_product_entity_int');
        $eavTextTableName = $db->getTableName('catalog_product_entity_text');

        $query = "
            (
                SELECT e.attribute_code, v.value FROM $eavVarcharTableName v
                JOIN $eavAttributeTableName e ON e.attribute_id = v.attribute_id
                JOIN $productTableName p ON p.entity_id = v.entity_id WHERE " . $db->quoteInto('p.sku = ?', $sku) . "
            )
            UNION
            (
                SELECT e.attribute_code, v.value FROM $eavDatetimeTableName v
                JOIN $eavAttributeTableName e ON e.attribute_id = v.attribute_id
                JOIN $productTableName p ON p.entity_id = v.entity_id WHERE " . $db->quoteInto('p.sku = ?', $sku) . "
            )
            UNION
            (
                SELECT e.attribute_code, v.value FROM $eavDecimalTableName v
                JOIN $eavAttributeTableName e ON e.attribute_id = v.attribute_id
                JOIN $productTableName p ON p.entity_id = v.entity_id WHERE " . $db->quoteInto('p.sku = ?', $sku) . "
                )
            UNION
            (
                SELECT e.attribute_code, v.value FROM $eavIntTableName v
                JOIN $eavAttributeTableName e ON e.attribute_id = v.attribute_id
                JOIN $productTableName p ON p.entity_id = v.entity_id WHERE " . $db->quoteInto('p.sku = ?', $sku) . "
            )
            UNION
            (
                SELECT e.attribute_code, v.value FROM $eavTextTableName v
                JOIN $eavAttributeTableName e ON e.attribute_id = v.attribute_id
                JOIN $productTableName p ON p.entity_id = v.entity_id WHERE " . $db->quoteInto('p.sku = ?', $sku) . "
            )
            ORDER BY attribute_code;
        ";

        return $db->fetchAll($query);
    }
}
