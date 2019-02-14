<?php declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\FoodEstablishment;

final class FoodEstablishmentNormalizer implements DenormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    private $decorated;
    private $iriConverter;
      
    public function __construct(DenormalizerInterface $decorated, IriConverterInterface $iriConverter)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }
        
        $this->decorated = $decorated;
        $this->iriConverter = $iriConverter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->decorated->supportsDenormalization($data, $type, $format);
    }
    
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (
            $class === FoodEstablishment::class &&
            ($addressData = &$data['address'] ?? null) &&
            ($persisted = $context[$this->decorated::OBJECT_TO_POPULATE] ?? null) &&
            ($currentAddress = $persisted->getAddress())
        ) {
            dump($data);
            $addressData['id'] = $this->iriConverter->getIriFromItem($currentAddress);
        }
        
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === \get_class($this);
    }
}
