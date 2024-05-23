<?php

namespace API\StaticClasses;

use API\Enums\MimeType;
use API\Models\ErrorInfo;
use Core\API;
use Core\HttpResponseStatusCodes;
use Throwable;

class ExceptionHandler
{
    public static function handler(Throwable $throwable)
    {
        try {
            throw $throwable;
        } catch (\API\Exceptions\ControllerException\ContextBrokerControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this context broker";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\IdentityManagerControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this identity manager";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\IdentityManagerGrantControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this identity manager grant";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\PropertyControllerException\BadWorkspaceException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "This property does not belong to the right workspace";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\PropertyControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this property";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\PropertyControllerException\UrlInvalidException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "A type or a property with the same name uses a different url";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\ServiceControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this service";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\TemporalServiceControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this temporal service";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\TypeControllerException\BadWorkspaceException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "This type does not belong to the right workspace";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\TypeControllerException\NameAlreadyUsedException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The name of this type is already used";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\TypeControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this type";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\TypeControllerException\UrlInvalidException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "A type or a property with the same name uses a different url";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ControllerException\WorkspaceControllerException\RelationshipException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "At least one entity depends on this workspace";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\EntityManagerException\CreationException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The creation of the entity failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\EntityManagerException\DeletionException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The deletion of the entity failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\EntityManagerException\EmptyIdException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The id of the entity is empty";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\EntityManagerException\RetrievalException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The retrieval of the entity/entities failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\EntityManagerException\UpdateException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The update of the entity failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\TemporalEntityPropertyManagerException\EmptyIdException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The id of the temporal entity is empty";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\TemporalEntityPropertyManagerException\NotSupportedTypeException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The temporal service type is not supported";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\TemporalEntityPropertyManagerException\RetrievalException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The retrieval of the temporal entity property failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\SubscriptionManagerException\CreationException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The creation of the subscription failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\SubscriptionManagerException\DeletionException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The deletion of the subscription failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\SubscriptionManagerException\EmptyIdException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The id of the subscription is empty";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\ManagerException\SubscriptionManagerException\RetrievalException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The retrieval of the subscription/subscriptions failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\StaticClassException\AuthorizationException\AccessTokenRequestException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The access token request failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\StaticClassException\AuthorizationException\AuthorizationDecisionRequestException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The authorization decision request failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\StaticClassException\AuthorizationException\UserRequestException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The user request failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\StaticClassException\AuthorizationException\TokensRequestException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The tokens request failed";
            if (isset($e->details)) $errorInfo->details = $e->details;

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\API\Exceptions\StaticClassException\DataModelException\GenerationException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The data model generation failed";

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        } catch (\Respect\Validation\Exceptions\NestedValidationException $e) {
            $errorInfo = new ErrorInfo();
            $errorInfo->title = "The submitted data is invalid";
            $errorInfo->details = array_values($e->getMessages());

            API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_BAD_REQUEST);
            API::response()->setHeader("Content-Type", MimeType::Json->value);
            API::response()->setJsonBody($errorInfo, JSON_UNESCAPED_SLASHES);
            API::response()->send();
        }

        throw $throwable;
    }
}
