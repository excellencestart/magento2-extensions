<?php
/**
 * @author Excellencestar
 * @copyright Copyright (c) 2020 Excellencestar
 * @package Excellencestar_InvisibleCaptcha
 */


namespace Excellencestar\InvisibleCaptcha\Block;

use Magento\Framework\View\Element\Template;
use Excellencestar\InvisibleCaptcha\Model\ConfigProvider;

class Captcha extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    /**
     * @return ConfigProvider
     */
    public function getConfig()
    {
        return $this->configProvider;
    }
    
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
