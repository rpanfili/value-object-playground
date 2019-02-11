<?php declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\PostalAddress;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

final class PostalAddressNormalizer implements NormalizerAwareInterface, ContextAwareNormalizerInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'POSTAL_ADDDRESS_NORM_CALLED';
  
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    { 
        return $context[self::ALREADY_CALLED] ?? false ? 
            false : 
            $data instanceof PostalAddress;
    }
    
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $normalized = $this->normalizer->normalize($object, $format, $context);
        unset($normalized['@id']);
        return $normalized;
    }
}
