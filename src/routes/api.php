<?php

use Core\API;
use Core\HttpRequestMethods;

API::router()->before(implode("|", [
    HttpRequestMethods::GET,
    HttpRequestMethods::POST,
    HttpRequestMethods::PUT,
    HttpRequestMethods::PATCH,
    HttpRequestMethods::DELETE
]), "/api/.*", "Middlewares\\AuthorizationMiddleware@authorize");

API::router()->mount("/api", function () {
    API::router()->mount("/temporal-services", function () {
        API::router()->get("/", "Controllers\\TemporalServiceController@index");
        API::router()->post("/", "Controllers\\TemporalServiceController@store");
        API::router()->get("/([^/]+)", "Controllers\\TemporalServiceController@show");
        API::router()->put("/([^/]+)", "Controllers\\TemporalServiceController@update");
        API::router()->delete("/([^/]+)", "Controllers\\TemporalServiceController@destroy");
    });

    API::router()->mount("/identity-manager-grants", function () {
        API::router()->get("/", "Controllers\\IdentityManagerGrantController@index");
        API::router()->post("/", "Controllers\\IdentityManagerGrantController@store");
        API::router()->get("/([^/]+)", "Controllers\\IdentityManagerGrantController@show");
        API::router()->put("/([^/]+)", "Controllers\\IdentityManagerGrantController@update");
        API::router()->delete("/([^/]+)", "Controllers\\IdentityManagerGrantController@destroy");
    });

    API::router()->mount("/identity-managers", function () {
        API::router()->get("/", "Controllers\\IdentityManagerController@index");
        API::router()->post("/", "Controllers\\IdentityManagerController@store");
        API::router()->get("/([^/]+)", "Controllers\\IdentityManagerController@show");
        API::router()->put("/([^/]+)", "Controllers\\IdentityManagerController@update");
        API::router()->delete("/([^/]+)", "Controllers\\IdentityManagerController@destroy");
    });

    API::router()->mount("/context-brokers", function () {
        API::router()->get("/", "Controllers\\ContextBrokerController@index");
        API::router()->post("/", "Controllers\\ContextBrokerController@store");
        API::router()->get("/([^/]+)", "Controllers\\ContextBrokerController@show");
        API::router()->put("/([^/]+)", "Controllers\\ContextBrokerController@update");
        API::router()->delete("/([^/]+)", "Controllers\\ContextBrokerController@destroy");
    });

    API::router()->mount("/services", function () {
        API::router()->get("/", "Controllers\\ServiceController@index");
        API::router()->post("/", "Controllers\\ServiceController@store");
        API::router()->get("/([^/]+)", "Controllers\\ServiceController@show");
        API::router()->put("/([^/]+)", "Controllers\\ServiceController@update");
        API::router()->delete("/([^/]+)", "Controllers\\ServiceController@destroy");
    });

    API::router()->mount("/workspaces", function () {
        API::router()->get("/", "Controllers\\WorkspaceController@index");
        API::router()->post("/", "Controllers\\WorkspaceController@store");
        API::router()->get("/([^/]+)", "Controllers\\WorkspaceController@show");
        API::router()->put("/([^/]+)", "Controllers\\WorkspaceController@update");
        API::router()->delete("/([^/]+)", "Controllers\\WorkspaceController@destroy");
    });

    API::router()->mount("/vc-verifiers", function () {
        API::router()->get("/", "Modules\\DSC\\Controllers\\VCVerifierController@index");
        API::router()->post("/", "Modules\\DSC\\Controllers\\VCVerifierController@store");
        API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\VCVerifierController@show");
        API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\VCVerifierController@update");
        API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\VCVerifierController@destroy");
    });

    API::router()->mount("/trusted-issuers-lists", function () {
        API::router()->get("/", "Modules\\DSC\\Controllers\\TrustedIssuersListController@index");
        API::router()->post("/", "Modules\\DSC\\Controllers\\TrustedIssuersListController@store");
        API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\TrustedIssuersListController@show");
        API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\TrustedIssuersListController@update");
        API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\TrustedIssuersListController@destroy");
    });

    if (I4TRUST_MODULE_ENABLED) {
        API::router()->mount("/authorization-registries", function () {
            API::router()->get("/", "Modules\\DSC\\Controllers\\AuthorizationRegistryController@index");
            API::router()->post("/", "Modules\\DSC\\Controllers\\AuthorizationRegistryController@store");
            API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryController@show");
            API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryController@update");
            API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryController@destroy");
        });

        API::router()->mount("/authorization-registry-grants", function () {
            API::router()->get("/", "Modules\\DSC\\Controllers\\AuthorizationRegistryGrantController@index");
            API::router()->post("/", "Modules\\DSC\\Controllers\\AuthorizationRegistryGrantController@store");
            API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryGrantController@show");
            API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryGrantController@update");
            API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\AuthorizationRegistryGrantController@destroy");
        });

        API::router()->mount("/data-actions", function () {
            API::router()->get("/", "Modules\\DataServices\\Controllers\\DataActionController@index");
            API::router()->post("/", "Modules\\DataServices\\Controllers\\DataActionController@store");
            API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataActionController@show");
            API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataActionController@update");
            API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataActionController@destroy");
        });
    }

    API::router()->mount("/workspace/([^/]+)", function () {
        API::router()->mount("/data-model", function () {
            API::router()->post("/generate", "Controllers\\DataModelController@generate");
            API::router()->post("/auto-discover", "Controllers\\DataModelController@autoDiscover");
        });

        API::router()->mount("/types", function () {
            API::router()->get("/", "Controllers\\TypeController@index");
            API::router()->post("/", "Controllers\\TypeController@store");
            API::router()->get("/([^/]+)", "Controllers\\TypeController@show");
            API::router()->put("/([^/]+)", "Controllers\\TypeController@update");
            API::router()->delete("/([^/]+)", "Controllers\\TypeController@destroy");
            API::router()->put("/([^/]+)/position-in-chart", "Controllers\\TypeController@updatePositionInChart");
        });

        API::router()->mount("/properties", function () {
            API::router()->get("/", "Controllers\\PropertyController@index");
            API::router()->post("/", "Controllers\\PropertyController@store");
            API::router()->get("/([^/]+)", "Controllers\\PropertyController@show");
            API::router()->put("/([^/]+)", "Controllers\\PropertyController@update");
            API::router()->delete("/([^/]+)", "Controllers\\PropertyController@destroy");
        });

        API::router()->mount("/entities", function () {
            API::router()->get("/", "Controllers\\EntityController@index");
            API::router()->post("/", "Controllers\\EntityController@store");
            API::router()->get("/([^/]+)", "Controllers\\EntityController@show");
            API::router()->put("/([^/]+)", "Controllers\\EntityController@update");
            API::router()->delete("/([^/]+)", "Controllers\\EntityController@destroy");
        });

        API::router()->get("/temporal-entities/([^/]+)/properties/([^/]+)/temporal-services/([^/]+)", "Controllers\\TemporalEntityPropertyController@show");

        API::router()->mount("/subscriptions", function () {
            API::router()->get("/", "Controllers\\SubscriptionController@index");
            API::router()->post("/", "Controllers\\SubscriptionController@store");
            API::router()->get("/([^/]+)", "Controllers\\SubscriptionController@show");
            API::router()->delete("/([^/]+)", "Controllers\\SubscriptionController@destroy");
        });

        API::router()->mount("/wot-actions", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\WoTActionController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\WoTActionController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\WoTActionController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\WoTActionController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\WoTActionController@destroy");
            API::router()->post("/([^/]+)/execute", "Modules\\WoT\\Controllers\\WoTActionController@execute");
        });

        API::router()->mount("/wot-properties", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\WoTPropertyController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\WoTPropertyController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\WoTPropertyController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\WoTPropertyController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\WoTPropertyController@destroy");
        });

        API::router()->mount("/wot-events", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\WoTEventController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\WoTEventController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\WoTEventController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\WoTEventController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\WoTEventController@destroy");
        });

        API::router()->mount("/routings", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\RoutingController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\RoutingController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingController@destroy");
        });

        API::router()->mount("/routing-operations", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\RoutingOperationController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\RoutingOperationController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationController@destroy");
        });

        API::router()->mount("/routing-operation-controls", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\RoutingOperationControlController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\RoutingOperationControlController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationControlController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationControlController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\RoutingOperationControlController@destroy");
        });

        API::router()->mount("/wot-thing-descriptions", function () {
            API::router()->get("/", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@index");
            API::router()->post("/", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@store");
            API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@show");
            API::router()->put("/([^/]+)", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@update");
            API::router()->delete("/([^/]+)", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@destroy");
        });


        if (I4TRUST_MODULE_ENABLED) {
            API::router()->mount("/contracts", function () {
                API::router()->get("/", "Modules\\DSC\\Controllers\\ContractController@index");
                API::router()->post("/", "Modules\\DSC\\Controllers\\ContractController@store");
                API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\ContractController@show");
                API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\ContractController@update");
                API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\ContractController@destroy");
                API::router()->post("/([^/]+)/synchronize", "Modules\\DSC\\Controllers\\ContractController@synchronize");
            });

            API::router()->mount("/contract-details", function () {
                API::router()->get("/", "Modules\\DSC\\Controllers\\ContractDetailController@index");
                API::router()->post("/", "Modules\\DSC\\Controllers\\ContractDetailController@store");
                API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\ContractDetailController@show");
                API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\ContractDetailController@update");
                API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\ContractDetailController@destroy");
            });

            API::router()->mount("/data-services", function () {
                API::router()->get("/", "Modules\\DataServices\\Controllers\\DataServiceController@index");
                API::router()->post("/", "Modules\\DataServices\\Controllers\\DataServiceController@store");
                API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceController@show");
                API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceController@update");
                API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceController@destroy");
            });

            API::router()->mount("/data-service-actions", function () {
                API::router()->get("/", "Modules\\DataServices\\Controllers\\DataServiceActionController@index");
                API::router()->post("/", "Modules\\DataServices\\Controllers\\DataServiceActionController@store");
                API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceActionController@show");
                API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceActionController@update");
                API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceActionController@destroy");
            });

            API::router()->mount("/data-service-properties", function () {
                API::router()->get("/", "Modules\\DataServices\\Controllers\\DataServicePropertyController@index");
                API::router()->post("/", "Modules\\DataServices\\Controllers\\DataServicePropertyController@store");
                API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServicePropertyController@show");
                API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServicePropertyController@update");
                API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServicePropertyController@destroy");
            });

            API::router()->mount("/data-service-offers", function () {
                API::router()->get("/", "Modules\\DataServices\\Controllers\\DataServiceOfferController@index");
                API::router()->post("/", "Modules\\DataServices\\Controllers\\DataServiceOfferController@store");
                API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceOfferController@show");
                API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceOfferController@update");
                API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceOfferController@destroy");
            });

            API::router()->mount("/data-service-accesses", function () {
                API::router()->get("/", "Modules\\DataServices\\Controllers\\DataServiceAccessController@index");
                API::router()->post("/", "Modules\\DataServices\\Controllers\\DataServiceAccessController@store");
                API::router()->get("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceAccessController@show");
                API::router()->put("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceAccessController@update");
                API::router()->delete("/([^/]+)", "Modules\\DataServices\\Controllers\\DataServiceAccessController@destroy");
            });

            API::router()->mount("/authorization-registry-bridge", function () {
                API::router()->post("/create-policy", "Modules\\DSC\\Controllers\\AuthorizationRegistryBridgeController@createPolicy");
            });

            API::router()->mount("/roles", function () {
                API::router()->get("/", "Modules\\DSC\\Controllers\\RoleController@index");
                API::router()->post("/", "Modules\\DSC\\Controllers\\RoleController@store");
                API::router()->get("/([^/]+)", "Modules\\DSC\\Controllers\\RoleController@show");
                API::router()->put("/([^/]+)", "Modules\\DSC\\Controllers\\RoleController@update");
                API::router()->delete("/([^/]+)", "Modules\\DSC\\Controllers\\RoleController@destroy");
                API::router()->post("/([^/]+)/synchronize", "Modules\\DSC\\Controllers\\RoleController@synchronize");
            });
        }
    });

    API::router()->mount("/proxies", function () {
        if (I4TRUST_MODULE_ENABLED) {
            API::router()->mount("/authorization-registry", function () {
                API::router()->post("/create-policy", "Modules\\DSC\\Controllers\\AuthorizationRegistryProxyController@createPolicy");
                API::router()->post("/request-delegation", "Modules\\DSC\\Controllers\\AuthorizationRegistryProxyController@requestDelegation");
            });
        }
        API::router()->mount("/google-sheets", function () {
            API::router()->get("/read-sheet", "Proxies\\GoogleSheetsProxy@readSheet");
            API::router()->post("/write-sheet", "Proxies\\GoogleSheetsProxy@writeSheet");
        });
    });

    API::router()->mount("/authorization", function () {
        API::router()->get("/permissions", "Controllers\\AuthorizationController@permissions");
    });

    API::router()->mount("/siop2", function () {
        API::router()->get("/callback", "Modules\\DSC\\Controllers\\SIOP2Controller@callback");
        API::router()->get("/poll", "Modules\\DSC\\Controllers\\SIOP2Controller@poll");
    });

    if (DEVELOPMENT) {
        API::router()->mount("/seeders", function () {
            API::router()->mount("/test-seeder", function () {
                API::router()->post("/seed", "Seeders\\TestSeeder@seed");
            });
        });
    }
});

API::router()->mount("/wot-thing-descriptions", function () {
    API::router()->get("/([^/]+)", "Modules\\WoT\\Controllers\\WoTThingDescriptionController@build");
});
