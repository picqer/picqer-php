<?php

namespace Picqer\Api;

/**
 * Picqer PHP API Client
 *
 * @author Casper Bakker <info@picqer.com>
 * @license http://creativecommons.org/licenses/MIT/ MIT
 */
class Client
{
    protected $company;
    protected $apikey;
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

    public function __construct($company, $apikey = '', $password = 'X')
    {
        $this->company = $company;
        $this->apikey = $apikey;
        $this->password = $password; // Only needed with legacy integrations
    }

    /*
     * Customers
     */
    public function getCustomers($filters = array())
    {
        return $this->sendRequest('/customers', null, null, $filters);
    }

    public function getAllCustomers($filters = array())
    {
        return $this->getAllResults('customer', $filters);
    }

    public function getCustomer($idcustomer)
    {
        return $this->sendRequest('/customers/' . $idcustomer);
    }

    public function getCustomerByCustomerid($customerid)
    {
        $result = $this->sendRequest('/customers?customerid=' . urlencode($customerid));
        if (is_array($result['data']) && count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        } else {
            $result = null;
        }
        return $result;
    }

    public function addCustomer($params)
    {
        return $this->sendRequest('/customers', $params, 'POST');
    }

    /** @deprecated Use the `update`, stays here for backwards compatibility */
    public function editCustomer($idcustomer, $params)
    {
        $this->updateCustomer($idcustomer, $params);
    }

    public function updateCustomer($idcustomer, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer, $params, 'PUT');
    }

    public function getCustomerAddress($idcustomer)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses');
    }

    public function addCustomerAddress($idcustomer, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses', $params, 'POST');
    }

    public function updateCustomerAddress($idcustomer, $idaddress, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, $params, 'POST');
    }

    public function deleteCustomerAddress($idcustomer, $idaddress)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, array(), 'DELETE');
    }

    /*
     * Products
     */
    public function getProducts($filters = array())
    {
        $endpoint = '/products';
        return $this->sendRequest($endpoint, null, null, $filters);
    }

    public function getAllProducts($filters = array())
    {
        return $this->getAllResults('product', $filters);
    }

    public function getProduct($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct);
    }

    public function getProductByProductcode($productcode)
    {
        $result = $this->sendRequest('/products?productcode=' . urlencode($productcode));

        if (is_array($result['data']) && count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        } else {
            $result = null;
        }

        return $result;
    }

    public function addProduct($params)
    {
        return $this->sendRequest('/products', $params, 'POST');
    }

    public function getProductStock($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock');
    }

    public function getProductStockForWarehouse($idproduct, $idwarehouse)
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse);
    }

    public function updateProductStockForWarehouse($idproduct, $idwarehouse, $params)
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse, $params, 'POST');
    }

    public function getProductWarehouseSettings($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses');
    }

    public function updateProductWarehouseSetting($idproduct, $idwarehouse, $params)
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses/' . $idwarehouse, $params, 'PUT');
    }

    public function getProductImages($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images');
    }

    public function addImageToProduct($idproduct, $base64Image)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images', array('image' => $base64Image), 'POST');
    }

    public function deleteImageFromProduct($idproduct, $idproduct_image)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images/' . $idproduct_image, array(), 'DELETE');
    }

    public function updateProduct($idproduct, $params)
    {
        return $this->sendRequest('/products/' . $idproduct, $params, 'PUT');
    }

    public function getProductTags($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags');
    }

    public function addProductTag($idproduct, $idtag)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags', array("idtag" => $idtag), 'POST');
    }

    public function deleteProductTag($idproduct, $idtag)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags/' . $idtag, array(), 'DELETE');
    }

    /*
     * Orders
     */
    public function getOrders($filters = array())
    {
        return $this->sendRequest('/orders', null, null, $filters);
    }

    public function getAllOrders($filters = array())
    {
        return $this->getAllResults('order', $filters);
    }

    public function getOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder);
    }

    public function getOrderByOrderid($orderid)
    {
        $result = $this->sendRequest('/orders?orderid=' . urlencode($orderid));

        if (count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        }

        return $result;
    }

    public function addOrder($params)
    {
        return $this->sendRequest('/orders', $params, 'POST');
    }

    public function cancelOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder, array(), 'DELETE');
    }

    public function getOrderProductStatus($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/productstatus');
    }

    public function closeOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/close', null, 'POST');
    }

    public function processOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/process', null, 'POST');
    }

    public function addOrderNote($idorder, $note)
    {
        return $this->sendRequest('/orders/' . $idorder . '/notes', array("note" => $note), 'POST');
    }

    public function getOrderTags($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags');
    }

    public function addOrderTag($idorder, $idtag)
    {
        $params = array(
            'idtag' => $idtag
        );

        return $this->sendRequest('/orders/' . $idorder . '/tags', $params, 'POST');
    }

    /** @deprecated Use the `delete`, stays here for backwards compatibility */
    public function removeOrderTag($idorder, $idtag)
    {
        $this->deleteOrderTag($idorder, $idtag);
    }

    public function deleteOrderTag($idorder, $idtag)
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags/' . $idtag, array(), 'DELETE');
    }

    public function updateOrder($idorder, $params)
    {
        return $this->sendRequest('/orders/' . $idorder, $params, 'PUT');
    }

    /*
     * Orders
     */
    public function getPicklists($filters = array())
    {
        return $this->sendRequest('/picklists', null, null, $filters);
    }

    public function getAllPicklists($filters = array())
    {
        return $this->getAllResults('picklist', $filters);
    }

    public function getPicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist);
    }

    public function getPicklistByPicklistid($picklistid)
    {
        $result = $this->sendRequest('/picklists?picklistid=' . urlencode($picklistid));

        if (count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        }

        return $result;
    }

    public function closePicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/close', null, 'POST');
    }

    public function pickallPicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/pickall', null, 'POST');
    }

    public function createShipment($idpicklist, $params)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments', $params, 'POST');
    }

    public function getShipments($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments');
    }

    /*
     * Suppliers
     */
    public function getSuppliers()
    {
        return $this->sendRequest('/suppliers');
    }

    public function getSupplier($idsupplier)
    {
        return $this->sendRequest('/suppliers/' . $idsupplier);
    }

    /*
     * Purchase orders
     */
    public function getPurchaseorders()
    {
        return $this->sendRequest('/purchaseorders');
    }

    public function getPurchaseorder($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder);
    }

    public function receivePurchaseorderProduct($idpurchaseorder, $params)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receive', $params, 'POST');
    }

    /*
     * Tags
     */
    public function getTags()
    {
        return $this->sendRequest('/tags');
    }

    public function getTag($idtag)
    {
        return $this->sendRequest('/tags/' . $idtag);
    }

    /*
     * VAT Groups
     */
    public function getVatgroups()
    {
        return $this->sendRequest('/vatgroups');
    }

    public function getVatgroup($idvatgroup)
    {
        return $this->sendRequest('/vatgroups/' . $idvatgroup);
    }

    /*
     * Stock history
     */
    public function getStockHistory()
    {
        return $this->sendRequest('/stockhistory');
    }

    public function getStockHistoryOfProduct($idproduct)
    {
        return $this->sendRequest('/stockhistory/' . $idproduct);
    }

    /*
     * Hooks
     */
    public function addHook($params)
    {
        return $this->sendRequest('/hooks', $params, 'POST');
    }

    public function getHooks()
    {
        return $this->sendRequest('/hooks');
    }

    public function getHook($id)
    {
        return $this->sendRequest('/hooks/' . $id);
    }

    public function deleteHook($id)
    {
        return $this->sendRequest('/hooks/' . $id, array(), 'DELETE');
    }

    /*
     * Backorders
     */
    public function getBackorders($filters = array())
    {
        return $this->sendRequest('/backorders', null, null, $filters);
    }

    public function getBackorder($idbackorder)
    {
        return $this->sendRequest('/backorders/' . $idbackorder);
    }

    public function processBackorders()
    {
        return $this->sendRequest('/backorders/process', null, 'POST');
    }

    /*
     * Warehouses
     */
    public function getWarehouses()
    {
        return $this->sendRequest('/warehouses');
    }

    public function getWarehouse($idwarehouse)
    {
        return $this->sendRequest('/warehouses/' . $idwarehouse);
    }

    /*
     * Pricelists
     */
    public function getPricelists()
    {
        return $this->sendRequest('/pricelists');
    }

    public function getPricelist($idpricelist)
    {
        return $this->sendRequest('/pricelists/' . $idpricelist);
    }

    /*
     * Shipping providers
     */
    public function getShippingProividers()
    {
        return $this->sendRequest('/shippingproviders');
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
        while ($gotAll == false) {
            $filters['offset'] = ($i * 100);
            $result = $this->$functionname($filters);
            if (isset($result['success']) && $result['success'] && isset($result['data'])) {
                if (count($result['data']) < 100) {
                    $gotAll = true;
                }
                foreach ($result['data'] as $item) {
                    $collection[] = $item;
                }
                $i++;
            } else {
                return $result;
            }
        }

        return array('success' => true, 'data' => $collection);
    }

    /**
     * Creates a new company account for Picqer
     * @param $params
     * @return mixed
     */
    public function addCompany($params)
    {
        return $this->sendRequest('/companies', $params, 'POST');
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

        $this->debug('URL: ' . $this->getUrl($endpoint));

        curl_setopt($ch, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutInSeconds);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apikey . ':' . $this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent . ' ' . $this->clientversion);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        if ($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
            $data = $this->prepareData($params);

            $this->debug('Data: ' . $data);

            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } elseif ($method == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            } elseif ($method == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($this->skipverification) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $apiresult = curl_exec($ch);
        $headerinfo = curl_getinfo($ch);

        $this->debug('Raw result: ' . $apiresult);

        $apiresult_json = json_decode($apiresult, true);

        $result = array();
        $result['success'] = false;

        // CURL failed
        if ($apiresult === false) {
            $result['error'] = true;
            $result['errorcode'] = 0;
            $result['errormessage'] = curl_error($ch);
            return $result;
        }

        curl_close($ch);

        if (! in_array($headerinfo['http_code'], array('200', '201', '204'))) {
            // API returns error
            $result['error'] = true;
            $result['errorcode'] = $headerinfo['http_code'];
            if (isset($apiresult)) {
                $result['errormessage'] = $apiresult;
            }
        } else {
            // API returns success
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
        return json_encode($params);
    }

    protected function getEndpoint($endpoint, $filters)
    {
        if (! empty($filters)) {
            $i = 0;
            foreach ($filters as $key => $value) {
                if ($i == 0) {
                    $endpoint .= '?';
                } else {
                    $endpoint .= '&';
                }
                $endpoint .= $key . '=' . urlencode($value);
                $i++;
            }
        }

        return $endpoint;
    }

    protected function debug($message)
    {
        if ($this->debug) {
            echo 'Debug: ' . $message . PHP_EOL;
        }
    }
}
