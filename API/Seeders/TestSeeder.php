<?php

namespace API\Seeders;

use API\StaticClasses\DataModel;
use API\StaticClasses\Utils;
use API\Enums\NgsiLdAttributeType;
use API\Enums\NgsiLdGeoPropertyType;
use API\Enums\NgsiLdPropertyValueType;
use API\Enums\StandardDataModelType;
use API\Managers\ContextBrokerManager;
use API\Managers\IdentityManagerGrantManager;
use API\Managers\IdentityManagerManager;
use API\Managers\PropertyManager;
use API\Managers\ServiceManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use API\Models\ContextBroker;
use API\Models\IdentityManager;
use API\Models\IdentityManagerGrant;
use API\Models\Property;
use API\Models\Service;
use API\Models\Type;
use API\Models\Workspace;

final class TestSeeder
{
    public static function seed(): void
    {
        global $systemEntityManager;

        $contextBrokerManager = new ContextBrokerManager($systemEntityManager);
        $identityManagerGrantManager = new IdentityManagerGrantManager($systemEntityManager);
        $identityManagerManager = new IdentityManagerManager($systemEntityManager);
        $serviceManager = new ServiceManager($systemEntityManager);
        $workspaceManager = new WorkspaceManager($systemEntityManager);
        $typeManager = new TypeManager($systemEntityManager);
        $propertyManager = new PropertyManager($systemEntityManager);

        $seedNumber = rand(100000, 999999);
        $loremIpsumText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi bibendum est lacus, non bibendum mi finibus ac. Sed quis mi vitae lectus viverra ultrices eget ut erat.";

        $contextBroker = new ContextBroker();
        $contextBroker->id = Utils::generateUniqueNgsiLdUrn(ContextBroker::TYPE);
        $contextBroker->name = "Context broker {$seedNumber}";
        $contextBroker->description = $loremIpsumText;
        $contextBroker->scheme = $_ENV["TEST_SEEDER_CONTEXT_BROKER_SCHEME"];
        $contextBroker->host = $_ENV["TEST_SEEDER_CONTEXT_BROKER_HOST"];
        $contextBroker->port = $_ENV["TEST_SEEDER_CONTEXT_BROKER_PORT"];
        if (isset($_ENV["TEST_SEEDER_CONTEXT_BROKER_PATH"])) {
            $contextBroker->path = $_ENV["TEST_SEEDER_CONTEXT_BROKER_PATH"];
        }
        $contextBroker->multiTenancyEnabled = Utils::convertToBoolean($_ENV["TEST_SEEDER_CONTEXT_BROKER_MULTI_TENANCY_ENABLED"]);
        $contextBroker->paginationMaxLimit = $_ENV["TEST_SEEDER_CONTEXT_BROKER_PAGINATION_MAX_LIMIT"];
        $contextBroker->implementationName = $_ENV["TEST_SEEDER_CONTEXT_BROKER_IMPLEMENTATION_NAME"];
        $contextBroker->implementationVersion = $_ENV["TEST_SEEDER_CONTEXT_BROKER_IMPLEMENTATION_VERSION"];
        $contextBrokerManager->create($contextBroker);

        if (Utils::convertToBoolean($_ENV["TEST_SEEDER_SERVICE_AUTHORIZATION_REQUIRED"])) {
            $identityManager = new IdentityManager();
            $identityManager->id = Utils::generateUniqueNgsiLdUrn(IdentityManager::TYPE);
            $identityManager->name = "Identity manager {$seedNumber}";
            $identityManager->description = $loremIpsumText;
            $identityManager->scheme = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_SCHEME"];
            $identityManager->host = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_HOST"];
            $identityManager->port = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_PORT"];
            if (isset($_ENV["TEST_SEEDER_IDENTITY_MANAGER_PATH"])) {
                $identityManager->path = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_PATH"];
            }
            $identityManager->oauth2TokenPath = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_OAUTH2_TOKEN_PATH"];
            $identityManager->implementationName = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_IMPLEMENTATION_NAME"];
            $identityManager->implementationVersion = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_IMPLEMENTATION_VERSION"];
            $identityManagerManager->create($identityManager);

            $identityManagerGrant = new IdentityManagerGrant();
            $identityManagerGrant->id = Utils::generateUniqueNgsiLdUrn(IdentityManagerGrant::TYPE);
            $identityManagerGrant->name = "Identity manager grant {$seedNumber}";
            $identityManagerGrant->description = $loremIpsumText;
            $identityManagerGrant->grantType = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_GRANT_GRANT_TYPE"];
            $identityManagerGrant->clientId = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_GRANT_CLIENT_ID"];
            $identityManagerGrant->clientSecret = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_GRANT_CLIENT_SECRET"];
            if ($identityManagerGrant->grantType === "password") {
                $identityManagerGrant->username = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_GRANT_USERNAME"];
                $identityManagerGrant->password = $_ENV["TEST_SEEDER_IDENTITY_MANAGER_GRANT_PASSWORD"];
            }
            $identityManagerGrantManager->create($identityManagerGrant);
        }

        $service = new Service();
        $service->id = Utils::generateUniqueNgsiLdUrn(Service::TYPE);
        $service->name = "Service {$seedNumber}";
        $service->description = $loremIpsumText;
        $service->authorizationRequired = Utils::convertToBoolean($_ENV["TEST_SEEDER_SERVICE_AUTHORIZATION_REQUIRED"]);
        $service->hasContextBroker = $contextBroker->id;
        if ($service->authorizationRequired) {
            $service->hasIdentityManager = $identityManager->id;
            $service->hasIdentityManagerGrant = $identityManagerGrant->id;
        }
        $serviceManager->create($service);

        $workspace = new Workspace();
        $workspace->id = Utils::generateUniqueNgsiLdUrn(Workspace::TYPE);
        $workspace->name = "Workspace {$seedNumber}";
        $workspace->description = $loremIpsumText;
        $workspace->dataModelName = "test-workspace-{$seedNumber}";
        $workspace->dataModelVersion = 0;
        if (isset($_ENV["TEST_SEEDER_WORKSPACE_CONTEXT_BROKER_TENANT"])) {
            $workspace->contextBrokerTenant = $_ENV["TEST_SEEDER_WORKSPACE_CONTEXT_BROKER_TENANT"];
        }
        $workspace->defaultDataModelUrl = "https://example.com";
        $workspace->temporalServices = [];
        $workspace->hasService = $service->id;
        $workspaceManager->create($workspace);

        $type = new Type();
        $type->id = Utils::generateUniqueNgsiLdUrn(Type::TYPE);
        $type->name = "City";
        $type->description = $loremIpsumText;
        $type->url = "https://schema.org/City";
        $type->standardDataModelBased = false;
        $type->hasWorkspace = $workspace->id;
        $typeManager->create($type);

        $cityTypeId = $type->id;

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "name";
        $property->description = $loremIpsumText;
        $property->url = "https://schema.org/name";
        $property->ngsiLdType = NgsiLdAttributeType::Property->value;
        $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::String->value;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "description";
        $property->description = $loremIpsumText;
        $property->url = "https://schema.org/description";
        $property->ngsiLdType = NgsiLdAttributeType::Property->value;
        $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::String->value;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "location";
        $property->description = $loremIpsumText;
        $property->url = "https://schema.org/location";
        $property->ngsiLdType = NgsiLdAttributeType::GeoProperty->value;;
        $property->geoPropertyNgsiLdType = NgsiLdGeoPropertyType::Point->value;;
        $property->geoPropertyGeographic = true;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $type = new Type();
        $type->id = Utils::generateUniqueNgsiLdUrn(Type::TYPE);
        $type->name = "Building";
        $type->description = $loremIpsumText;
        $type->standardDataModelBased = true;
        $type->standardDataModelType = StandardDataModelType::SmartDataModels->value;
        $type->standardDataModelDefinitionUrl = "https://raw.githubusercontent.com/smart-data-models/dataModel.Building/master/Building/model.yaml";
        $type->hasWorkspace = $workspace->id;
        $typeManager->create($type);

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "category";
        $property->description = $loremIpsumText;
        $property->ngsiLdType = NgsiLdAttributeType::Property->value;
        $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::String->value;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $categoryPropertyId = $property->id;

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "subcategory";
        $property->description = $loremIpsumText;
        $property->url = "https://example.com/subcategory";
        $property->ngsiLdType = NgsiLdAttributeType::Property->value;
        $property->propertyNgsiLdValueType = NgsiLdPropertyValueType::Number->value;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasProperty = $categoryPropertyId;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $property = new Property();
        $property->id = Utils::generateUniqueNgsiLdUrn(Property::TYPE);
        $property->name = "hasCity";
        $property->description = $loremIpsumText;
        $property->url = "https://example.com/hasCity";
        $property->ngsiLdType = NgsiLdAttributeType::Relationship->value;
        $property->relationshipType = $cityTypeId;
        $property->standard = true;
        $property->mandatory = true;
        $property->temporal = false;
        $property->hasType = $type->id;
        $property->hasWorkspace = $workspace->id;
        $propertyManager->create($property);

        $dataModel = new DataModel();
        DataModel::generate($workspace, $propertyManager, $typeManager, $workspaceManager);
    }
}
