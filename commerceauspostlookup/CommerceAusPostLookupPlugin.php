<?php
namespace Craft;

class CommerceAusPostLookupPlugin extends BasePlugin
{

    private $version = "0.1.4";
    private $schemaVersion = "0.0.0";

    private $name = 'Commerce Aus Post Lookup';
    private $description = 'Commerce Aus Post Lookup helps you set up an address form field for automatic lookup of correct Aus Post mailing details (suburb, postcode and state in one field).  Requires curl.';
    private $documentationUrl = 'https://github.com/bossanova808/CommerceAusPostLookup';
    private $developer = "Jeremy Daalder";
    private $developerUrl = "https://github.com/bossanova808";
    private $releaseFeedUrl = "https://raw.githubusercontent.com/bossanova808/CommerceAusPostLookup/master/releases.json";

    static protected $settings;

    /**
     * Static log functions for this plugin
     *
     * @param mixed $msg
     * @param string $level
     * @param bool $force
     *
     * @return null
     */
    public static function logError($msg){
        CommerceAusPostLookupPlugin::log($msg, LogLevel::Error, $force = true);
    }
    public static function logWarning($msg){
        CommerceAusPostLookupPlugin::log($msg, LogLevel::Warning, $force = true);
    }
    // If debugging is set to true in this plugin's settings, then log every message, devMode or not.
    public static function log($msg, $level = LogLevel::Profile, $force = false)
    {
        if(self::$settings['debug']) $force=true;

        if (is_string($msg))
        {
            $msg = "\n\n" . $msg . "\n";
        }
        else
        {
            $msg = "\n\n" . print_r($msg, true) . "\n";
        }

        parent::log($msg, $level, $force);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDocumentationUrl()
    {
        return $this->documentationUrl;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSchemaVersion()
    {
        return $this->schemaVersion;
    }

    public function getDeveloper()
    {
        return $this->developer;
    }

    public function getDeveloperUrl()
    {
        return $this->developerUrl;
    }

    function hasSettings(){
        return true;
    }

    function getReleaseFeedUrl(){
        return $this->releaseFeedUrl;
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

    public function init()
    {
        $this->settings = $this->getSettings();
    }


}