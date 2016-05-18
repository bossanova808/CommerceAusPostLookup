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
         return Craft::t('Commerce Aus Post Lookup');
    }

    function getVersion()
    {
        return '0.1.1';
    }

    function getDeveloper()
    {
        return 'Jeremy Daalder';
    }

    function getDeveloperUrl()
    {
        return 'https://github.com/bossanova808';
    }

    function getDocumentationUrl(){
        return 'https://github.com/bossanova808/CommerceAusPostLookup';
    }

    function getDescription(){
        return 'Commerce Aus Post Lookup helps you set up an address form field for automatic lookup of correct Aus Post mailing details (suburb, postcode and state in one field).  Requires curl.';
    }

    function hasSettings(){
        return true;
    }

    function getReleaseFeedUrl(){
        return 'https://raw.githubusercontent.com/bossanova808/CommerceAusPostLookup/master/releases.json';
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
            'description' => $this->getDescription(),
        );

        return craft()->templates->render('commerceauspostlookup/_settings', $variables);

   }
}