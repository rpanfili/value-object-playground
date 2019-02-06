<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Embeddable\GeoCoordinates;
use App\Entity\Embeddable\PostalAddress;

/**
 * @ApiResource(
 *   iri="http://schema.org/FoodEstablishment"
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
     */
    public $geo;
    
    /**
     * @var PostalAddress Physical address of the item.
     * @ORM\Embedded(class=PostalAddress::class, columnPrefix="address_")
     * @ApiProperty(
     *   iri="https://schema.org/PostalAddress",
     *   attributes = {
     *     "swagger_context"={
     *       "type"="object",
     *       "properties"={
     *         "addressCountry"={
     *           "type"="string",
     *           "description"="The country. You can also provide the two-letter ISO 3166-1 alpha-2 country code.",
     *           "example"="USA"
     *         },
     *         "addressLocality"={
     *           "type"="string",
     *           "description"="The locality.",
     *           "example"="Mountain View"
     *         },
     *         "postalCode"={
     *           "type"="string",
     *           "description"="The postal code.",
     *           "example"="94043"
     *         },
     *         "streetAddress"={
     *           "type"="string",
     *           "description"="The street address.",
     *           "example"="1600 Amphitheatre Pkwy"
     *         },
     *       }
     *     }
     *   }  
     * )
     * @Assert\Valid
     */
    public $address;
    
    public function getId(): ?int
    {
        return $this->id;
    }

}
