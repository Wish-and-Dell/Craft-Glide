<?php
namespace wishanddell\glide\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Asset;
use wishanddell\glide\Plugin;

use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

class ParseController extends Controller
{
    protected array|int|bool $allowAnonymous = true;

    public function actionIndex($path)
    {
        $params = $_GET;
        unset($params['p']);

        // // Find asset by filename
        $assets = Asset::find()->filename(basename($path))->all();

        // // Filter out assets not in this folder
        $assets = array_filter($assets, function ($asset) use ($path) {
          /**
           * @var Asset $asset
           */
          return (strpos($path, $asset->folderPath . $asset->filename) !== false);
        });

        if (empty($assets))
        {
          throw new \Exception("No assets found.");
        }

        $firstItemKey = array_key_first($assets);
        $asset        = $assets[$firstItemKey];
        
        $settings = Plugin::getInstance()->getSettings();
        if ($settings->signed) {
            SignatureFactory::create($settings->key)->validateRequest('glide/' . $asset->folderPath . $asset->filename, $params);
        }

        // Load Glide
        $server = ServerFactory::create([
            'source' => Craft::parseEnv($asset->getVolume()->fs->path),
            'cache' => '../storage/glide',
            'driver' => $settings->driver
        ]);

        // Render Image
        $server->outputImage($path, $_GET);
        die();
    }
}
