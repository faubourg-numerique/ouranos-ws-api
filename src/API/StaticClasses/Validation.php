<?php

namespace API\StaticClasses;

use API\Enums\AuthorizationMode;
use API\Enums\ContextBrokerImplementationName;
use API\Enums\DataModelGroup;
use API\Enums\IdentityManagerGrantType;
use API\Enums\IdentityManagerImplementationName;
use API\Enums\NgsiLdAttributeType;
use API\Enums\NgsiLdGeoPropertyType;
use API\Enums\NgsiLdPropertyValueType;
use API\Enums\Scheme;
use API\Enums\StandardDataModelType;
use API\Enums\TemporalService;
use API\Modules\DSC\Enums\AuthorizationRegistryImplementationName;
use API\Modules\DSC\Enums\VCVerifierImplementationName;
use API\Modules\DSC\Enums\TrustedIssuersListImplementationName;
use Respect\Validation\Rules;

final class Validation
{
    public static function booleanValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\BoolType()
        );
    }

    public static function clientIdValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank()
        );
    }

    public static function clientSecretValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank()
        );
    }

    public static function contextBrokerImplementationNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(ContextBrokerImplementationName::values(), true)
        );
    }

    public static function contextBrokerTenantValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank(),
            new Rules\Alnum("_"),
            new Rules\Lowercase(),
            new Rules\Length(1, 50)
        );
    }

    public static function dataModelNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank(),
            new Rules\Alnum("-"),
            new Rules\Lowercase(),
            new Rules\Length(1, 50)
        );
    }

    public static function dataModelVersionValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\IntType(),
            new Rules\Min(0)
        );
    }

    public static function descriptionValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Length(1, 10000)
        );
    }

    public static function grantTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(IdentityManagerGrantType::values(), true)
        );
    }

    public static function hostValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Length(1, 1000)
        );
    }

    public static function identityManagerImplementationNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(IdentityManagerImplementationName::values(), true)
        );
    }

    public static function vcVerifierImplementationNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(VCVerifierImplementationName::values(), true)
        );
    }

    public static function trustedIssuersListImplementationNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(TrustedIssuersListImplementationName::values(), true)
        );
    }

    public static function authorizationModeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(AuthorizationMode::values(), true)
        );
    }

    public static function nameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Length(1, 50)
        );
    }

    public static function ngsiLdAttributeTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(NgsiLdAttributeType::values(), true)
        );
    }

    public static function ngsiLdGeoPropertyTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(NgsiLdGeoPropertyType::values(), true)
        );
    }

    public static function ngsiLdPropertyValueTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(NgsiLdPropertyValueType::values(), true)
        );
    }

    public static function paginationMaxLimitValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\IntType(),
            new Rules\Min(1)
        );
    }

    public static function passwordValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotOptional()
        );
    }

    public static function pathValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\StartsWith("/"),
            new Rules\Length(1, 1000)
        );
    }

    public static function portValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\IntType(),
            new Rules\Between(0, 65535)
        );
    }

    public static function positionInChartValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\Length(2, 2),
            new Rules\Each(
                new Rules\AnyOf(
                    new Rules\IntType(),
                    new Rules\FloatType()
                )
            )
        );
    }

    public static function propertyNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Alnum("-", "_"),
            new Rules\Length(1, 50)
        );
    }

    public static function schemeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(Scheme::values(), true)
        );
    }

    public static function standardDataModelTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(StandardDataModelType::values(), true)
        );
    }

    public static function temporalServiceTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(TemporalService::values(), true)
        );
    }

    public static function typeNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Alnum("-", "_"),
            new Rules\Length(1, 50)
        );
    }

    public static function urlValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Url()
        );
    }

    public static function urnArrayValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\Each(self::urnValidator())
        );
    }

    public static function urnValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\StartsWith("urn:", true),
            new Rules\Alnum(":", "-")
        );
    }

    public static function usernameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotOptional()
        );
    }

    public static function versionValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Version()
        );
    }

    public static function didValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType()
        );
    }

    public static function prohibitedValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\AlwaysInvalid
        );
    }

    public static function subscriptionTypeValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\Identical("Subscription")
        );
    }

    public static function nullValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\NullType()
        );
    }

    public static function headersValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\NotEmpty(),
            new Rules\Each(
                new Rules\StringType()
            )
        );
    }

    public static function dataModelGroupValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(DataModelGroup::values(), true)
        );
    }

    public static function validateContextBroker(mixed $data): void
    {
        (new Rules\Key("scheme", self::schemeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("multiTenancyEnabled", self::booleanValidator(), true),
            new Rules\Key("paginationMaxLimit", self::paginationMaxLimitValidator(), true),
            new Rules\Key("implementationName", self::contextBrokerImplementationNameValidator(), true),
            new Rules\Key("implementationVersion", self::versionValidator(), true),
            new Rules\Key("customHeaders", new Rules\Nullable(self::headersValidator()), false)
        ];

        if ($data["scheme"] === Scheme::Https->value) {
            $keys[] = new Rules\Key("disableCertificateVerification", self::booleanValidator(), true);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateIdentityManagerGrant(mixed $data): void
    {
        (new Rules\Key("grantType", self::grantTypeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("grantType", self::grantTypeValidator(), true),
            new Rules\Key("clientId", self::clientIdValidator(), true),
            new Rules\Key("clientSecret", self::clientSecretValidator(), true)
        ];

        if ($data["grantType"] === IdentityManagerGrantType::Password->value) {
            $keys[] = new Rules\Key("username", self::usernameValidator(), true);
            $keys[] = new Rules\Key("password", self::passwordValidator(), true);
        } else {
            $keys[] = new Rules\Key("username", self::nullValidator(), false);
            $keys[] = new Rules\Key("password", self::nullValidator(), false);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateIdentityManager(mixed $data): void
    {
        (new Rules\Key("scheme", self::schemeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("oauth2TokenPath", self::pathValidator(), true),
            new Rules\Key("userPath", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("implementationName", self::identityManagerImplementationNameValidator(), true),
            new Rules\Key("implementationVersion", self::versionValidator(), true)
        ];

        if ($data["scheme"] === Scheme::Https->value) {
            $keys[] = new Rules\Key("disableCertificateVerification", self::booleanValidator(), true);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateProperty(mixed $data): void
    {
        (new Rules\Key("ngsiLdType", self::ngsiLdAttributeTypeValidator(), true))->assert($data);
        (new Rules\Key("temporal", self::booleanValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("url", new Rules\Nullable(self::urlValidator()), false),
            new Rules\Key("ngsiLdType", self::ngsiLdAttributeTypeValidator(), true),
            new Rules\Key("standard", self::booleanValidator(), true),
            new Rules\Key("mandatory", self::booleanValidator(), true),
            new Rules\Key("temporal", self::booleanValidator(), true),
            new Rules\Key("multiValued", self::booleanValidator(), true),
            new Rules\Key("hasType", self::urnValidator(), true),
            new Rules\Key("hasProperty", new Rules\Nullable(self::urnValidator()), false),
            new Rules\Key("hasWorkspace", self::urnValidator(), true)
        ];

        if ($data["ngsiLdType"] === NgsiLdAttributeType::Property->value) {
            $keys[] = new Rules\Key("propertyNgsiLdValueType", self::ngsiLdPropertyValueTypeValidator(), true);
            $keys[] = new Rules\Key("relationshipType", self::nullValidator(), false);
            $keys[] = new Rules\Key("geoPropertyNgsiLdType", self::nullValidator(), false);
            $keys[] = new Rules\Key("geoPropertyGeographic", self::nullValidator(), false);
        } elseif ($data["ngsiLdType"] === NgsiLdAttributeType::Relationship->value) {
            $keys[] = new Rules\Key("propertyNgsiLdValueType", self::nullValidator(), false);
            $keys[] = new Rules\Key("relationshipType", self::urnValidator(), true);
            $keys[] = new Rules\Key("geoPropertyNgsiLdType", self::nullValidator(), false);
            $keys[] = new Rules\Key("geoPropertyGeographic", self::nullValidator(), false);
        } elseif ($data["ngsiLdType"] === NgsiLdAttributeType::GeoProperty->value) {
            $keys[] = new Rules\Key("propertyNgsiLdValueType", self::nullValidator(), false);
            $keys[] = new Rules\Key("relationshipType", self::nullValidator(), false);
            $keys[] = new Rules\Key("geoPropertyNgsiLdType", self::ngsiLdGeoPropertyTypeValidator(), true);
            $keys[] = new Rules\Key("geoPropertyGeographic", self::booleanValidator(), true);
        }

        if ($data["temporal"]) {
            $keys[] = new Rules\Key("temporalServices", self::urnArrayValidator(), true);
        } else {
            $keys[] = new Rules\Key("temporalServices", self::nullValidator(), false);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateService(mixed $data): void
    {
        (new Rules\Key("authorizationRequired", self::booleanValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("authorizationRequired", self::booleanValidator(), true),
            new Rules\Key("hasContextBroker", self::urnValidator(), true)
        ];

        if ($data["authorizationRequired"]) {
            (new Rules\Key("authorizationMode", self::authorizationModeValidator(), true))->assert($data);
            $keys[] = new Rules\Key("authorizationMode", self::authorizationModeValidator(), true);

            if ($data["authorizationMode"] === AuthorizationMode::OAuth2->value) {
                $keys[] = new Rules\Key("clientId", self::nullValidator(), false);
                $keys[] = new Rules\Key("hasIdentityManager", self::urnValidator(), true);
                $keys[] = new Rules\Key("hasIdentityManagerGrant", self::urnValidator(), true);
                $keys[] = new Rules\Key("hasVCVerifier", self::nullValidator(), false);
                $keys[] = new Rules\Key("hasTrustedIssuersList", self::nullValidator(), false);
            } else if ($data["authorizationMode"] === AuthorizationMode::SIOP2->value) {
                $keys[] = new Rules\Key("clientId", self::clientIdValidator(), true);
                $keys[] = new Rules\Key("hasIdentityManager", self::nullValidator(), false);
                $keys[] = new Rules\Key("hasIdentityManagerGrant", self::nullValidator(), false);
                $keys[] = new Rules\Key("hasVCVerifier", self::urnValidator(), true);
                $keys[] = new Rules\Key("hasTrustedIssuersList", self::urnValidator(), true);
            }
        } else {
            $keys[] = new Rules\Key("authorizationMode", self::nullValidator(), false);
            $keys[] = new Rules\Key("clientId", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasIdentityManager", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasIdentityManagerGrant", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasVCVerifier", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasTrustedIssuersList", self::nullValidator(), false);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateTemporalServiceType(mixed $data): void
    {
        $validator = new Rules\Key("temporalServiceType", self::temporalServiceTypeValidator(), true);
        $validator->assert($data);
    }

    public static function validateTemporalServiceNgsiLd(mixed $data): void
    {
        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("temporalServiceType", self::temporalServiceTypeValidator(), true)
        ];

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateTemporalServiceMintaka(mixed $data): void
    {
        (new Rules\Key("scheme", self::schemeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("temporalServiceType", self::temporalServiceTypeValidator(), true),
            new Rules\Key("version", self::versionValidator(), true),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("authorizationRequired", self::booleanValidator(), true)
        ];

        if ($data["authorizationRequired"]) {
            $keys[] = new Rules\Key("hasIdentityManager", self::urnValidator(), true);
            $keys[] = new Rules\Key("hasIdentityManagerGrant", self::urnValidator(), true);
        } else {
            $keys[] = new Rules\Key("hasIdentityManager", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasIdentityManagerGrant", self::nullValidator(), false);
        }

        if ($data["scheme"] === Scheme::Https->value) {
            $keys[] = new Rules\Key("disableCertificateVerification", self::booleanValidator(), true);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateType(mixed $data): void
    {
        (new Rules\Key("standardDataModelBased", self::booleanValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::propertyNameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("url", new Rules\Nullable(self::urlValidator()), false),
            new Rules\Key("standardDataModelBased", self::booleanValidator(), true),
            new Rules\Key("positionInChart", new Rules\Nullable(self::positionInChartValidator()), false),
            new Rules\Key("dataModelGroup", new Rules\Nullable(self::dataModelGroupValidator()), false),
            new Rules\Key("scopeName", new Rules\Nullable(new Rules\StringType()), false),
            new Rules\Key("hasWorkspace", self::urnValidator(), true)
        ];

        if ($data["standardDataModelBased"]) {
            $keys[] = new Rules\Key("standardDataModelType", self::standardDataModelTypeValidator(), true);
            $keys[] = new Rules\Key("standardDataModelDefinitionUrl", self::urlValidator(), true);
        } else {
            $keys[] = new Rules\Key("standardDataModelType", self::nullValidator(), false);
            $keys[] = new Rules\Key("standardDataModelDefinitionUrl", self::nullValidator(), false);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateWorkspace(mixed $data): void
    {
        (new Rules\Key("enableOffers", self::booleanValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("dataModelName", self::dataModelNameValidator(), true),
            new Rules\Key("dataModelVersion", self::dataModelVersionValidator(), true),
            new Rules\Key("contextBrokerTenant", new Rules\Nullable(self::contextBrokerTenantValidator()), false),
            new Rules\Key("defaultDataModelUrl", new Rules\Nullable(self::urlValidator()), false),
            new Rules\Key("temporalServices", self::urnArrayValidator(), true),
            new Rules\Key("hasService", self::urnValidator(), true),
            new Rules\Key("enableOffers", self::booleanValidator(), true)
        ];

        if ($data["enableOffers"]) {
            $keys[] = new Rules\Key("hasAuthorizationRegistry", self::urnValidator(), true);
            $keys[] = new Rules\Key("hasAuthorizationRegistryGrant", self::urnValidator(), true);
        } else {
            $keys[] = new Rules\Key("hasAuthorizationRegistry", self::nullValidator(), false);
            $keys[] = new Rules\Key("hasAuthorizationRegistryGrant", self::nullValidator(), false);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateEntityCreation(mixed $data): void
    {
        $validator = new Rules\AllOf(
            new Rules\Key("id", new Rules\Nullable(self::urnValidator()), false),
            new Rules\Key("type", self::typeNameValidator(), true)
        );
        $validator->assert($data);
    }

    public static function validateEntityUpdate(mixed $data): void
    {
        $validator = new Rules\AllOf(
            new Rules\Key("id", new Rules\Nullable(self::prohibitedValidator()), false),
            new Rules\Key("type", new Rules\Nullable(self::prohibitedValidator()), false)
        );
        $validator->assert($data);
    }

    public static function validateSubscriptionCreation(mixed $data): void
    {
        $validator = new Rules\AllOf(
            new Rules\Key("id", new Rules\Nullable(self::urnValidator()), false),
            new Rules\Key("type", self::subscriptionTypeValidator(), true)
        );
        $validator->assert($data);
    }

    public static function validatePositionInChart(mixed $data): void
    {
        $validator = self::positionInChartValidator();
        $validator->assert($data);
    }

    public static function validateVCVerifier(mixed $data): void
    {
        (new Rules\Key("scheme", self::schemeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("implementationName", self::vcVerifierImplementationNameValidator(), true),
            new Rules\Key("implementationVersion", self::versionValidator(), true),
            new Rules\Key("did", self::didValidator(), true)
        ];

        if ($data["scheme"] === Scheme::Https->value) {
            $keys[] = new Rules\Key("disableCertificateVerification", self::booleanValidator(), true);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateTrustedIssuersList(mixed $data): void
    {
        (new Rules\Key("scheme", self::schemeValidator(), true))->assert($data);

        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("implementationName", self::trustedIssuersListImplementationNameValidator(), true),
            new Rules\Key("implementationVersion", self::versionValidator(), true)
        ];

        if ($data["scheme"] === Scheme::Https->value) {
            $keys[] = new Rules\Key("disableCertificateVerification", self::booleanValidator(), true);
        }

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateWoTAction(mixed $data): void
    {
    }

    public static function validateWoTProperty(mixed $data): void
    {
    }

    public static function validateRouting(mixed $data): void
    {
    }

    public static function validateRoutingOperation(mixed $data): void
    {
    }

    public static function validateRoutingOperationControl(mixed $data): void
    {
    }

    public static function validateWoTThingDescription(mixed $data): void
    {
    }

    public static function identifierValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank()
        );
    }

    public static function authorizationRegistryImplementationNameValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\In(AuthorizationRegistryImplementationName::values(), true)
        );
    }

    public static function certificatesValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType(),
            new Rules\NotBlank(),
            new Rules\Each(
                new Rules\AllOf(
                    new Rules\StringType(),
                    new Rules\NotBlank()
                )
            )
        );
    }

    public static function privateKeyValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\StringType(),
            new Rules\NotBlank()
        );
    }

    public static function delegationEvidenceValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType()
        );
    }

    public static function delegationRequestValidator(): Rules\AllOf
    {
        return new Rules\AllOf(
            new Rules\ArrayType()
        );
    }

    public static function validateAuthorizationRegistry(mixed $data): void
    {
        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("identifier", self::identifierValidator(), true),
            new Rules\Key("certificates", self::certificatesValidator(), true),
            new Rules\Key("scheme", self::schemeValidator(), true),
            new Rules\Key("host", self::hostValidator(), true),
            new Rules\Key("port", self::portValidator(), true),
            new Rules\Key("path", new Rules\Nullable(self::pathValidator()), false),
            new Rules\Key("oauth2TokenPath", self::pathValidator(), true),
            new Rules\Key("delegationPath", self::pathValidator(), true),
            new Rules\Key("policyPath", self::pathValidator(), true),
            new Rules\Key("implementationName", self::authorizationRegistryImplementationNameValidator(), true),
            new Rules\Key("implementationVersion", self::versionValidator(), true),
        ];

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateAuthorizationRegistryGrant(mixed $data): void
    {
        $keys = [
            new Rules\Key("name", self::nameValidator(), true),
            new Rules\Key("description", new Rules\Nullable(self::descriptionValidator()), false),
            new Rules\Key("identifier", self::identifierValidator(), true),
            new Rules\Key("certificates", self::certificatesValidator(), true),
            new Rules\Key("privateKey", self::privateKeyValidator(), true)
        ];

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateDelegationEvidence(mixed $data): void
    {
        $keys = [
            new Rules\Key("delegationEvidence", self::delegationEvidenceValidator(), true)
        ];

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }

    public static function validateDelegationRequest(mixed $data): void
    {
        $keys = [
            new Rules\Key("delegationRequest", self::delegationRequestValidator(), true)
        ];

        $validator = new Rules\KeySet(...$keys);
        $validator->assert($data);
    }
}
