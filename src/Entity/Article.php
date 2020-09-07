<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\String\Slugger\AsciiSlugger;
use App\Controller\GetArticleBySlugAction;
use App\Filter\NotEmptyFilter;

/**
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"article"}
 *     },
 *     attributes={
 *          "force_eager"=false,
 *          "order"={"publishedAt": "DESC", "id": "DESC"},
 *          "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY', object)",
 *              "path"="/articles/{id}",
 *              "requirements"={"id"="\d+"}
 *          },
 *          "get_by_slug"={
 *              "method"="GET",
 *              "path"="/articles/slug/{slug}",
 *              "read"=false,
 *              "controller"=GetArticleBySlugAction::class
 *          },
 *          "put"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "slug": "exact",
 *     "tags.name": "exact"
 * })
 * @ApiFilter(DateFilter::class, properties={"publishedAt"})
 * @ApiFilter(NotEmptyFilter::class, properties={"previewImage"})
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=true)
     * @Groups({"article"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1024)
     * @Assert\NotBlank()
     * @Groups({"article"})
     */
    private $title = '';

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"article"})
     */
    private $text = '';

    /**
     * @ORM\Column(type="text", name="preview_text")
     * @Groups({"article"})
     */
    private $previewText = '';

    /**
     * @ORM\Column(type="datetime",  name="published_at")
     * @Groups({"article"})
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime",  name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles")
     * @Groups({"article"})
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=1024, unique=true)
     * @Groups({"article"})
     */
    private $slug = '';

    /**
     * @ApiProperty(iri="http://localhos:8888/media")
     * @ORM\OneToMany(targetEntity="App\Entity\MediaObject", mappedBy="article", cascade={"remove"})
     * @Groups({"article"})
     */
    private $mediaObjects;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PreviewImage", mappedBy="article", cascade={"remove"})
     * @Groups({"article"})
     */
    private $previewImage;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->publishedAt = new \DateTime();
        $this->mediaObjects = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setSlugValue()
    {
        if ($this->slug === '') {
            $date = $this->publishedAt->format('Y-m-d-');
            $this->slug = $date . mb_strtolower($this->title);
        }

        $slugger = new AsciiSlugger('ru');
        $this->slug = $slugger->slug($this->slug)->toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPreviewText(): ?string
    {
        return $this->previewText;
    }

    public function setPreviewText(string $previewText): self
    {
        $this->previewText = $previewText;

        return $this;
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|MediaObject[]
     */
    public function getMediaObjects(): Collection
    {
        return $this->mediaObjects;
    }

    public function addMediaObject(MediaObject $mediaObject): self
    {
        if (!$this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects[] = $mediaObject;
            $mediaObject->setArticle($this);
        }

        return $this;
    }

    public function removeMediaObject(MediaObject $mediaObject): self
    {
        if ($this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->removeElement($mediaObject);
            // set the owning side to null (unless already changed)
            if ($mediaObject->getArticle() === $this) {
                $mediaObject->setArticle(null);
            }
        }

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
        if ($previewImage->getArticle() !== $this) {
            $previewImage->setArticle($this);
        }

        return $this;
    }
}
