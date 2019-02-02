<?php declare(strict_types=1);

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
    public $latitude;

    /**
     * @var float The longitude of a location
     * @Assert\Type("numeric")
     * @Assert\NotNull
     * @Assert\GreaterThanOrEqual(-180)
     * @Assert\LessThanOrEqual(180)
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=false)
     */
    public $longitude;
    
}