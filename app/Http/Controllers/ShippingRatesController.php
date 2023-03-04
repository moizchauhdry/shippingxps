<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ShippingRatesController extends Controller
{
    public function index(Request $request)
    {
        try {
            // dd($request->all());

            if ($request->ship_from == 'other') {
                $ship_from_postal_code = $request->ship_from_postal_code;
            } else {
                $warehouse = Warehouse::find($request->ship_from);
                $ship_from_postal_code = $warehouse->zip;
            }

            dd($ship_from_postal_code);

            $client = new Client();

            $result = $client->post('https://apis.fedex.com/oauth/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'l7ef7275cc94544aaabf802ef4308bb66a',
                    'client_secret' => 'e4c4374b-aa10-4c7a-89a6-4ff0d4236d56',
                ]
            ]);

            $authorization = $result->getBody()->getContents();
            $authorization = json_decode($authorization);

            $headers = [
                'X-locale' => 'en_US',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $authorization->access_token
            ];

            // FEDEX SHIPPING RATES
            $units = explode('_', $request->units);
            $weight_units = $units[0];
            $dimension_units = $units[1];

            $requested_package_line_items = [];
            foreach ($request->dimensions as $key => $dimension) {
                $requested_package_line_items[] =  [
                    "weight" => [
                        "units" => $weight_units,
                        "value" => $dimension['weight']
                    ],
                    "dimensions" => [
                        "length" => $dimension['length'],
                        "width" => $dimension['width'],
                        "height" => $dimension['height'],
                        "units" => $dimension_units
                    ]
                ];
            }

            $body = [
                "accountNumber" => [
                    "value" => "695684150"
                ],
                "requestedShipment" => [
                    "shipper" => [
                        "address" => [
                            "postalCode" => $ship_from_postal_code,
                            "countryCode" => $request->ship_from_country_code
                        ]
                    ],
                    "recipient" => [
                        "address" => [
                            "postalCode" => $request->ship_to_postal_code,
                            "countryCode" => $request->ship_to_country_code
                        ]
                    ],
                    "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                    "rateRequestType" => [
                        "ACCOUNT"
                    ],
                    "requestedPackageLineItems" => $requested_package_line_items
                ]
            ];

            $request = $client->post('https://apis.fedex.com/rate/v1/rates/quotes', [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            $response = $request->getBody()->getContents();
            $response = json_decode($response);

            $fedex_rates = [];
            foreach ($response->output->rateReplyDetails as $key => $fedex) {
                $fedex_rates[] = [
                    'name' => $fedex->serviceName,
                    'price' => $fedex->ratedShipmentDetails[0]->totalNetFedExCharge,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $fedex_rates,
            ]);
        } catch (\Throwable $th) {
            dd($th);
            return response()->json([
                'status' => false,
                'message' => 'error',
                'data' => [],
            ]);
        }
    }
}
