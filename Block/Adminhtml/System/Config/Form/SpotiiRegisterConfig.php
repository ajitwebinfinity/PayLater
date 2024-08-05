<?php
namespace Spotii\Spotiipay\Block\Adminhtml\System\Config\Form;

class SpotiiRegisterConfig extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->_layout
            ->createBlock(\Spotii\Spotiipay\Block\Adminhtml\System\Config\SpotiiRegisterAdmin::class)
            ->setTemplate('Spotii_Spotiipay::system/config/spotii_register_admin.phtml')
            ->setCacheable(false)
            ->toHtml();

        return $html;
    }
}
