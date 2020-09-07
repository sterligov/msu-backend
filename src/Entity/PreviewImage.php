<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"article", "preview_image"}
 *     },
 *     attributes={
 *          "force_eager"=false,
 *          "order"={"article.publishedAt": "DESC", "article.id": "DESC"},
 *          "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *          "put"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PreviewImageRepository")
 */
class PreviewImage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ApiProperty(identifier=false)
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Article", inversedBy="previewImage")
     * @ORM\JoinColumn(nullable=false)
     * @ApiProperty(identifier=true)
     * @Groups({"article", "preview_image"})
     */
    private $article;

    /**
     * @ApiProperty(iri="http://localhos:8888/media")
     * @ORM\OneToOne(targetEntity="App\Entity\MediaObject", inversedBy="previewImage")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"article", "preview_image"})
     */
    private MediaObject $mediaObject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getMediaObject(): ?MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(MediaObject $mediaObject): self
    {
        $this->mediaObject = $mediaObject;

        return $this;
    }
}
