<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN')"},
 *         "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('ROLE_ADMIN')"},
 *          "put"={"security"="is_granted('ROLE_ADMIN')"},
 *          "delete"={"security"="is_granted('ROLE_ADMIN')"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\DepartmentRepository")
 */
class Department
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ApiProperty(identifier=false)
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @ApiProperty(identifier=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Email", mappedBy="department")
     */
    private $emails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appeal", mappedBy="department")
     */
    private $appeals;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
        $this->appeals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
            $email->addDepartment($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        if ($this->emails->contains($email)) {
            $this->emails->removeElement($email);
            $email->removeDepartment($this);
        }

        return $this;
    }

    /**
     * @return Collection|Appeal[]
     */
    public function getAppeals(): Collection
    {
        return $this->appeals;
    }

    public function addAppeal(Appeal $appeal): self
    {
        if (!$this->appeals->contains($appeal)) {
            $this->appeals[] = $appeal;
            $appeal->setDepartment($this);
        }

        return $this;
    }

    public function removeAppeal(Appeal $appeal): self
    {
        if ($this->appeals->contains($appeal)) {
            $this->appeals->removeElement($appeal);
            // set the owning side to null (unless already changed)
            if ($appeal->getDepartment() === $this) {
                $appeal->setDepartment(null);
            }
        }

        return $this;
    }
}
