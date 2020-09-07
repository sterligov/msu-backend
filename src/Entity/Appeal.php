<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints\ReCaptcha;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     iri="http://localhost:8888/Appeal",
 *     normalizationContext={
 *         "groups"={"appeal"}
 *     },
 *     attributes={
 *          "order"={"createdAt": "DESC", "id": "DESC"},
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN')"},
 *         "post"={"security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AppealRepository")
 */
class Appeal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"appeal"})
     */
    private int $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     * @Groups({"appeal"})
     */
    private string $address = '';

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=32)
     * @Groups({"appeal"})
     */
    private string $phone = '';

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, name="person_type")
     * @Groups({"appeal"})
     */
    private string $personType = '';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"appeal"})
     */
    private string $organization = '';

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\GreaterThan(value=1900)
     * @ORM\Column(type="integer", name="birth_year")
     * @Groups({"appeal"})
     */
    private ?int $birthYear = 0;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, name="full_name")
     * @Groups({"appeal"})
     */
    private string $fullName = '';

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(type="string", length=255)
     * @Groups({"appeal"})
     */
    private string $email = '';

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="text")
     * @Groups({"appeal"})
     */
    private string $message = '';

    /**
     * @ORM\Column(type="date", name="created_at")
     * @Groups({"appeal"})
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ApiProperty(iri="http://localhos:8888/media")
     * @ORM\OneToOne(targetEntity="App\Entity\MediaObject", cascade={"persist", "remove"})
     * @Groups({"appeal"})
     */
    private ?MediaObject $mediaObject = null;

    /**
     * @ReCaptcha
     */
    private string $gRecaptchaResponse = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="appeals")
     * @Groups({"appeal"})
     */
    private ?Department $department = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    public function setPersonType(string $personType): self
    {
        $this->personType = $personType;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getBirthYear(): ?int
    {
        return $this->birthYear;
    }

    public function setBirthYear(?int $birthYear): self
    {
        $this->birthYear = $birthYear;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getMediaObject(): ?MediaObject
    {
        return $this->mediaObject;
    }

    public function setMediaObject(?MediaObject $mediaObject): self
    {
        $this->mediaObject = $mediaObject;

        return $this;
    }

    public function setGRecaptchaResponse(string $gRecaptchaResponse)
    {
        $this->gRecaptchaResponse = $gRecaptchaResponse;
    }

    public function getGRecaptchaResponse(): string
    {
        return $this->gRecaptchaResponse;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
