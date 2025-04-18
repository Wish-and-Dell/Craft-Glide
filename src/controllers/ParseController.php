<?php
namespace wishanddell\glide\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Asset;
use wishanddell\glide\Plugin;

use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

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

        // AWS file system
        if ($asset->fs->handle == 'aws') {
            $filesystem = $this->createAwsFilesystem($asset->getVolume()->fs);
            $source = $filesystem;
            $cache = $filesystem;
        
        // Local file system
        } else {
            $source = Craft::parseEnv($asset->getVolume()->fs->path);
            $cache = '../storage';
        }

        // Load Glide
        $server = ServerFactory::create([
            'source' => $source,
            'watermarks' => $source,
            'cache' => $cache,
            'cache_path_prefix' => 'glide',
            'driver' => $settings->driver
        ]);

        // Render Image
        $server->outputImage($path, $_GET);
        die();
    }

    /**
     * AWS S3 Connection and Filesystem
     */
    protected function createAwsFilesystem($filesystem)
    {
      $client = S3Client::factory([
          'credentials' => [
              'key'    => $filesystem->keyId,
              'secret' => $filesystem->secret,
          ],
          'region' => $filesystem->region,
          'version' => 'latest',
      ]);
      
      $adapter = new AwsS3V3Adapter($client, $filesystem->bucket, $filesystem->subfolder);

      return new \League\Flysystem\Filesystem($adapter);
    }
}