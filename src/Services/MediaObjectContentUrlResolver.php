<?php


namespace App\Services;


use App\Entity\MediaObject;
use Vich\UploaderBundle\Storage\StorageInterface;

class MediaObjectContentUrlResolver
{
    /**
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * MediaObjectContentUrlResolver constructor.
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param MediaObject $mediaObject
     * @return string|null
     */
    public function resolve(MediaObject $mediaObject): ?string
    {
        $uri = $this->storage->resolveUri($mediaObject, 'file');
        if (!$uri) {
            return null;
        }

        return $_ENV['URL'] . $uri;
    }
}