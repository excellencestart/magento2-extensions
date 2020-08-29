<?php
/**
 * @author Excellencestar
 * @copyright Copyright (c) 2020 Excellencestar
 * @package Excellencestar_InvisibleCaptcha
 */


namespace Excellencestar\InvisibleCaptcha\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BadgeTheme implements OptionSourceInterface
{
    const BADGE_THEME_LIGHT = 'light';
    const BADGE_THEME_DARK = 'dark';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BADGE_THEME_LIGHT, 'label'=> __('Light')],
            ['value' => self::BADGE_THEME_DARK, 'label'=> __('Dark')]
        ];
    }
}
