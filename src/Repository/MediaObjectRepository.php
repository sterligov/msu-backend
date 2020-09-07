<?php

namespace App\Repository;

use App\Entity\MediaObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @method MediaObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaObject[]    findAll()
 * @method MediaObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaObjectRepository extends ServiceEntityRepository
{
    private StorageInterface $storage;

    private Filesystem $filesystem;

    public function __construct(ManagerRegistry $registry, StorageInterface $storage, Filesystem $filesystem)
    {
        parent::__construct($registry, MediaObject::class);

        $this->storage = $storage;
        $this->filesystem = $filesystem;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(int $id): bool
    {
        $entity = $this->find($id);
        if (!$entity) {
            throw new EntityNotFoundException("MediaObject entity with id $id not found");
        }

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function hardDelete(int $id): bool
    {
        $entity = $this->find($id);
        if (!$entity) {
            throw new EntityNotFoundException("MediaObject entity with id $id not found");
        }

        $filename = $this->storage->resolvePath($entity, 'file');

        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();

        $this->filesystem->remove($filename);
    }
}
