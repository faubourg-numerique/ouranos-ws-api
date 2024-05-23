<?php

namespace API\StaticClasses;

use Ramsey\Uuid\Uuid;

class Utils
{
    public static function buildUrl(string $scheme, string $host, int $port, ?string $path = null): string
    {
        return "{$scheme}://{$host}:{$port}{$path}";
    }

    public static function buildLinkHeader(string $contextUrl): string
    {
        return sprintf('<%s>; rel="http://www.w3.org/ns/json-ld#context"; type="application/ld+json"', $contextUrl);
    }

    public static function generateUniqueNgsiLdUrn(?string $type = null): string
    {
        $uuid = Uuid::uuid4();
        return "urn:ngsi-ld:" . (!is_null($type) ? "{$type}:" : null) . "{$uuid}";
    }

    public static function replacePathSeparators(string $path, string $separator): string
    {
        return str_replace(["\\", "/"], $separator, $path);
    }

    public static function convertToBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function buildSystemPath(string ...$elements): string
    {
        return implode(DIRECTORY_SEPARATOR, $elements);
    }

    public static function formatCertificate(string $certificate): string
    {
        return "-----BEGIN CERTIFICATE-----\n" . trim(chunk_split($certificate, 64, "\n")) . "\n-----END CERTIFICATE-----\n";
    }

    public static function formatPrivateKey(string $privateKey): string
    {
        return "-----BEGIN PRIVATE KEY-----\n" . trim(chunk_split($privateKey, 64, "\n")) . "\n-----END PRIVATE KEY-----\n";
    }

    public static function extractTypeFromNgsiLdUrn(string $id): string
    {
        return explode(":", $id)[2];
    }
}
