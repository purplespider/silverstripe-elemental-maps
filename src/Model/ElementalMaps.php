<?php

namespace TheWebmen\ElementalMaps\Model;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use TheWebmen\Addressfield\Forms\GooglePlacesField;
use TheWebmen\ElementalMaps\Controller\ElementalMapsController;

class ElementalMaps extends BaseElement
{
    private static $maps_api_key = false;

    private static $icon = 'font-icon-block-globe';

    private static $table_name = 'ElementalMaps';

    private static $title = 'Map';

    private static $description = 'Google Maps Map';

    private static $singular_name = 'Map';

    private static $plural_name = 'Maps';

    private static $controller_class = ElementalMapsController::class;

    private static $inline_editable = false;

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Map');
    }

    private static $db = [
        'MapLocation' => 'Varchar(255)',
        'Latitude' => 'Decimal(11,8)',
        'Longitude' => 'Decimal(11,8)',
        'MapZoom' => 'Int',
        'MapType' => 'Varchar',
        'MapHeight' => 'Varchar',
        'MapID' => 'Varchar',
    ];

    private static $has_many = [
        'Markers' => ElementalMapsMarker::class
    ];

    private static $defaults = [
        'MapZoom' => 12,
        'MapType' => 'roadmap',
        'MapHeight' => '500px',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', HeaderField::create('MapLocationHeader', 'Map Center'));
        $fields->addFieldToTab('Root.Main', $googlePlacesField = GooglePlacesField::create('MapLocation', 'Location')->setDescription('Start typing to search places using Google Maps'));
        $fields->addFieldToTab('Root.Main', TextField::create('Latitude'));
        $fields->addFieldToTab('Root.Main', TextField::create('Longitude'));
        $googlePlacesField->setLatitudeField('Latitude');
        $googlePlacesField->setLongitudeField('Longitude');

        $fields->addFieldToTab('Root.Main', HeaderField::create('MapSettingsHeader', 'Map Settings'));
        $fields->addFieldToTab('Root.Main', NumericField::create('MapZoom', 'Zoom Level')->setDescription('0 = World, 20 = Building'));
        $fields->addFieldToTab('Root.Main', DropdownField::create('MapType', 'Map Type', [
            'roadmap' => 'Roadmap',
            'satellite' => 'Satellite',
            'hybrid' => 'Hybrid',
            'terrain' => 'Terrain'
        ]));

        $fields->addFieldToTab('Root.Main', TextField::create('MapHeight', 'Map Height')->setDescription('CSS value, e.g. 500px - Leave empty to control in stylesheet'));
        $fields->addFieldToTab('Root.Main', TextField::create('MapID', 'Map ID')->setDescription('optional - Google Maps Map ID. Allows you to use custom styles and layers. See <a href="https://developers.google.com/maps/documentation/javascript/styling" target="_blank">Google Maps Styling</a>'));

        $markersField = $fields->dataFieldByName('Markers');
        if ($markersField) {
            $markersField->setConfig(GridFieldConfig_RecordEditor::create());
        }

        return $fields;
    }

    public function getSummary()
    {
        return $this->MapLocation;
    }
}
