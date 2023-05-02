<?php

namespace App\Shared\Dto;

use App\Shared\DBAL\Types\TranslationType;
use Locastic\ApiPlatformTranslationBundle\Model\TranslatableInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\String\Inflector\EnglishInflector;

class DtoToEntityWriter
{
    private EnglishInflector $inflector;

    public function __construct()
    {
        $this->inflector = new EnglishInflector();
    }

    public function writeToEntity(mixed $entity, mixed $inputData): mixed
    {
        $propertyAccessor = new PropertyAccessor();
        foreach ($inputData as $name => $inputDatum) {
            if ($inputDatum instanceof FormInput) {
                $parameterType = (new ReflectionExtractor())->getTypes($entity::class, $name)[0]->getClassName();
                if ($propertyAccessor->isReadable($entity, $name)) {
                    $subEntity = $propertyAccessor->getValue($entity, $name) ?: new $parameterType();
                } else {
                    $subEntity = new $parameterType();
                }
                $subEntity = $this->writeToEntity($subEntity, $inputDatum);

                ($propertyAccessor)->setValue($entity, $name, $subEntity);
                continue;
            }

            if ($inputDatum instanceof TranslationType && $entity instanceof TranslatableInterface) {
                $field = str_replace('Translations', '', $name);
                foreach ((array)$inputDatum as $locale => $content) {
                    $entity->setCurrentLocale($locale);
                    // TODO: keep only else part once we remove tedious translation setters
                    if ($propertyAccessor->isWritable($entity, $field)) {
                        $propertyAccessor->setValue($entity, $field, $content);
                    } else {
                        $translation = $entity->getTranslation();
                        $propertyAccessor->setValue($translation, $field, $content);
                    }
                }
                continue;
            }

            if (is_array($inputDatum) && ($inputDatum[0] ?? null) instanceof FormInput) {
                $getter = 'get'.ucfirst($name);
                $propertyAccessor = new PropertyAccessor();
                $singular = $this->inflector->singularize($name)[0];
                $remover = 'remove'.ucfirst($singular);
                $adder = 'add'.ucfirst($singular);

                // clear existing subentities
                foreach ($entity->{$getter}() as $item) {
                    $entity->{$remover}($item);
                }

                foreach ($inputDatum as $input) {
                    // add subentities
                    $parameterType = (new \ReflectionClass($entity))->getMethod($adder)->getParameters()[0]->getType();
                    if (!method_exists($parameterType, 'getName')) {
                        continue;
                    }
                    $parameterType = $parameterType->getName();
                    $subEntity = new $parameterType();
                    $subEntity = $this->writeToEntity($subEntity, $input);
                    $entity->{$adder}($subEntity);
                }
                continue;
            }

            ($propertyAccessor)->setValue($entity, $name, $inputDatum);
        }

        return $entity;
    }
}
