<?php
/**
 * @author Excellencestar
 * @copyright Copyright (c) 2020 Excellencestar
 * @package Excellencestar_InvisibleCaptcha
 */


namespace Excellencestar\InvisibleCaptcha\Model;

use Excellencestar\InvisibleCaptcha\Model\Config\Source\CaptchaVersion;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ConfigProvider
 *
 * Config Provider for settings
 */
class ConfigProvider
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const CONFIG_PATH_GENERAL_ENABLE_MODULE = 'invisiblecaptcha/general/enabledCaptcha';
    const CONFIG_PATH_SETUP_CAPTCHA_VERSION = 'invisiblecaptcha/setup/captchaVersion';
    const CONFIG_PATH_SETUP_CAPTCHA_SCORE = 'invisiblecaptcha/setup/captchaScore';
    const CONFIG_PATH_SETUP_SITE_KEY = 'invisiblecaptcha/setup/captchaKey';
    const CONFIG_PATH_SETUP_SECRET_KEY = 'invisiblecaptcha/setup/captchaSecret';
    const CONFIG_PATH_SETUP_SITE_KEY_V3 = 'invisiblecaptcha/setup/captchaKeyV3';
    const CONFIG_PATH_SETUP_SECRET_KEY_V3 = 'invisiblecaptcha/setup/captchaSecretV3';
    const CONFIG_PATH_SETUP_LANGUAGE = 'invisiblecaptcha/setup/captchaLanguage';
    const CONFIG_PATH_FORMS_DEFAULT_FORMS = 'invisiblecaptcha/forms/defaultForms';
    const CONFIG_PATH_FORMS_CUSTOM_FORMS = 'invisiblecaptcha/forms/urls';
    const CONFIG_PATH_SETUP_BADGE_THEME = 'invisiblecaptcha/setup/badgeTheme';
    const CONFIG_PATH_SETUP_BADGE_POSITION = 'invisiblecaptcha/setup/badgePosition';
    const CONFIG_PATH_SETUP_CAPTCHA_ERROR_MESSAGE = 'invisiblecaptcha/setup/errorMessage';
    const CONFIG_PATH_SETUP_CAPTCHA_IPWHITELIST = 'invisiblecaptcha/setup/ipWhiteList';

    const FORM_SELECTOR_PATTERN = 'form[action*="%s"]';

    /**
     * Local IP address
     */
    const LOCAL_IP = '127.0.0.1';

    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig(self::CONFIG_PATH_GENERAL_ENABLE_MODULE);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->getSiteKey()) && !empty($this->getSecretKey());
    }

    /**
     * @return int
     */
    public function getCaptchaVersion()
    {
        return (int)$this->getConfig(self::CONFIG_PATH_SETUP_CAPTCHA_VERSION);
    }

    /**
     * @return float
     */
    public function getCaptchaScore()
    {
        return $this->getConfig(self::CONFIG_PATH_SETUP_CAPTCHA_SCORE);
    }

    /**
     * @return string
     */
    public function getSiteKey()
    {
        $configPath = self::CONFIG_PATH_SETUP_SITE_KEY;
        if ($this->getCaptchaVersion() == CaptchaVersion::VERSION_3) {
            $configPath = self::CONFIG_PATH_SETUP_SITE_KEY_V3;
        }

        return $this->getConfig($configPath);
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        $configPath = self::CONFIG_PATH_SETUP_SECRET_KEY;
        if ($this->getCaptchaVersion() == CaptchaVersion::VERSION_3) {
            $configPath = self::CONFIG_PATH_SETUP_SECRET_KEY_V3;
        }

        return $this->getConfig($configPath);
    }

    /**
     * @return array
     */
    public function getEnabledDefaultForms(): array
    {
        return $this->explode($this->getConfig(self::CONFIG_PATH_FORMS_DEFAULT_FORMS));
    }

    /**
     * @return array
     */
    public function getEnabledCustomForms(): array
    {
        return $this->explode($this->getConfig(self::CONFIG_PATH_FORMS_CUSTOM_FORMS));
    }

    /**
     * @return array
     */
    public function getWhiteListIps(): array
    {
        return $this->explode($this->getConfig(self::CONFIG_PATH_SETUP_CAPTCHA_IPWHITELIST));
    }

    /**
     * @return array
     */
    public function getAllFormSelectors(): array
    {
        $formsSelectors = array_map(
            function ($url) {
                return sprintf(self::FORM_SELECTOR_PATTERN, $url);
            },
            $this->getAllUrls()
        );
        return $formsSelectors;
    }

    /**
     * @return array
     */
    public function getAllUrls(): array
    {
        return array_merge(
            $this->getEnabledDefaultForms(),
            $this->getEnabledCustomForms()         
        );
    }
    

    /**
     * @return string
     */
    public function getLanguage()
    {
        $language = $this->getConfig(self::CONFIG_PATH_SETUP_LANGUAGE);
        if ($language && 7 > mb_strlen($language)) {
            $language = '&hl=' . $language;
        } else {
            $language = '';
        }

        return $language;
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getBadgePosition()
    {
        return $this->getConfig(self::CONFIG_PATH_SETUP_BADGE_POSITION);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getBadgeTheme()
    {
        return $this->getConfig(self::CONFIG_PATH_SETUP_BADGE_THEME);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getConfigErrorMessage()
    {
        return $this->getConfig(self::CONFIG_PATH_SETUP_CAPTCHA_ERROR_MESSAGE);
    }

    /**
     * @param string|null $string
     * @return array
     */
    protected function explode($string): array
    {
        $string = trim($string);

        return !empty($string)
            ? preg_split('|\s*[\r\n,]+\s*|', $string, -1, PREG_SPLIT_NO_EMPTY)
            : [];
    }

    /**
     * @param string|null $string
     * @return $string
     */

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getCurrentIp()
    {
        foreach ($this->addressPath as $path) {
            $ip = $this->request->getServer($path);
            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) !== self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } else {
                    if ($ip !== self::LOCAL_IP) {
                        return $ip;
                    }
                }
            }
        }

        return $this->remoteAddress->getRemoteAddress();
    }
}
