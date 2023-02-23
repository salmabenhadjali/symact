<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @ApiResource(
 *     collectionOperations={
 *          "GET",
 *          "POST"
 *      },
 *     itemOperations={
 *          "GET",
 *          "PUT",
 *          "DELETE"
 *      },
 *     subresourceOperations={
 *          "invoices_get_subresource":{"path":"/clients/{id}/invoices"}
 *     },
 *     normalizationContext={
 *      "groups"={"custpmers_read"}
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"firstName":"partial", "lastName", "compay"})
 * @ApiFilter(OrderFilter::class, properties={"user.lastName"})
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"custpmers_read", "invoices_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"custpmers_read", "invoices_read"})
     * @Assert\NotBlank(message="The firstname is mandatory.")
     * @Assert\Length(
     *     min=3, minMessage="firstname should be at least 3 caracters.",
     *     max=255, maxMessage="firstname should be at most 255 caracters."
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"custpmers_read", "invoices_read"})
     * @Assert\NotBlank(message="The lastName is mandatory.")
     * @Assert\Length(
     *     min=3, minMessage="lastName should be at least 3 caracters.",
     *     max=255, maxMessage="lastName should be at most 255 caracters."
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"custpmers_read", "invoices_read"})
     * @Assert\NotBlank(message="The firstname is mandatory")
     * @Assert\Email(message="The email {{ value }} is not a valid format.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"custpmers_read", "invoices_read"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="customer")
     * @Groups("custpmers_read")
     * @ApiSubresource()
     */
    private $invoices;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="customers")
     * @Groups({"custpmers_read"})
     * @Assert\NotBlank(message="The firstname is mandatory")
     */
    private $user;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    /**
     * return total invoices of the customer
     *
     * @Groups({"custpmers_read"})
     * @return float
     */
    public function getTotalAmount() : float
    {
        return array_reduce($this->invoices->toArray(), function($total, $invoice) {
            return $total + $invoice->getAmount();
        }, 0);
    }

    /**
     * return total unpaid invoices of the customer
     *
     * @Groups({"custpmers_read"})
     * @return float
     */
    public function getUnpaidAmount() : float
    {
        return array_reduce($this->invoices->toArray(), function($total, $invoice) {
            return $total + ($invoice->getStatus() === 'PAID' || $invoice->getStatus() === 'CANCELED' ? 0 : $invoice->getAmount());
        }, 0);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
