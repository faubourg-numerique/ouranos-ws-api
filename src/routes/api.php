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
        API::router()->get("/", "Controllers\\VCVerifierController@index");
        API::router()->post("/", "Controllers\\VCVerifierController@store");
        API::router()->get("/([^/]+)", "Controllers\\VCVerifierController@show");
        API::router()->put("/([^/]+)", "Controllers\\VCVerifierController@update");
        API::router()->delete("/([^/]+)", "Controllers\\VCVerifierController@destroy");
    });

    API::router()->mount("/trusted-issuers-lists", function () {
        API::router()->get("/", "Controllers\\TrustedIssuersListController@index");
        API::router()->post("/", "Controllers\\TrustedIssuersListController@store");
        API::router()->get("/([^/]+)", "Controllers\\TrustedIssuersListController@show");
        API::router()->put("/([^/]+)", "Controllers\\TrustedIssuersListController@update");
        API::router()->delete("/([^/]+)", "Controllers\\TrustedIssuersListController@destroy");
    });

    if (I4TRUST_MODULE_ENABLED) {
        API::router()->mount("/authorization-registries", function () {
            API::router()->get("/", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryController@index");
            API::router()->post("/", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryController@store");
            API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryController@show");
            API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryController@update");
            API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryController@destroy");
        });

        API::router()->mount("/authorization-registry-grants", function () {
            API::router()->get("/", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryGrantController@index");
            API::router()->post("/", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryGrantController@store");
            API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryGrantController@show");
            API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryGrantController@update");
            API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryGrantController@destroy");
        });

        API::router()->mount("/data-actions", function () {
            API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataActionController@index");
            API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataActionController@store");
            API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataActionController@show");
            API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataActionController@update");
            API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataActionController@destroy");
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
            API::router()->get("/", "Controllers\\WoTActionController@index");
            API::router()->post("/", "Controllers\\WoTActionController@store");
            API::router()->get("/([^/]+)", "Controllers\\WoTActionController@show");
            API::router()->put("/([^/]+)", "Controllers\\WoTActionController@update");
            API::router()->delete("/([^/]+)", "Controllers\\WoTActionController@destroy");
            API::router()->post("/([^/]+)/execute", "Controllers\\WoTActionController@execute");
        });

        API::router()->mount("/wot-properties", function () {
            API::router()->get("/", "Controllers\\WoTPropertyController@index");
            API::router()->post("/", "Controllers\\WoTPropertyController@store");
            API::router()->get("/([^/]+)", "Controllers\\WoTPropertyController@show");
            API::router()->put("/([^/]+)", "Controllers\\WoTPropertyController@update");
            API::router()->delete("/([^/]+)", "Controllers\\WoTPropertyController@destroy");
        });

        API::router()->mount("/wot-events", function () {
            API::router()->get("/", "Controllers\\WoTEventController@index");
            API::router()->post("/", "Controllers\\WoTEventController@store");
            API::router()->get("/([^/]+)", "Controllers\\WoTEventController@show");
            API::router()->put("/([^/]+)", "Controllers\\WoTEventController@update");
            API::router()->delete("/([^/]+)", "Controllers\\WoTEventController@destroy");
        });

        API::router()->mount("/routings", function () {
            API::router()->get("/", "Controllers\\RoutingController@index");
            API::router()->post("/", "Controllers\\RoutingController@store");
            API::router()->get("/([^/]+)", "Controllers\\RoutingController@show");
            API::router()->put("/([^/]+)", "Controllers\\RoutingController@update");
            API::router()->delete("/([^/]+)", "Controllers\\RoutingController@destroy");
        });

        API::router()->mount("/routing-operations", function () {
            API::router()->get("/", "Controllers\\RoutingOperationController@index");
            API::router()->post("/", "Controllers\\RoutingOperationController@store");
            API::router()->get("/([^/]+)", "Controllers\\RoutingOperationController@show");
            API::router()->put("/([^/]+)", "Controllers\\RoutingOperationController@update");
            API::router()->delete("/([^/]+)", "Controllers\\RoutingOperationController@destroy");
        });

        API::router()->mount("/routing-operation-controls", function () {
            API::router()->get("/", "Controllers\\RoutingOperationControlController@index");
            API::router()->post("/", "Controllers\\RoutingOperationControlController@store");
            API::router()->get("/([^/]+)", "Controllers\\RoutingOperationControlController@show");
            API::router()->put("/([^/]+)", "Controllers\\RoutingOperationControlController@update");
            API::router()->delete("/([^/]+)", "Controllers\\RoutingOperationControlController@destroy");
        });

        API::router()->mount("/wot-thing-descriptions", function () {
            API::router()->get("/", "Controllers\\WoTThingDescriptionController@index");
            API::router()->post("/", "Controllers\\WoTThingDescriptionController@store");
            API::router()->get("/([^/]+)", "Controllers\\WoTThingDescriptionController@show");
            API::router()->put("/([^/]+)", "Controllers\\WoTThingDescriptionController@update");
            API::router()->delete("/([^/]+)", "Controllers\\WoTThingDescriptionController@destroy");
        });


        if (I4TRUST_MODULE_ENABLED) {
            API::router()->mount("/contracts", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\ContractController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\ContractController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractController@destroy");
                API::router()->post("/([^/]+)/synchronize", "Modules\\I4Trust\\Controllers\\ContractController@synchronize");
            });

            API::router()->mount("/contract-details", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\ContractDetailController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\ContractDetailController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractDetailController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractDetailController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\ContractDetailController@destroy");
            });

            API::router()->mount("/data-services", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataServiceController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataServiceController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceController@destroy");
            });

            API::router()->mount("/data-service-actions", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataServiceActionController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataServiceActionController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceActionController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceActionController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceActionController@destroy");
            });

            API::router()->mount("/data-service-properties", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataServicePropertyController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataServicePropertyController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServicePropertyController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServicePropertyController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServicePropertyController@destroy");
            });

            API::router()->mount("/data-service-offers", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataServiceOfferController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataServiceOfferController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceOfferController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceOfferController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceOfferController@destroy");
            });

            API::router()->mount("/data-service-accesses", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\DataServiceAccessController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\DataServiceAccessController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceAccessController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceAccessController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\DataServiceAccessController@destroy");
            });

            API::router()->mount("/authorization-registry-bridge", function () {
                API::router()->post("/create-policy", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryBridgeController@createPolicy");
            });

            API::router()->mount("/roles", function () {
                API::router()->get("/", "Modules\\I4Trust\\Controllers\\RoleController@index");
                API::router()->post("/", "Modules\\I4Trust\\Controllers\\RoleController@store");
                API::router()->get("/([^/]+)", "Modules\\I4Trust\\Controllers\\RoleController@show");
                API::router()->put("/([^/]+)", "Modules\\I4Trust\\Controllers\\RoleController@update");
                API::router()->delete("/([^/]+)", "Modules\\I4Trust\\Controllers\\RoleController@destroy");
                API::router()->post("/([^/]+)/synchronize", "Modules\\I4Trust\\Controllers\\RoleController@synchronize");
            });
        }
    });

    API::router()->mount("/proxies", function () {
        if (I4TRUST_MODULE_ENABLED) {
            API::router()->mount("/authorization-registry", function () {
                API::router()->post("/create-policy", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryProxyController@createPolicy");
                API::router()->post("/request-delegation", "Modules\\I4Trust\\Controllers\\AuthorizationRegistryProxyController@requestDelegation");
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
        API::router()->get("/callback", "Controllers\\SIOP2Controller@callback");
        API::router()->get("/poll", "Controllers\\SIOP2Controller@poll");
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
    API::router()->get("/([^/]+)", "Controllers\\WoTThingDescriptionController@build");
});