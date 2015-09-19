<?php
namespace Craft;

class CommerceAusPostLookupPlugin extends BasePlugin
{

    protected $settings;

    public function init()
    {
        $this->settings = $this->getSettings();
    }

    function getName()
    {
         return Craft::t('Aus Post Lookup for Commerce');
    }

    function getVersion()
    {
        return '0.0.1';
    }

    function getDeveloper()
    {
        return 'Jeremy Daalder';
    }

    function getDeveloperUrl()
    {
        return '';
    }

    public function defineSettings()
    {
        return array(
            'debug'                         => AttributeType::Bool,
            'apiKey'                        => AttributeType::String,
            'urlPrefix'                     => AttributeType::String,
        );
    }

    public function getSettingsHtml()
    {

        $settings = $this->settings;

        $variables = array(
            'name'     => $this->getName(true),
            'version'  => $this->getVersion(),
            'settings' => $settings,
        );

        return craft()->templates->render('commerceauspostlookup/_settings', $variables);

   }
}