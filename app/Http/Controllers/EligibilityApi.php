<?php

namespace App\Services;

use App\Models\InsurancePayer;
use App\Patient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\SeekException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;

class EligibilityApi
{
    protected $url;
    protected $token;
    protected $http;
    protected $headers;

    public function __construct(Client $client)
    {
        $this->url = 'https://apis.changehealthcare.com/medicalnetwork/eligibility/v3';
        $this->http = $client;
        $this->authToken = '';
        //get auth
        $full_path = 'https://apis.changehealthcare.com/apip/auth/v1/token';

        $post_params['client_id'] = 'pIli8rS5iFU7vmy4U7NczBGpC23AARv3';
        $post_params['client_secret'] = 'YU5sXVTSYwzmmJ0v';
        $post_params['grant_type'] = 'client_credentials';

        try {
            $request = $this->http->post($full_path, [

                'http_errors' => true,
                'form_params' => $post_params,
            ]);
        } catch (ServerException $exception) {
            return null;
        } catch (ConnectException $exception) {
            return null;
        } catch (ClientException $exception) {
            return null;
        } catch (RequestException $exception) {
            return null;
        } catch (BadResponseException $exception) {
            return null;
        }catch (TransferException $exception) {
            return null;
        } catch (SeekException $exception) {
            return null;
        }catch (\Exception $exception) {
            return null;
        }

        $response = $request ? $request->getBody()->getContents() : null;
        $status = $request ? $request->getStatusCode() : 500;

        if ($response && $status === 200 && $response !== 'null') {
            $this->authToken = json_decode($response)->accessToken;
        }


        $this->headers = [
            'cache-control' => 'no-cache',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->authToken
        ];
    }


    private function getResponse($post_params)
    {
        $full_path = $this->url;

        try {
            
            $request = $this->http->post($full_path, [
                'headers' => $this->headers,
                'body' => json_encode($post_params),
                'http_errors' => true,
            ]);

        } catch (ServerException $exception) {
            return null;
        } catch (ConnectException $exception) {
            return null;
        } catch (ClientException $exception) {
            return null;
        } catch (RequestException $exception) {
            return null;
        } catch (BadResponseException $exception) {
            return null;
        }catch (TransferException $exception) {
            return null;
        } catch (SeekException $exception) {
            return null;
        }catch (\Exception $exception) {
            return null;
        }


        $response = $request ? $request->getBody()->getContents() : null;

        $status = $request ? $request->getStatusCode() : 500;

        if ($response && $status === 200 && $response !== 'null') {
            return (object)json_decode($response);
        }

        return null;
    }

    private function postResponse(string $uri = null, array $post_params = [])
    {
        $full_path = $this->url;
        $full_path .= $uri;

        $request = $this->http->post($full_path, [
            'headers' => $this->headers,
            'timeout' => 30,
            'connect_timeout' => true,
            'http_errors' => true,
            'form_params' => $post_params,
        ]);

        $response = $request ? $request->getBody()->getContents() : null;
        $status = $request ? $request->getStatusCode() : 500;

        if ($response && $status === 200 && $response !== 'null') {
            return (object)json_decode($response);
        }

        return null;
    }

    public function authenticate()
    {
        $full_path = 'https://apis.changehealthcare.com/apip/auth/v1/token';
        $post_params['client_id'] = env('APP_URL');
        $post_params['client_secret'] = env('CHC_CLIENT_SECRET');
        $post_params['grant_type'] = 'client_credentials';

        $request = $this->http->post($full_path, [

            'http_errors' => true,
            'form_params' => $post_params,
        ]);

        $response = $request ? $request->getBody()->getContents() : null;
        $status = $request ? $request->getStatusCode() : 500;

        if ($response && $status === 200 && $response !== 'null') {
            $this->authToken = json_decode($response)->accessToken;
            dump($this->authToken);
        }

    }

    public function checkEligibility($request_param)
    {

        $payer_id = \Session::get('patient_medicare');

        $payer = InsurancePayer::find($payer_id);

        $user = \Auth::user();
        $patient = $user->patient;

        $fname = isset($request_param->fname) ? $request_param->fname : $patient->fname;
        $lname = isset($request_param->lname) ? $request_param->lname : $patient->lname;
        $gender = isset($request_param->gender) ? $request_param->gender : $patient->gender;
        $dob = isset($request_param->month_dob) ? $request_param->year_dob . $request_param->month_dob . $request_param->day_dob : $patient->year_dob . $patient->month_dob . $patient->day_dob;
        $member_id = $request_param->member_id;

        $provider['organizationName'] = 'ORTHO ON DEMAND P.C.';
        $provider['npi'] = '1982106878';
        $provider['referenceIdentification'] = '193400000X';
        $provider['providerCode'] = 'AD';

        $subscriber['memberId'] = $member_id;
        $subscriber['firstName'] = $fname;
        $subscriber['lastName'] = $lname;
        $subscriber['gender'] = $gender;
        $subscriber['dateOfBirth'] = $dob;

        $post_params['controlNumber'] = '000000016';
        $post_params['tradingPartnerServiceId'] = $payer->eligibility_payer_id;
        $post_params['provider'] = $provider;
        $post_params['subscriber'] = $subscriber;

        \Log::info('eligibility' . json_encode($post_params));
        $eligibility_response = $this->getResponse($post_params);
        if (isset($eligibility_response->planStatus)) {
            $plan_status = $eligibility_response->planStatus;
            if (isset($plan_status[0]->statusCode) && $plan_status[0]->statusCode == 1) {
                $eligibility['status'] = 1;

            } else {
                $eligibility['status'] = 0;
            }
//            if(isset($plan_status[0]))
        } else {
            $eligibility['status'] = 0;
        }
        $eligibility['response'] = $eligibility_response;

        return $eligibility;
    }
}
