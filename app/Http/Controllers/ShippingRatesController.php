<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Warehouse;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ShippingRatesController extends Controller
{
    public function index(Request $request)
    {
        try {

            if ($request->ship_to_postal_code) {
                $ship_to_postal_code = $request->ship_to_postal_code;
            } else {
                $ship_to_postal_code = '00000';
            }

            if ($request->ship_from == 'other') {
                $ship_from_postal_code = $request->ship_from_postal_code;
                $ship_from_city = $request->ship_from_city;
            } else {
                $warehouse = Warehouse::find($request->ship_from);
                $ship_from_postal_code = $warehouse->zip;
                $ship_from_city = $warehouse->city;
            }

            $units = explode('_', $request->units);
            $weight_units = $units[0];
            $dimension_units = $units[1];

            $measurement_unit = 'imperial';
            if ($weight_units == 'KG') {
                $measurement_unit = 'metric';
            }

            $data = [
                'ship_from_postal_code' => $ship_from_postal_code,
                'ship_from_country_code' => $request->ship_from_country_code,
                'ship_from_city' => $ship_from_city,
                'ship_to_postal_code' => $ship_to_postal_code,
                'ship_to_country_code' => $request->ship_to_country_code,
                'ship_to_city' => $request->ship_to_city,
                'weight_units' => $weight_units,
                'dimension_units' => $dimension_units,
                'measurement_unit' => $measurement_unit,
                'customs_value' => $request->customs_value,
                'dimensions' => $request->dimensions,
            ];

            $fedex_rates = $this->fedex($data);
            $dhl_rates = $this->dhl($data);

            $rates = array_merge($fedex_rates, $dhl_rates);

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $rates,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'error',
                'data' => [],
            ]);
        }
    }

    public function fedex($data)
    {
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

        $requested_package_line_items = [];
        foreach ($data['dimensions'] as $key => $dimension) {
            $requested_package_line_items[] =  [
                "weight" => [
                    "units" => $data['weight_units'],
                    "value" => $dimension['weight']
                ],
                "dimensions" => [
                    "length" => $dimension['length'],
                    "width" => $dimension['width'],
                    "height" => $dimension['height'],
                    "units" => $data['dimension_units']
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
                        "postalCode" => $data['ship_from_postal_code'],
                        "countryCode" => $data['ship_from_country_code']
                    ]
                ],
                "recipient" => [
                    "address" => [
                        "postalCode" => $data['ship_to_postal_code'],
                        "countryCode" => $data['ship_to_country_code']
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

        $markup = SiteSetting::getByName('markup');

        $rates = [];
        foreach ($response->output->rateReplyDetails as $key => $fedex) {
            $price = $fedex->ratedShipmentDetails[0]->totalNetFedExCharge;
            $markup_amount = $fedex->ratedShipmentDetails[0]->totalNetFedExCharge * ((int)$markup / 100);
            $total = $price + $markup_amount;
            $total = number_format((float)$total, 2, '.', '');

            $rates[] = [
                'code' => 'fedex',
                'type' => $fedex->serviceType,
                'name' => $fedex->serviceName,
                'pkg_type' => $fedex->packagingType,
                'price' => $price,
                'markup' => $markup_amount,
                'total' => $total,
            ];
        }

        return $rates;
    }

    public function dhl($data)
    {
        try {
            $packages = [];
            foreach ($data['dimensions'] as $key => $dimension) {
                $packages[] =  [
                    "weight" => (float) $dimension['weight'],
                    "dimensions" => [
                        "length" => (float) $dimension['length'],
                        "width" => (float) $dimension['width'],
                        "height" => (float) $dimension['height']
                    ]
                ];
            }

            $client = new Client();

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic YXBHN3RWNGNSMWFVOGQ6Wl40c0ckMXlSQDZ4VSM5Yw=='
            ];

            $body = [
                "customerDetails" => [
                    "shipperDetails" => [
                        "postalCode" => $data['ship_from_postal_code'],
                        "cityName" => $data['ship_from_city'],
                        "countryCode" => $data['ship_from_country_code']
                    ],
                    "receiverDetails" => [
                        "postalCode" => $data['ship_to_postal_code'],
                        "cityName" => $data['ship_to_city'],
                        "countryCode" => $data['ship_to_country_code']
                    ]
                ],
                "accounts" => [
                    [
                        "typeCode" => "shipper",
                        "number" => "849192247"
                    ]
                ],
                "productsAndServices" => [
                    [
                        "productCode" => "P",
                        "localProductCode" => "P"
                    ]
                ],
                "payerCountryCode" => "US",
                "plannedShippingDateAndTime" => Carbon::now(),
                "unitOfMeasurement" => $data['measurement_unit'],
                "isCustomsDeclarable" => true,
                "monetaryAmount" => [
                    [
                        "typeCode" => "declaredValue",
                        "value" => (float) $data['customs_value'],
                        "currency" => "USD"
                    ]
                ],
                "estimatedDeliveryDate" => [
                    "isRequested" => true,
                    "typeCode" => "QDDC"
                ],
                "getAdditionalInformation" => [
                    [
                        "typeCode" => "allValueAddedServices",
                        "isRequested" => true
                    ]
                ],
                "returnStandardProductsOnly" => false,
                "nextBusinessDay" => true,
                "productTypeCode" => "all",
                "packages" => $packages
            ];

            $request = $client->post('https://express.api.dhl.com/mydhlapi/test/rates', [
                'headers' => $headers,
                'body' => json_encode($body)
            ]);

            $response = $request->getBody()->getContents();
            $response = json_decode($response);

            $markup = SiteSetting::getByName('markup');
            $price = $response->products[0]->totalPrice[0]->price;
            $markup_amount = $response->products[0]->totalPrice[0]->price * ((int)$markup / 100);
            $total = $price + $markup_amount;
            $total = number_format((float)$total, 2, '.', '');

            $rates = [];
            $rates[] = [
                'code' => 'dhl',
                'type' => 'EXPRESS_WORLDWIDE',
                'name' => 'DHL Express Worldwide',
                'pkg_type' => 'YOUR_PACKAGING',
                'price' => $price,
                'markup' => $markup_amount,
                'total' => $total,
            ];

            return $rates;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
