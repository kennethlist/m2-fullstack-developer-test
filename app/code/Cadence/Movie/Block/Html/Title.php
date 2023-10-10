<?php
namespace Cadence\Movie\Block\Html;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Registry;

class Title extends \Magento\Theme\Block\Html\Title
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Context $productContext
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Context $productContext,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $scopeConfig, $data);
        $this->registry = $productContext->getRegistry();
    }

    /**
     * Get Product
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }
}
