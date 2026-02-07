<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MouserApiService
{
    public function __construct()
    {
        //
    }

    public function searchPartNumber($searchTerm, string $apiKey)
    {
        // Construct the URL with the API key as a query parameter
        $url = 'https://api.mouser.com/api/v1/search/partnumber?apiKey='.$apiKey;

        // Send the POST request with the JSON body
        $response = Http::post($url, [
            'SearchByPartRequest' => [
                'mouserPartNumber' => $searchTerm,
                'partSearchOptions' => null, // The following values are valid: None | Exact - can use string representations or integer IDs: 1[None] | 2[Exact]
            ],
        ]);
        $response->throw(); // Throw an exception if the response status code is not 2xx

        return $response->json();  // Return the response as a JSON array
    }
}

// Example JSON:

// {
//     "Errors": [],
//     "SearchResults": {
//         "NumberOfResult": 1,
//         "Parts": [
//             {
//                 "Availability": "4739 Auf Lager",
//                 "DataSheetUrl": "",
//                 "Description": "Operationsverst\u00e4rker Quad with 2kV ESD Pr otection A 595-LM324KAPW",
//                 "FactoryStock": "0",
//                 "ImagePath": "https:\/\/www.mouser.com\/images\/texasinstruments\/images\/ITP_TI_TSSOP-14_PW_t.jpg",
//                 "Category": "Operationsverst\u00e4rker",
//                 "LeadTime": "42 Tage",
//                 "LifecycleStatus": null,
//                 "Manufacturer": "Texas Instruments",
//                 "ManufacturerPartNumber": "LM324KAPWR",
//                 "Min": "1",
//                 "Mult": "1",
//                 "MouserPartNumber": "595-LM324KAPWR",
//                 "ProductAttributes": [
//                     {
//                         "AttributeName": "Verpackung",
//                         "AttributeValue": "Reel"
//                     },
//                     {
//                         "AttributeName": "Verpackung",
//                         "AttributeValue": "Cut Tape"
//                     },
//                     {
//                         "AttributeName": "Verpackung",
//                         "AttributeValue": "MouseReel",
//                         "AttributeCost": "F\u00fcr die MouseReel\u2122 wird Ihrem Warenkorb automatisch eine Geb\u00fchr von 5,00 \u20ac hinzugef\u00fcgt. MouseReel\u2122 Bestellungen sind weder stornierbar, noch k\u00f6nnen sie zur\u00fcckgegeben werden."
//                     },
//                     {
//                         "AttributeName": "Standardpackungsmenge",
//                         "AttributeValue": "2000"
//                     }
//                 ],
//                 "PriceBreaks": [
//                     {
//                         "Quantity": 1,
//                         "Price": "0,344 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 10,
//                         "Price": "0,237 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 25,
//                         "Price": "0,211 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 100,
//                         "Price": "0,181 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 250,
//                         "Price": "0,168 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 500,
//                         "Price": "0,159 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 1000,
//                         "Price": "0,152 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 2000,
//                         "Price": "0,152 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 4000,
//                         "Price": "0,144 \u20ac",
//                         "Currency": "EUR"
//                     },
//                     {
//                         "Quantity": 8000,
//                         "Price": "0,138 \u20ac",
//                         "Currency": "EUR"
//                     }
//                 ],
//                 "AlternatePackagings": [
//                     {
//                         "APMfrPN": "LM324KAPW"
//                     },
//                     {
//                         "APMfrPN": " LM324KAPWRG4"
//                     }
//                 ],
//                 "ProductDetailUrl": "https:\/\/www.mouser.de\/ProductDetail\/Texas-Instruments\/LM324KAPWR?qs=VBduBm9rCJT8QyuiDYxUYw%3D%3D",
//                 "Reeling": true,
//                 "ROHSStatus": "RoHS Compliant",
//                 "SuggestedReplacement": "595-LM324KAPW",
//                 "MultiSimBlue": 0,
//                 "UnitWeightKg": {
//                     "UnitWeight": 5.72e-5
//                 },
//                 "AvailabilityInStock": "4739",
//                 "AvailabilityOnOrder": [],
//                 "InfoMessages": [],
//                 "SurchargeMessages": null,
//                 "ProductCompliance": [
//                     {
//                         "ComplianceName": "CNHTS",
//                         "ComplianceValue": "8542339000"
//                     },
//                     {
//                         "ComplianceName": "CAHTS",
//                         "ComplianceValue": "8542329000"
//                     },
//                     {
//                         "ComplianceName": "USHTS",
//                         "ComplianceValue": "8542330001"
//                     },
//                     {
//                         "ComplianceName": "JPHTS",
//                         "ComplianceValue": "8542330996"
//                     },
//                     {
//                         "ComplianceName": "KRHTS",
//                         "ComplianceValue": "8532331000"
//                     },
//                     {
//                         "ComplianceName": "MXHTS",
//                         "ComplianceValue": "8542330299"
//                     },
//                     {
//                         "ComplianceName": "ECCN",
//                         "ComplianceValue": "EAR99"
//                     }
//                 ]
//             }
//         ]
//     }
// }
