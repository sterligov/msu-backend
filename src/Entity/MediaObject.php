<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateMediaObjectAction;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\DeleteMediaObjectAction;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ApiResource(
 *     iri="http://localhost:8888/MediaObject",
 *     normalizationContext={
 *         "groups"={"media_object_read"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "path"="/media_objects/{id}",
 *              "controller"=DeleteMediaObjectAction::class
 *          }
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN')"},
 *         "post"={
 *             "controller"=CreateMediaObjectAction::class,
 *             "deserialize"=false,
 *             "validation_groups"={"Default", "media_object_create"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *     }
 * )
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\MediaObjectRepository")
 */
class MediaObject
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ApiProperty(iri="http://localhost:8888/contentUrl")
     * @Groups({"appeal", "article"})
     */
    private $contentUrl;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PreviewImage", mappedBy="mediaObject", cascade={"remove"})
     */
    private $previewImage;

    /**
     * @var File|null
     *
     * @Assert\NotNull(groups={"media_object_create"})
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filename")
     */
    public $file;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true, name="filename")
     */
    public $filename;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="mediaObjects")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $article;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function setContentUrl(?string $contentUrl)
    {
        $this->contentUrl = $contentUrl;
    }

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPreviewImage(): ?PreviewImage
    {
        return $this->previewImage;
    }

    public function setPreviewImage(PreviewImage $previewImage): self
    {
        $this->previewImage = $previewImage;

        // set the owning side of the relation if necessary
        if ($previewImage->getMediaObject() !== $this) {
            $previewImage->setMediaObject($this);
        }

        return $this;
    }
}