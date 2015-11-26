<?php

namespace Picqer\Api;

/**
 * Picqer PHP API Client
 *
 * @author Casper Bakker <info@picqer.com>
 * @license http://creativecommons.org/licenses/MIT/ MIT
 */

class Client {
    protected $company;
    protected $username;
    protected $password;

    protected $apihost = 'picqer.com';
    protected $protocol = 'https';
    protected $apilocation = '/api';
    protected $apiversion = 'v1';
    protected $useragent = 'Picqer PHP API Client (picqer.com)';

    protected $clientversion = '0.10.0';

    protected $debug = false;
    protected $skipverification = false;

    protected $timeoutInSeconds = 60;

    public function __construct($company, $username = '', $password = 'X')
    {
        $this->company = $company;
        $this->username = $username;
        $this->password = $password;
    }

    /*
     * Customers
     */
    public function getCustomers($filters = array())
    {
        $result = $this->sendRequest('/customers', null, null, $filters);
        return $result;
    }

    public function getAllCustomers($filters = array())
    {
        return $this->getAllResults('customer', $filters);
    }

    public function getCustomer($idcustomer)
    {
        $result = $this->sendRequest('/customers/' . $idcustomer);
        return $result;
    }

    public function getCustomerByCustomerid($customerid)
    {
        $result = $this->sendRequest('/customers?customerid=' . urlencode($customerid));
        if (is_array($result['data']) && count($result['data']) == 1)
        {
            $result['data'] = $result['data'][0];
        } else
        {
            $result = null;
        }
        return $result;
    }

    public function addCustomer($params)
    {
        $result = $this->sendRequest('/customers', $params, 'POST');
        return $result;
    }

    public function editCustomer($idcustomer, $params)
    {
        $result = $this->sendRequest('/customers/' . $idcustomer, $params, 'PUT');
        return $result;
    }

    /*
     * Products
     */
    public function getProducts($filters = array())
    {
        $endpoint = '/products';
        $result = $this->sendRequest($endpoint, null, null, $filters);
        return $result;
    }

    public function getAllProducts($filters = array())
    {
        return $this->getAllResults('product', $filters);
    }

    public function getProduct($idproduct)
    {
        $result = $this->sendRequest('/products/' . $idproduct);
        return $result;
    }

    public function getProductByProductcode($productcode)
    {
        $result = $this->sendRequest('/products?productcode=' . urlencode($productcode));
        if (is_array($result['data']) && count($result['data']) == 1)
        {
            $result['data'] = $result['data'][0];
        } else
        {
            $result = null;
        }
        return $result;
    }

    public function addProduct($params)
    {
        $result = $this->sendRequest('/products', $params, 'POST');
        return $result;
    }

    public function getProductStock($idproduct)
    {
        $result = $this->sendRequest('/products/' . $idproduct . '/stock');
        return $result;
    }

    public function getProductStockForWarehouse($idproduct, $idwarehouse)
    {
        $result = $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse);
        return $result;
    }

    public function updateProductStockForWarehouse($idproduct, $idwarehouse, $params)
    {
        $result = $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse, $params, 'POST');
        return $result;
    }

    /*
     * Orders
     */
    public function getOrders($filters = array())
    {
        $result = $this->sendRequest('/orders', null, null, $filters);
        return $result;
    }

    public function getAllOrders($filters = array())
    {
        return $this->getAllResults('order', $filters);
    }

    public function getOrder($idorder)
    {
        $result = $this->sendRequest('/orders/' . $idorder);
        return $result;
    }

    public function getOrderByOrderid($orderid)
    {
        $result = $this->sendRequest('/orders?orderid=' . urlencode($orderid));
        if (count($result['data']) == 1)
        {
            $result['data'] = $result['data'][0];
        }
        return $result;
    }

    public function addOrder($params)
    {
        $result = $this->sendRequest('/orders', $params, 'POST');
        return $result;
    }

    public function closeOrder($idorder)
    {
        $result = $this->sendRequest('/orders/' . $idorder . '/close', null, 'POST');
        return $result;
    }

    public function getOrderTags($idorder)
    {
        $result = $this->sendRequest('/orders/' . $idorder . '/tags');
        return $result;
    }

    public function addOrderTag($idorder, $idtag)
    {
        $params = array(
            'idtag' => $idtag
        );

        $result = $this->sendRequest('/orders/' . $idorder . '/tags', $params, 'POST');
        return $result;
    }

    public function removeOrderTag($idorder, $idtag)
    {
        $result = $this->sendRequest('/orders/' . $idorder . '/tags/' . $idtag, array(), 'DELETE');
        return $result;
    }

    /*
     * Orders
     */
    public function getPicklists($filters = array())
    {
        $result = $this->sendRequest('/picklists', null, null, $filters);
        return $result;
    }

    public function getAllPicklists($filters = array())
    {
        return $this->getAllResults('picklist', $filters);
    }

    public function getPicklist($idpicklist)
    {
        $result = $this->sendRequest('/picklists/' . $idpicklist);
        return $result;
    }

    public function getPicklistByPicklistid($picklistid)
    {
        $result = $this->sendRequest('/picklists?picklistid=' . urlencode($picklistid));
        if (count($result['data']) == 1)
        {
            $result['data'] = $result['data'][0];
        }
        return $result;
    }

    public function closePicklist($idpicklist)
    {
        $result = $this->sendRequest('/picklists/' . $idpicklist . '/close', null, 'POST');
        return $result;
    }

    public function pickallPicklist($idpicklist)
    {
        $result = $this->sendRequest('/picklists/' . $idpicklist . '/pickall', null, 'POST');
        return $result;
    }

    public function createShipment($idpicklist, $params)
    {
        $result = $this->sendRequest('/picklists/' . $idpicklist . '/shipments', $params, 'POST');
        return $result;
    }

    public function getShipments($idpicklist)
    {
        $result = $this->sendRequest('/picklists/' . $idpicklist . '/shipments');
        return $result;
    }

    /*
     * Suppliers
     */
    public function getSuppliers()
    {
        $result = $this->sendRequest('/suppliers');
        return $result;
    }

    public function getSupplier($idsupplier)
    {
        $result = $this->sendRequest('/suppliers/' . $idsupplier);
        return $result;
    }

    /*
     * Purchase orders
     */
    public function getPurchaseorders()
    {
        $result = $this->sendRequest('/purchaseorders');
        return $result;
    }

    public function getPurchaseorder($idpurchaseorder)
    {
        $result = $this->sendRequest('/purchaseorders/' . $idpurchaseorder);
        return $result;
    }

    public function receivePurchaseorderProduct($idpurchaseorder, $params)
    {
        $result = $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receive', $params, 'POST');
        return $result;
    }

    /*
     * Tags
     */
    public function getTags()
    {
        $result = $this->sendRequest('/tags');
        return $result;
    }

    public function getTag($idtag)
    {
        $result = $this->sendRequest('/tags/' . $idtag);
        return $result;
    }

    /*
     * VAT Groups
     */
    public function getVatgroups()
    {
        $result = $this->sendRequest('/vatgroups');
        return $result;
    }

    public function getVatgroup($idvatgroup)
    {
        $result = $this->sendRequest('/vatgroups/' . $idvatgroup);
        return $result;
    }

    /*
     * Hooks
     */
    public function addHook($params)
    {
        $result = $this->sendRequest('/hooks', $params, 'POST');
        return $result;
    }

    public function getHooks()
    {
        $result = $this->sendRequest('/hooks');
        return $result;
    }

    public function getHook($id)
    {
        $result = $this->sendRequest('/hooks/' . $id);
        return $result;
    }

    public function deleteHook($id)
    {
        $result = $this->sendRequest('/hooks/' . $id, array(), 'DELETE');
        return $result;
    }

    /*
     * Backorders
     */
    public function getBackorders($filters = array())
    {
        $result = $this->sendRequest('/backorders', null, null, $filters);
        return $result;
    }

    public function getBackorder($idbackorder)
    {
        $result = $this->sendRequest('/backorders/' . $idbackorder);
        return $result;
    }

    public function processBackorders()
    {
        $result = $this->sendRequest('/backorders/process', null, 'POST');
        return $result;
    }

    /*
     * Warehouses
     */
    public function getWarehouses()
    {
        $result = $this->sendRequest('/warehouses');
        return $result;
    }

    public function getWarehouse($idwarehouse)
    {
        $result = $this->sendRequest('/warehouses/' . $idwarehouse);
        return $result;
    }

    /*
     * General
     */
    public function getAllResults($entity, $filters = array())
    {
        $gotAll = false;
        $collection = array();

        $functionname = 'get' . ucfirst($entity) . 's';

        $i = 0;
        while ($gotAll == false)
        {
            $filters['offset'] = ($i * 100);
            $result = $this->$functionname($filters);
            if (isset($result['success']) && $result['success'] && isset($result['data']))
            {
                if (count($result['data']) < 100)
                {
                    $gotAll = true;
                }
                foreach ($result['data'] as $item)
                {
                    $collection[] = $item;
                }
                $i++;
            } else
            {
                return $result;
            }
        }
        $result = array('success' => true, 'data' => $collection);
        return $result;
    }

    /**
     * Creates a new company account for Picqer
     * @param $params
     * @return mixed
     */
    public function addCompany($params)
    {
        $result = $this->sendRequest('/companies', $params, 'POST');
        return $result;
    }

    /**
     * Enable debug mode gives verbose output on requests and responses
     */
    public function enableDebugmode()
    {
        $this->debug = true;
    }

    /**
     * Disable Curl's SSL verification for testing
     */
    public function disableSslVerification()
    {
        $this->skipverification = true;
    }

    /**
     * @param string $apihost
     */
    public function setApihost($apihost)
    {
        $this->apihost = $apihost;
    }

    /**
     * @param string $protocol http or https
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @param string $useragent
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;
    }

    /**
     * Change the timeout for CURL requests
     * @param int $timeoutInSeconds
     */
    public function setTimeoutInSeconds($timeoutInSeconds)
    {
        $this->timeoutInSeconds = $timeoutInSeconds;
    }

    protected function sendRequest($endpoint, $params = array(), $method = 'GET', $filters = array())
    {
        $ch = curl_init();

        $endpoint = $this->getEndpoint($endpoint, $filters);

        if ($this->debug)
        {
            echo 'URL: ' . $this->getUrl($endpoint) . PHP_EOL;
        }

        curl_setopt($ch, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutInSeconds);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent . ' ' . $this->clientversion);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE')
        {
            $data = $this->prepareData($params);

            if ($this->debug)
                echo 'Data: ' . $data . PHP_EOL;

            if ($method == 'POST')
            {
                curl_setopt($ch, CURLOPT_POST, true);
            } elseif ($method == 'PUT')
            {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            } elseif ($method == 'DELETE')
            {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($this->skipverification)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $apiresult = curl_exec($ch);
        $headerinfo = curl_getinfo($ch);

        if ($this->debug)
            echo 'Raw result: ' . $apiresult . PHP_EOL;

        $apiresult_json = json_decode($apiresult, true);

        $result = array();
        $result['success'] = false;

        if ($apiresult === false) // CURL failed
        {
            $result['error'] = true;
            $result['errorcode'] = 0;
            $result['errormessage'] = curl_error($ch);
            return $result;
        }

        curl_close($ch);

        if ( ! in_array($headerinfo['http_code'], array('200', '201', '204'))) // API returns error
        {
            $result['error'] = true;
            $result['errorcode'] = $headerinfo['http_code'];
            if (isset($apiresult))
            {
                $result['errormessage'] = $apiresult;
            }
        } else // API returns success
        {
            $result['success'] = true;
            $result['data'] = (($apiresult_json === null) ? $apiresult : $apiresult_json);
        }

        return $result;
    }

    protected function getUrl($endpoint)
    {
        return $this->protocol . '://' . $this->company . '.' . $this->apihost . $this->apilocation . '/' . $this->apiversion . $endpoint;
    }

    protected function prepareData($params)
    {
        $data = json_encode($params);
        return $data;
    }

    protected function getEndpoint($endpoint, $filters)
    {
        if ( ! empty($filters))
        {
            $i = 0;
            foreach ($filters as $key => $value)
            {
                if ($i == 0)
                {
                    $endpoint .= '?';
                } else
                {
                    $endpoint .= '&';
                }
                $endpoint .= $key . '=' . urlencode($value);
                $i++;
            }
        }

        return $endpoint;
    }
}
