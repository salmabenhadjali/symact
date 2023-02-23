<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ApiResource(
 *     itemOperations={
 *          "GET", "PUT", "DELETE",
 *          "increment"={
 *              "method"="POST",
 *              "path"="/invoices/{id}/increment",
 *              "controller"="App\Controller\InvoiceIncrementController",
 *              "openapi_context"={
 *                  "summary"="Increment the invoice",
 *                  "description"="Increment the chrono of a current invoice"
 *              }
 *          }
 *     },
 *     subresourceOperations={
 *          "api_customers_invoices_get_subresource"={
 *              "normalization_context":{"groups"="invoices_subresource"}
 *          }
 *     },
 *     attributes={
 *          "pagination_enabled"=true,
 *          "pagination_items_per_page"=20,
 *          "order": {"amount":"desc"}
 *     },
 *     normalizationContext={
 *          "groups"={"invoices_read"}
 *     },
 *     denormalizationContext={
 *          "disable_type_enforcement"=true
 *     }
 * )
 * @ApiFilter(OrderFilter::class)
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "custpmers_read", "invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read", "custpmers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The amount is mandatory.")
     * @Assert\Type(type="numeric", message="The amount should be a float.")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read", "custpmers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The sent date is mandatory.")
     * @Assert\Type(type="DateTimeInterface", message="The sent date should have the format YYYY-MM-dd")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read", "custpmers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The status is mandatory.")
     * @Assert\Choice({"SENT", "PAID", "CANCELED"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("invoices_read")
     * @Assert\NotBlank(message="The customer is mandatory.")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "custpmers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The chrono is mandatory.")
     * @Assert\Type(type="integer", message="The amout should be an integer.")
     */
    private $chrono;

    /**
     * return User
     *
     * @Groups({"invoices_read", "invoices_subresource"})
     * @return User
     */
    public function getUser() : User
    {
        return $this->getCustomer()->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono($chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
