<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Embeddable\GeoCoordinates;

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

    public function getId(): ?int
    {
        return $this->id;
    }

}
