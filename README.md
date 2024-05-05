# Glide Image Transforms

On-demand image manipulation for Craft CMS using Glide 2.0 from The PHP League.

Note: This currently only supports local storage filesystems.

## Example Usage

    <img src="/glide/myimage.jpg?w=100&h=200" alt="My Image" />
    
If your asset uploaded to craft is `mysite.com/assets/animage.jpg`
then this can now be rendered with Glide at `mysite.com/glide/animage.jpg?w=100`

For more information about what parameters are supported please see the [Glide website](https://glide.thephpleague.com/2.0/api/quick-reference/).

## Installation

    composer require wishanddell/craft-glide
    
Assets can now be rendered through Glide using:

    https://your-domain.com/path-to-craft/glide/{filename}

## CDN / Advanced Usage

We strongly recommend using [Imgix](https://www.imgix.com/) or similar if your budget will allow it. 
It works in a similar way to this plugin but with a few nice extra features and all responses are served via CDN.

This plugin was never intended to replace Imgix, Cloudinary, etc. but rather is aimed at smaller projects with tighter budgets
that need more flexibility than Crafts native transforms.

It should be possible to create a CDN distribution using your domain as the origin, just make sure Query String Forwarding is enabled.

## Security

By signing each request with a private key, no alterations can be made to the URL parameters.

Create the file `config/glide.php` with the following:

    <?php
    
    return [
        'signed' => true,
        'key' => 'random-long-string',
    ];
    
Then you can use this service to generate the URL:

    \wishanddell\glide\Plugin::getInstance()->render->url('image.jpg', ['w' => '100']);
    
Or with Twig:

    <img src="{{ craft.glide.url('wedding.jpg', {w: 500}) }}" alt="My Image" />