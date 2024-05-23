<?php

namespace API\Proxies;

use API\Enums\MimeType;
use Core\API;
use Core\HttpResponseStatusCodes;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetsProxy
{
    public function readSheet(): void
    {
        $spreadsheetId = API::request()->getUrlQueryParameter("spreadsheetId");
        $sheetName = API::request()->getUrlQueryParameter("sheetName");

        $googleClient = new Google_Client();
        $googleClient->setAuthConfig($_ENV["GOOGLE_SERVICE_ACCOUNT_KEY_PATH"]);
        $googleClient->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $googleServiceSheets = new Google_Service_Sheets($googleClient);

        $response = $googleServiceSheets->spreadsheets_values->get($spreadsheetId, $sheetName);
        $data = $response->getValues();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($data, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function writeSheet(): void
    {
        $spreadsheetId = API::request()->getUrlQueryParameter("spreadsheetId");
        $sheetName = API::request()->getUrlQueryParameter("sheetName");

        $data = API::request()->getDecodedJsonBody();

        $googleClient = new Google_Client();
        $googleClient->setAuthConfig($_ENV["GOOGLE_SERVICE_ACCOUNT_KEY_PATH"]);
        $googleClient->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $googleServiceSheets = new Google_Service_Sheets($googleClient);

        $googleServiceSheetsValueRange = new Google_Service_Sheets_ValueRange(["values" => $data]);
        $parameters = ["valueInputOption" => "RAW"];
        $result = $googleServiceSheets->spreadsheets_values->append($spreadsheetId, $sheetName, $googleServiceSheetsValueRange, $parameters);
        $result->getUpdates()->getUpdatedRows();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
