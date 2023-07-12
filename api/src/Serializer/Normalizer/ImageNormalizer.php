<?php

namespace App\Serializer\Normalizer;

use App\Entity\Bank;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Vich\UploaderBundle\Storage\StorageInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ImageNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_NORMALIZER_ALREADY_CALLED';


    public function __construct(private readonly StorageInterface $storage, private readonly RequestContext $requestContext)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Bank;
    }

    /**
     * @inheritDoc
     *
     * @param Bank $object
     */
    public function normalize($object, string $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);

        if ($object->getImageName() !== null) {
            $uri = $this->storage->resolveUri($object, 'imageFile');

            $data['image'] = 'https://' . $this->requestContext->getHost() . $uri;
        }
        return $data;
    }


}