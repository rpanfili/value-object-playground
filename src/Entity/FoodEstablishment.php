<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Traits\GeoCoordinates;

/**
 * @ApiResource(
 *   iri="http://schema.org/FoodEstablishment"
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FoodEstablishmentRepository")
 */
class FoodEstablishment
{
    use GeoCoordinates;
  
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

    public function getId(): ?int
    {
        return $this->id;
    }

}
