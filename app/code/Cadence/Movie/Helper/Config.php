<?php
namespace Cadence\Movie\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const MOVIE_ATTRIBUTE_SET_ID = '9';
    const MOVIE_CATEGORY_ID = '3';

    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * __construct
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            'tmdb/general/api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
    }
}
