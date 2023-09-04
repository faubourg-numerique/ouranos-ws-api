<?php

namespace API\Controllers;

use API\StaticClasses\DataModel;
use API\Managers\PropertyManager;
use API\Managers\TypeManager;
use API\Managers\WorkspaceManager;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class DataModelController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private TypeManager $typeManager;
    private PropertyManager $propertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->typeManager = new TypeManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
    }

    public function generate(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        DataModel::generate($workspace, $this->propertyManager, $this->typeManager, $this->workspaceManager);

        $workspace->dataModelUpToDate = true;
        $this->workspaceManager->update($workspace);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
