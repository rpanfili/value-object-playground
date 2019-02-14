<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Embeddable\GeoCoordinates;
use App\Entity\PostalAddress;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Validator\Constraints as AppAssert;
use App\Entity\ValueObject\OpeningHours;

/**
 * @ApiResource(
 *   iri="http://schema.org/FoodEstablishment",
 *   normalizationContext={"groups"={
 *      "food_establishment:read"
 *   }},
 *   denormalizationContext={"groups"={
 *      "food_establishment:write"
 *   }},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FoodEstablishmentRepository")
 */
class FoodEstablishment
{  
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string The name of the location
     * @ORM\Column(type="string", length=255)
     * @ApiProperty(iri="http://schema.org/name")
     * @Assert\NotBlank
     * @Groups({"food_establishment:write", "food_establishment:read"})
     */
    public $name;

    /**
     * Geo coordinates
     * @var GeoCoordinates The geographic coordinates
     * @ORM\Embedded(class=GeoCoordinates::class, columnPrefix=false)
     * @ApiProperty(
     *   iri="https://schema.org/GeoCoordinates",
     *   attributes = {
     *     "swagger_context"={
     *       "type"="object",
     *       "properties"={
     *         "latitude"={
     *           "type"="string",
     *           "description"="The latitude of a location. For example 37.42242",
     *           "example"="37.42242"
     *         },
     *         "longitude"={
     *           "type"="string",
     *           "description"="The longitude of a location. For example -122.08585",
     *           "example"="-122.08585"
     *         },
     *       }
     *     }
     *   }  
     * )
     * @Assert\Valid
     * @Groups({"food_establishment:write", "food_establishment:read"})
     */
    public $geo;

    /**
     * @var PostalAddress The mailing address.
     * @ORM\OneToOne(targetEntity="App\Entity\PostalAddress", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)   
     * @ApiProperty(iri="http://schema.org/PostalAddress")
     * @Assert\Valid
     * @Groups({"food_establishment:write", "food_establishment:read"})
     */
    private $address;
    
    /**
     * @ORM\Column(type="opening_hours", nullable=true)
     * @Groups({"food_establishment:write", "food_establishment:read"})
     * @AppAssert\OpeningHours
     * @var OpeningHours
     */
    public $openingHours;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?PostalAddress
    {
        return $this->address;
    }

    public function setAddress(PostalAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

}
