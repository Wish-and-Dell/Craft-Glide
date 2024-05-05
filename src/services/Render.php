<?php
namespace wishanddell\glide\services;

use yii\base\Component;
use wishanddell\glide\Plugin;
use craft\elements\Asset;
use League\Glide\Urls\UrlBuilderFactory;

class Render extends Component
{
    public function url($path, $params)
    {
        $settings = Plugin::getInstance()->getSettings();

        // Find asset by filename
        $assets = Asset::find()->filename(basename($path))->all();

        // Filter out assets not in this folder
        $assets = array_filter($assets, function ($asset) use ($path) {
          /**
           * @var Asset $asset
           */
          return (strpos($path, $asset->folderPath . $asset->filename) !== false);
        });

        if (empty($assets))
        {
          return null;
        }

        $firstItemKey = array_key_first($assets);
        $asset        = $assets[$firstItemKey];

        // Create an instance of the URL builder
        $urlBuilder = UrlBuilderFactory::create('/', $settings->key);

        // Generate a URL
        return $urlBuilder->getUrl('glide/' . $asset->folderPath . $asset->filename, $params);
    }
}
