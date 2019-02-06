<?php declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class PostalAddress
{
    /**
     * @var string The country. For example, USA. You can also provide the two-letter ISO 3166-1 alpha-2 country code.
     * @ORM\Column(type="string", name="country", length=100)
     * @Assert\NotNull
     */
    public $addressCountry;
    
    /**
     * @var string The locality. For example, Mountain View.
     * @ORM\Column(type="string", name="locality", length=255)
     * @Assert\NotNull
     */
    public $addressLocality;

    /**
     * @var string|null The postal code. For example, 94043.
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    public $postalCode;
    
    /**
     * @var string|null The street address. For example, 1600 Amphitheatre Pkwy.
     * @ORM\Column(type="string", name="street", length=255)
     * @Assert\NotNull
     */
    public $streetAddress;
}