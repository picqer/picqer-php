<?php

namespace Picqer\Api;

/**
 * Picqer PHP API Client
 *
 * @author Casper Bakker <support@picqer.com>
 * @license http://creativecommons.org/licenses/MIT/ MIT
 */

class Client
{
    protected $company;
    protected $username;
    protected $password;

    protected $apihost = 'picqer.com';
    protected $protocol = 'https';
    protected $apilocation = '/api';
    protected $apiversion = 'v1';

    protected $debug = false;
    protected $clientversion = '0.9.6';

    protected $skipverification = false;

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
        $gotAllCustomers = false;
        $customers = array();
        $i=0;
        while ($gotAllCustomers == false) {
            $filters['offset'] = ($i*100);
            $result = $this->getCustomers($filters);
            if ($result['success']) {
                if (count($result['data']) < 100) {
                    $gotAllCustomers = true;
                }
                foreach ($result['data'] as $customer) {
                    $customers[] = $customer;
                }
                $i++;
            } else {
                return $result;
            }
        }
        $result = array('success'=>true, 'data'=>$customers);
        return $result;
    }

    public function getCustomer($idcustomer)
    {
        $result = $this->sendRequest('/customers/'.$idcustomer);
        return $result;
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
        $result = $this->sendRequest('/customers', $params, 'POST');
        return $result;
    }

    public function editCustomer($idcustomer, $params)
    {
        $result = $this->sendRequest('/customers/'.$idcustomer, $params, 'PUT');
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
        $gotAllProducts = false;
        $products = array();
        $i=0;
        while ($gotAllProducts == false) {
            $filters['offset'] = ($i*100);
            $result = $this->getProducts($filters);
            if ($result['success']) {
                if (count($result['data']) < 100) {
                    $gotAllProducts = true;
                }
                foreach ($result['data'] as $product) {
                    $products[] = $product;
                }
                $i++;
            } else {
                return $result;
            }
        }
        $result = array('success'=>true, 'data'=>$products);
        return $result;
    }

    public function getProduct($idproduct)
    {
        $result = $this->sendRequest('/products/'.$idproduct);
        return $result;
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
        $result = $this->sendRequest('/products', $params, 'POST');
        return $result;
    }

    public function getProductStock($idproduct)
    {
        $result = $this->sendRequest('/products/'.$idproduct.'/stock');
        return $result;
    }

    public function getProductStockForWarehouse($idproduct, $idwarehouse)
    {
        $result = $this->sendRequest('/products/'.$idproduct.'/stock/'.$idwarehouse);
        return $result;
    }

    public function updateProductStockForWarehouse($idproduct, $idwarehouse, $params)
    {
        $result = $this->sendRequest('/products/'.$idproduct.'/stock/'.$idwarehouse, $params, 'POST');
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
        $gotAllOrders = false;
        $orders = array();
        $i=0;
        while ($gotAllOrders == false) {
            $filters['offset'] = ($i*100);
            $result = $this->getOrders($filters);
            if ($result['success']) {
                if (count($result['data']) < 100) {
                    $gotAllOrders = true;
                }
                foreach ($result['data'] as $order) {
                    $orders[] = $order;
                }
                $i++;
            } else {
                return $result;
            }
        }
        $result = array('success'=>true, 'data'=>$orders);
        return $result;
    }

    public function getOrder($idorder)
    {
        $result = $this->sendRequest('/orders/'.$idorder);
        return $result;
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
        $result = $this->sendRequest('/orders', $params, 'POST');
        return $result;
    }

    public function closeOrder($idorder)
    {
        $result = $this->sendRequest('/orders/'.$idorder.'/close', null, 'POST');
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
        $gotAllPicklists = false;
        $picklists = array();
        $i=0;
        while ($gotAllPicklists == false) {
            $filters['offset'] = ($i*100);
            $result = $this->getPicklists($filters);
            if ($result['success']) {
                if (count($result['data']) < 100) {
                    $gotAllPicklists = true;
                }
                foreach ($result['data'] as $picklist) {
                    $picklists[] = $picklist;
                }
                $i++;
            } else {
                return $result;
            }
        }
        $result = array('success'=>true, 'data'=>$picklists);
        return $result;
    }

    public function getPicklist($idpicklist)
    {
        $result = $this->sendRequest('/picklists/'.$idpicklist);
        return $result;
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
        $result = $this->sendRequest('/picklists/'.$idpicklist.'/close', null, 'POST');
        return $result;
    }

    public function pickallPicklist($idpicklist)
    {
        $result = $this->sendRequest('/picklists/'.$idpicklist.'/pickall', null, 'POST');
        return $result;
    }

    public function createShipment($idpicklist, $params)
    {
        $result = $this->sendRequest('/picklists/'.$idpicklist.'/shipments', $params, 'POST');
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
        $result = $this->sendRequest('/suppliers/'.$idsupplier);
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
        $result = $this->sendRequest('/purchaseorders/'.$idpurchaseorder);
        return $result;
    }

    public function receivePurchaseorderProduct($idpurchaseorder, $params)
    {
        $result = $this->sendRequest('/purchaseorders/'.$idpurchaseorder.'/receive', $params, 'POST');
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
        $result = $this->sendRequest('/tags/'.$idtag);
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
        $result = $this->sendRequest('/vatgroups/'.$idvatgroup);
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
        $result = $this->sendRequest('/hooks/'.$id);
        return $result;
    }

    /*
     * Backorders
     */
    public function processBackorders()
    {
        $result = $this->sendRequest('/backorders/process', null, 'POST');
        return $result;
    }


    /*
     * General
     */

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

    public function enableDebugmode()
    {
        $this->debug = true;
    }

    protected function sendRequest($endpoint, $params=array(), $method='GET', $filters = array())
    {
        $ch = curl_init();

        if (!empty($filters)) {
            $i=0;
            foreach ($filters as $key => $value) {
                if ($i==0) {
                    $endpoint .= '?';
                } else {
                    $endpoint .= '&';
                }
                $endpoint .= $key.'='.urlencode($value);
                $i++;
            }
        }

        if ($this->debug) {
            echo 'URL: '.$this->getUrl($endpoint).PHP_EOL;
        }

        curl_setopt($ch, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Picqer PHP API Client '.$this->clientversion.' (www.picqer.com)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));
        if ($method == 'POST' || $method == 'PUT') {
            $data = $this->prepareData($params);
            if ($this->debug) {
                echo 'Data: '.$data.PHP_EOL;
            }
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
            } elseif ($method == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($this->skipverification) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $apiresult = curl_exec($ch);
        $headerinfo = curl_getinfo($ch);

        if ($this->debug) {
            echo 'Raw result: '.$apiresult.PHP_EOL;
        }

        curl_close($ch);
        $apiresult_json = json_decode($apiresult, true);

        $result = array();
        if (!in_array($headerinfo['http_code'], array('200','201','204'))) {
            $result['success'] = false;
            $result['error'] = true;
            $result['errorcode'] = $headerinfo['http_code'];
            if (isset($apiresult)) {
                $result['errormessage'] = $apiresult;
            }
        } else {
            $result['success'] = true;
            $result['data'] = (($apiresult_json===null)?$apiresult:$apiresult_json);
        }

        return $result;
    }

    protected function getUrl($endpoint)
    {
        return $this->protocol.'://'.$this->company.'.'.$this->apihost.$this->apilocation.'/'.$this->apiversion.$endpoint;
    }

    protected function prepareData($params)
    {
        $data = json_encode($params);
        return $data;
    }
}
