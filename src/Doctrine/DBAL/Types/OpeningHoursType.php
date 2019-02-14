<?php declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use App\Entity\ValueObject\OpeningHours;

/**
 * Schema.org OpeningHours Doctrine mapping type.
 */
class OpeningHoursType extends JsonType
{
    /**
     * Type name.
     */
    const NAME = 'opening_hours';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof OpeningHours) {
            throw new ConversionException('Expected '. OpeningHours::class .', got ' . gettype($value));
        }

        dump($value->getIntervals());
        die(__METHOD__);

        return parent::convertToDatabaseValue($value->getIntervals(), $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $intervals = parent::convertToPHPValue($value, $platform);
        return new OpeningHours($intervals);
    }
}
