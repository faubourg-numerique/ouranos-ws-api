<?php

namespace API\Traits;

trait Updatable
{
    public function update(array $data): void
    {
        $propertiesNames = array_keys(get_class_vars($this::class));

        foreach ($propertiesNames as $name) {
            if ($name === "id") {
                continue;
            }

            $this->$name = array_key_exists($name, $data) ? $data[$name] : null;
        }
    }
}
