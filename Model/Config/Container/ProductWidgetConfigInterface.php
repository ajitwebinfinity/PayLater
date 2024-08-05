<?php
/*
 * @category    Spotii
 * @package     Spotii_Spotiipay
 * @copyright   Copyright (c) Spotii (https://www.spotii.me/)
 */

namespace Spotii\Spotiipay\Model\Config\Container;

/**
 * Interface ProductWidgetConfigInterface
 * @package Spotii\Spotiipay\Model\Config\Container
 */
interface ProductWidgetConfigInterface extends IdentityInterface
{

    /**
     * Get Target Xpath
     * @return mixed
     */
    public function getTargetXPath();

    /**
     * Get Render to Path
     * @return mixed
     */
    public function getRenderToPath();

    /**
     * Get forced show
     * @return mixed
     */
    public function getForcedShow();

    /**
     * Get alignment
     * @return mixed
     */
    public function getAlignment();

    /**
     * Get theme
     * @return mixed
     */
    public function getTheme();

    /**
     * Get width type
     * @return mixed
     */
    public function getWidthType();

    /**
     * Get image url
     * @return mixed
     */
    public function getImageUrl();

    /**
     * Get hide class
     * @return mixed
     */
    public function getHideClass();
}
