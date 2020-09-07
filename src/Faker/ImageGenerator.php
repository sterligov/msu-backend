<?php

namespace App\Faker;

use Faker\Generator;
use Faker\Provider\Image;
use Faker\Provider\Base;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ImageGenerator extends Base
{
    private Filesystem $filesystem;

    /**
     * {@inheritdoc}
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(Generator $generator, Filesystem $filesystem)
    {
        parent::__construct($generator);

        $this->filesystem = $filesystem;
    }

    public function randomImage(int $width = 640, int $height = 480): string
    {
        $date = explode('-', date('Y-m'));
        $dir = "/var/www/html/public/media/{$date[0]}/{$date[1]}";
        $this->filesystem->mkdir($dir);

        return basename(Image::image($dir, $width, $height));
    }
}
