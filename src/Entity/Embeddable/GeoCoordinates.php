<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class GeoCoordinates
{
    /**
     * @var float The latitude of a location
     * @Assert\Type("numeric")
     * @Assert\NotNull
     * @Assert\GreaterThanOrEqual(-90)
     * @Assert\LessThanOrEqual(90)
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=false)
     */
    private $latitude;

    /**
     * @var float The longitude of a location
     * @Assert\Type("numeric")
     * @Assert\NotNull
     * @Assert\GreaterThanOrEqual(-180)
     * @Assert\LessThanOrEqual(180)
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=false)
     */
    private $longitude;
    
    /**
     * @param string $latitude  The latitude of a location
     * @param string $longitude The longitude of a location
     */
    public function __construct(string $latitude, string $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
    
    public function getLatitude(): string
    {
        return $this->latitude;
    }
    
    public function getLongitude(): string
    {
        return $this->longitude;
    }
}
