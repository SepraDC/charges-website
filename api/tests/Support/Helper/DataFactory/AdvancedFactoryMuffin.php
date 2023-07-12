<?php

namespace App\Tests\Support\Helper\DataFactory;

use League\FactoryMuffin\FactoryMuffin;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class AdvancedFactoryMuffin extends FactoryMuffin
{
    protected function generate($model, array $attr = [])
    {
        foreach ($attr as $key => $kind) {
            $value = $this->factory->generate($kind, $model, $this);

            $propertyAccessor = new PropertyAccessor();
            try {
                ($propertyAccessor)->setValue($model, $key, $value);
            } catch (\Exception $e) {
                echo (sprintf(
                    PHP_EOL."\e[0;31mError while writing %s->%s : %s\e[0m\n".PHP_EOL,
                    get_class($model),
                    $key,
                    $e->getMessage()
                ) );
            }
        }
    }

    public function entity(string $class)
    {
        return "entity|$class";
    }
}
