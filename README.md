# Glide Image Transforms

On-demand image manipulation for Craft CMS using Glide 3.0 from The PHP League.
This plugin is an alternative to Crafts native image transforms and allows you to specify
parameters to adjust the image on the fly in the url.

Supported file systems:

- Local
- [AWS S3](https://plugins.craftcms.com/aws-s3)

## Example Usage

    <img src="/glide/myimage.jpg?w=100&h=200" alt="My Image" />
    
If your asset uploaded to craft is `mysite.com/assets/animage.jpg`
then this can now be rendered with Glide at `mysite.com/glide/animage.jpg?w=100`

For more information about what parameters are supported please see the [Glide website](https://glide.thephpleague.com/3.0/api/quick-reference/).

## Installation

    composer require wishanddell/craft-glide
    
Assets can now be rendered through Glide using:

    https://your-domain.com/glide/{filename}

## AWS S3

When configuring your AWS filesystem, tick the box for public URLs and set the value to `https://your-domain.com/glide/`
The plugin will request the image from S3, apply the transforms and save the cached version back to your S3 bucket prefixed with `glide`

## CDN / Advanced Usage

It is possible to create a CDN distribution using your domain as the origin, just make sure Query String Forwarding is enabled.

## Security

By signing each request with a private key, no alterations can be made to the URL parameters.

Create the file `config/glide2.php` with the following:

    <?php
    
    return [
        'signed' => true,
        'key' => 'random-long-string',
    ];
    
Then you can use this service to generate the URL:

    \wishanddell\glide\Plugin::getInstance()->render->url('image.jpg', ['w' => '100']);
    
Or with Twig:

    <img src="{{ craft.glide.url('wedding.jpg', {w: 500}) }}" alt="My Image" />

## Example transforms

    craft.glide.url('testimage.png', {w: 200, h:200})

    craft.glide.url('testimage.png', {w: 100, flip: 'v', blur: 5})

    craft.glide.url('testimage.png', {w: 350, border: '10,5000,overlay', mark: 'test/another.png', markw: '100', markpos: 'top-left'})