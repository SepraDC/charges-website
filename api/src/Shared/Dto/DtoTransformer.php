<?php

namespace App\Shared\Dto;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class DtoTransformer implements DataTransformerInterface
{

    public function __construct(private readonly ValidatorInterface $validator, private readonly DtoToEntityWriter $dtoToEntityWriter)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $entity = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new $to();
        $inputData = get_object_vars($object);
        $this->validator->validate($object);
        return $this->dtoToEntityWriter->writeToEntity($entity, $inputData);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if (!isset($context['input']['class'])) {
            return false;
        }
        return is_a($context['input']['class'], FormInput::class, true);
    }
}
