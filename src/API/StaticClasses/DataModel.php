<?php

namespace API\StaticClasses;

use API\Exceptions\StaticClassException\DataModelException;
use API\Managers\PropertyManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\Workspace;

class DataModel
{
    public static function generate(Workspace $workspace, PropertyManager $propertyManager, TypeManager $typeManager, WorkspaceManager $workspaceManager): void
    {
        $path = Utils::replacePathSeparators($_ENV["DATA_MODELS_DIRECTORY_PATH"], DIRECTORY_SEPARATOR);

        if (!is_dir($path) || !is_writable($path)) {
            throw new DataModelException\GenerationException();
        }

        $path = Utils::buildSystemPath($path, $workspace->dataModelName);

        if (!is_dir($path)) {
            if (file_exists($path)) {
                throw new DataModelException\GenerationException();
            }

            $result = mkdir($path);
            if (!$result) {
                throw new DataModelException\GenerationException();
            }
        }

        $workspace->dataModelVersion += 1;
        $path = Utils::buildSystemPath($path, $workspace->dataModelVersion);

        if (file_exists($path)) {
            throw new DataModelException\GenerationException();
        }

        $result = mkdir($path);
        if (!$result) {
            throw new DataModelException\GenerationException();
        }

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $types = $typeManager->readMultiple($query, true);

        $propertiesByTypeIds = [];
        foreach ($types as $type) {
            $query = "hasType==\"{$type->id}\"";
            $properties = $propertyManager->readMultiple($query, true);
            $propertiesByTypeIds[$type->id] = $properties;
        }

        $files = [];
        $fullContext = [];
        foreach ($propertiesByTypeIds as $typeId => $properties) {
            $context = [];
            $type = $types[$typeId];

            if (!is_null($type->url)) {
                $context[$type->name] = $type->url;
                $fullContext[$type->name] = $type->url;
            }

            foreach ($properties as $property) {
                if (is_null($property->url)) continue;
                $context[$property->name] = $property->url;
                $fullContext[$property->name] = $property->url;
            }

            ksort($context);
            $fileName = Utils::buildSystemPath($type->name, "context.jsonld");
            $files[$fileName] = ["@context" => $context];
        }

        ksort($fullContext);
        $fileName = "context.jsonld";
        $files[$fileName] = ["@context" => $fullContext];

        foreach ($types as $type) {
            $typeDirectoryPath = Utils::buildSystemPath($path, $type->name);

            $result = mkdir($typeDirectoryPath);
            if (!$result) {
                throw new DataModelException\GenerationException();
            }
        }

        foreach ($files as $fileName => $data) {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $result = file_put_contents(Utils::buildSystemPath($path, $fileName), $json);

            if ($result === false) {
                throw new DataModelException\GenerationException();
            }
        }

        $workspaceManager->update($workspace);
    }
}
