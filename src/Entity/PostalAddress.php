<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\City;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *   iri="http://schema.org/PostalAddress",
 *   collectionOperations={"get"},
 *   itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PostalAddressRepository")
 */
class PostalAddress
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null The street address. For example, 1600 Amphitheatre Pkwy.
     * @ORM\Column(type="string", length=255)
     * @Groups({"food_establishment:write", "food_establishment:read"})
     */
    public $streetAddress;

    /**
     * @var string|null The postal code. For example, 94043.
     * @ORM\Column(type="string", length=6, nullable=true)
     * @Groups({"food_establishment:write", "food_establishment:read"})
     */
    public $postalCode;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"food_establishment:write", "food_establishment:read"})
     * @Assert\NotBlank
     */
    public $city;

    /**
     * @var string|null The country. For example, USA
     * @Groups({"food_establishment:read"})
     */
    private $country;

    /**
     * @var string The locality. For example, Mountain View.
     * @Groups({"food_establishment:read"})
     */
    private $addressLocality;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string The country. For example, USA
     * @Groups({"food_establishment:read"})
     */
    public function getAddressCountry(): string
    {
        return (string)$this->city->country;
    }

    /**
     * @return string The locality. For example, Mountain View.
     * @Groups({"food_establishment:read"})
     */
    public function getAddressLocality(): string
    {
        return (string)$this->city;
    }

}
