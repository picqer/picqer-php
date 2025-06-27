<?php

namespace Picqer\Api;

class Client
{
    protected string $company;
    protected string $apiKey;
    protected string $password;

    protected string $protocol = 'https';
    protected string $apiHost = 'picqer.com';
    protected string $apiLocation = '/api';
    protected string $apiVersion = 'v1';
    protected string $userAgent = 'Picqer PHP API Client (picqer.com)';

    protected string $clientVersion = '0.23.1';

    protected bool $debug = false;
    protected bool $skipSslVerification = false;

    protected int $timeoutInSeconds = 60;
    protected bool $waitOnRateLimit = false;
    protected int $sleepTimeOnRateLimitHitInSeconds = 20;

    protected array $entityBaseMethodNameMap = [
        'product' => 'getProducts',
        'picklistBatch' => 'getPicklistBatches',
        'returnStatus' => 'getReturnStatuses',
    ];

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    protected array $rawResponseHeaders = [];

    public function __construct($company, $apikey = '', $password = 'X')
    {
        $this->company = $company;
        $this->apiKey = $apikey;
        $this->password = $password; // Only needed with legacy integrations
    }

    /*
     * Customers
     */
    public function getCustomers($filters = [])
    {
        return $this->sendRequest('/customers', null, null, $filters);
    }

    public function getAllCustomers($filters = [])
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
            $result['data'] = null;
        }
        return $result;
    }

    public function addCustomer($params)
    {
        return $this->sendRequest('/customers', $params, self::METHOD_POST);
    }

    /** @deprecated Use the `update`, stays here for backwards compatibility */
    public function editCustomer($idcustomer, $params)
    {
        $this->updateCustomer($idcustomer, $params);
    }

    public function updateCustomer($idcustomer, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer, $params, self::METHOD_PUT);
    }

    public function getCustomerAddress($idcustomer)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses');
    }

    public function addCustomerAddress($idcustomer, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses', $params, self::METHOD_POST);
    }

    public function updateCustomerAddress($idcustomer, $idaddress, $params)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, $params, self::METHOD_PUT);
    }

    public function deleteCustomerAddress($idcustomer, $idaddress)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, [], self::METHOD_DELETE);
    }

    public function getCustomerTags($idcustomer)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags');
    }

    public function addCustomerTag($idcustomer, $idtag)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags', ['idtag' => $idtag], self::METHOD_POST);
    }

    public function deleteCustomerTag($idcustomer, $idtag)
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    /**
     * Fulfilment customers
     */
    public function getFulfilmentCustomers($filters = [])
    {
        return $this->sendRequest('/fulfilment/customers', null, null, $filters);
    }

    public function getAllFulfilmentCustomers($filters = [])
    {
        return $this->getAllResults('fulfilmentCustomer', $filters);
    }

    public function getFulfilmentCustomer($idfulfilmentcustomer)
    {
        return $this->sendRequest('/fulfilment/customers/' . $idfulfilmentcustomer);
    }

    public function addFulfilmentCustomer($params)
    {
        return $this->sendRequest('/fulfilment/customers', $params, self::METHOD_POST);
    }

    public function updateFulfilmentCustomer($idfulfilmentcustomer, $params)
    {
        return $this->sendRequest('/fulfilment/customers/' . $idfulfilmentcustomer, $params, self::METHOD_PUT);
    }

    /*
     * Products
     */
    public function getProducts($filters = [])
    {
        return $this->sendRequest('/products', null, null, $filters);
    }

    public function getAllProducts($filters = [])
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
            $result['data'] = null;
        }

        return $result;
    }

    public function addProduct($params)
    {
        return $this->sendRequest('/products', $params, self::METHOD_POST);
    }

    public function inactivateProduct($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/inactivate', [], self::METHOD_POST);
    }

    public function activateProduct($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/activate', [], self::METHOD_POST);
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
        return $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse, $params, self::METHOD_POST);
    }

    public function getProductWarehouseSettings($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses');
    }

    public function updateProductWarehouseSetting($idproduct, $idwarehouse, $params)
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses/' . $idwarehouse, $params, self::METHOD_PUT);
    }

    public function getProductImages($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images');
    }

    public function addImageToProduct($idproduct, $base64Image)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images', ['image' => $base64Image], self::METHOD_POST);
    }

    public function deleteImageFromProduct($idproduct, $idproduct_image)
    {
        return $this->sendRequest('/products/' . $idproduct . '/images/' . $idproduct_image, [], self::METHOD_DELETE);
    }

    public function updateProduct($idproduct, $params)
    {
        return $this->sendRequest('/products/' . $idproduct, $params, self::METHOD_PUT);
    }

    public function getProductTags($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags');
    }

    public function addProductTag($idproduct, $idtag)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags', ['idtag' => $idtag], self::METHOD_POST);
    }

    public function deleteProductTag($idproduct, $idtag)
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    public function getProductParts($idproduct)
    {
        return $this->sendRequest('/products/'.$idproduct.'/parts');
    }

    public function getProductPart($idproduct, $idproduct_part)
    {
        return $this->sendRequest('/products/'.$idproduct.'/parts/'.$idproduct_part);
    }

    public function addProductPart($idproduct, $params)
    {
        return $this->sendRequest('/products/' . $idproduct . '/parts', $params, self::METHOD_POST);
    }

    public function updateProductPartAmount($idproduct, $idproductpart, $amount)
    {
        $params = ['amount' => $amount];

        return $this->sendRequest('/products/' . $idproduct . '/parts/'.$idproductpart, $params, self::METHOD_PUT);
    }

    public function deleteProductPart($idproduct, $idproductpart)
    {
        return $this->sendRequest('/products/' . $idproduct . '/parts/' . $idproductpart, [], self::METHOD_DELETE);
    }

    public function getProductLocations($idproduct)
    {
        return $this->sendRequest('/products/' . $idproduct . '/locations');
    }

    public function linkProductToLocation($idproduct, $idlocation)
    {
        $params = array('idlocation' => $idlocation);

        return $this->sendRequest('/products/' . $idproduct . '/locations', $params, self::METHOD_POST);
    }

    public function unlinkProductFromLocation($idproduct, $idlocation)
    {
        return $this->sendRequest('/products/' . $idproduct . '/locations/' . $idlocation, array(), self::METHOD_DELETE);
    }

    /*
     * Stock history
     */
    public function getStockHistories($filters = [])
    {
        return $this->sendRequest('/stockhistory', null, null, $filters);
    }

    public function getStockHistory($idproduct_stock_history)
    {
        return $this->sendRequest('/stockhistory/' . $idproduct_stock_history);
    }

    public function getStockHistoryForProduct($idproduct, $offset = 0)
    {
        return $this->getStockHistories(['idproduct' => $idproduct, 'offset' => $offset]);
    }

    public function getStockHistoryForWarehouse($idwarehouse, $offset = 0)
    {
        return $this->getStockHistories(['idwarehouse' => $idwarehouse, 'offset' => $offset]);
    }

    /*
     * Orders
     */
    public function getOrders($filters = [])
    {
        return $this->sendRequest('/orders', null, null, $filters);
    }

    public function getAllOrders($filters = [])
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
        return $this->sendRequest('/orders', $params, self::METHOD_POST);
    }

    public function cancelOrder($idorder, $params = [])
    {
        return $this->sendRequest('/orders/' . $idorder, $params, self::METHOD_DELETE);
    }

    public function getOrderProductStatus($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/productstatus');
    }

    public function getOrderBackorders($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/backorders');
    }

    /** @deprecated Use `processOrder`, still here for backwards compatibility */
    public function closeOrder($idorder)
    {
        return $this->processOrder($idorder);
    }

    public function processOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/process', null, self::METHOD_POST);
    }

    public function getOrderNotes($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/notes');
    }

    public function addOrderNote($idorder, $note)
    {
        return $this->sendRequest('/orders/' . $idorder . '/notes', ['note' => $note], self::METHOD_POST);
    }

    public function getOrderTags($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags');
    }
    
    public function createTag(string $title, string $color = '#823882', bool $inherit = false)
    {
        $params = [
            'title' => $title,
            'color' => $color,
            'inherit' => $inherit
        ];

        return $this->sendRequest('/tags/', $params, self::METHOD_POST);
    }
    
    public function deleteTag(string $idtag)
    {
        return $this->sendRequest('/tags/'.$idtag, [], self::METHOD_DELETE);
    }
    
    public function addOrderTag($idorder, $idtag)
    {
        $params = ['idtag' => $idtag];

        return $this->sendRequest('/orders/' . $idorder . '/tags', $params, self::METHOD_POST);
    }

    /** @deprecated Use the `delete`, stays here for backwards compatibility */
    public function removeOrderTag($idorder, $idtag)
    {
        $this->deleteOrderTag($idorder, $idtag);
    }

    public function deleteOrderTag($idorder, $idtag)
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    public function updateOrder($idorder, $params)
    {
        return $this->sendRequest('/orders/' . $idorder, $params, self::METHOD_PUT);
    }

    public function getOrderFieldsOnOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields');
    }

    public function updateOrderFieldOnOrder($idorder, $idorderfield, $value)
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields/' . $idorderfield, ['value' => $value], self::METHOD_PUT);
    }

    public function deleteOrderFieldFromOrder($idorder, $idorderfield)
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields/' . $idorderfield, [], self::METHOD_DELETE);
    }

    public function allocateStockForOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/allocate', null, self::METHOD_POST);
    }

    public function deallocateStockForOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/deallocate', null, self::METHOD_POST);
    }

    public function prioritiseOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/prioritise', null, self::METHOD_POST);
    }

    /*
     * Picklists
     */
    public function getPicklists($filters = [])
    {
        return $this->sendRequest('/picklists', null, null, $filters);
    }

    public function getAllPicklists($filters = [])
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

    public function updatePicklist($idpicklist, $params)
    {
        return $this->sendRequest('/picklists/' . $idpicklist, $params, self::METHOD_PUT);
    }

    public function closePicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/close', null, self::METHOD_POST);
    }

    public function cancelPicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/cancel', null, self::METHOD_POST);
    }

    public function pickProductOnPicklist($idpicklist, $product, $amount)
    {
        $params = [
            'product' => $product,
            'amount' => $amount
        ];

        return $this->sendRequest('/picklists/' . $idpicklist . '/pick', $params, self::METHOD_POST);
    }

    public function pickallPicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/pickall', null, self::METHOD_POST);
    }

    public function assignPicklistToUser($idpicklist, $iduser)
    {
        $params = ['iduser' => $iduser];
        return $this->sendRequest('/picklists/' . $idpicklist . '/assign', $params, self::METHOD_POST);
    }

    public function unassignPicklist($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/unassign', null, self::METHOD_POST);
    }

    public function snoozePicklist($idpicklist, $snooze_until = null)
    {
        $params = ['snooze_until' => $snooze_until];
        return $this->sendRequest('/picklists/' . $idpicklist . '/snooze', $params, self::METHOD_POST);
    }

    public function createShipment($idpicklist, $params)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments', $params, self::METHOD_POST);
    }

    public function getShipments($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments');
    }

    public function getPicklistPdf($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/picklistpdf');
    }

    public function getPackinglistPdf($idpicklist)
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/packinglistpdf');
    }

    /*
     * Picklist batches
     */
    public function getPicklistBatches($filters = [])
    {
        return $this->sendRequest('/picklists/batches', null, null, $filters);
    }

    public function getAllPicklistBatches($filters = [])
    {
        return $this->getAllResults('picklistBatch', $filters);
    }

    public function getPicklistBatch($idpicklist_batch)
    {
        return $this->sendRequest('/picklists/batches/' . $idpicklist_batch);
    }

    /*
     * Suppliers
     */
    public function getSuppliers($filters = [])
    {
        return $this->sendRequest('/suppliers', null, null, $filters);
    }

    public function getAllSuppliers($filters = [])
    {
        return $this->getAllResults('supplier', $filters);
    }

    public function getSupplier($idsupplier)
    {
        return $this->sendRequest('/suppliers/' . $idsupplier);
    }

    public function addSupplier($params)
    {
        return $this->sendRequest('/suppliers', $params, self::METHOD_POST);
    }

    public function updateSupplier($idsupplier, $params)
    {
        return $this->sendRequest('/suppliers/' . $idsupplier, $params, self::METHOD_PUT);
    }

    /*
     * Purchase orders
     */
    public function addPurchaseorder($params)
    {
        return $this->sendRequest('/purchaseorders', $params, self::METHOD_POST);
    }

    public function getPurchaseorders($filters = [])
    {
        return $this->sendRequest('/purchaseorders', null, null, $filters);
    }

    public function getPurchaseorder($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder);
    }

    public function markPurchaseorderAsPurchased($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/mark-as-purchased', null, self::METHOD_POST);
    }

    public function cancelPurchaseorder($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/cancel', null, self::METHOD_POST);
    }

    public function getPurchaseorderProducts($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products');
    }

    public function addProductToPurchaseorder($idpurchaseorder, $idproduct, $amount, $price = null, $deliverydate = null)
    {
        $params = [
            'idproduct' => $idproduct,
            'amount' => $amount
        ];

        if (! is_null($price)) {
            $params['price'] = $price;
        }

        if (! is_null($deliverydate)) {
            $params['delivery_date'] = $deliverydate;
        }

        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products', $params, self::METHOD_POST);
    }

    public function updatePurchaseorderProduct($idpurchaseorder, $idpurchaseorder_product, $params)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products/' . $idpurchaseorder_product, $params, self::METHOD_PUT);
    }

    public function removePurchaseorderProduct($idpurchaseorder, $idpurchaseorder_product)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products/' . $idpurchaseorder_product, [], self::METHOD_DELETE);
    }

    public function getReceiptsFromPurchaseorder($idpurchaseorder)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts');
    }

    public function getReceiptFromPurchaseorder($idpurchaseorder, $idreceipt)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts/' . $idreceipt);
    }

    public function addReceiptToPurchaseorder($idpurchaseorder, $params)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts', $params, self::METHOD_POST);
    }

    public function updatePurchaseorder($idpurchaseorder, $params)
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder, $params, self::METHOD_PUT);
    }

    /*
     * Returns
     */

    public function getReturns($filters = [])
    {
        return $this->sendRequest('/returns', null, null, $filters);
    }

    public function getAllReturns($filters = [])
    {
        return $this->getAllResults('return', $filters);
    }

    public function getReturn($idreturn)
    {
        return $this->sendRequest('/returns/' . $idreturn);
    }

    public function addReturn($params)
    {
        return $this->sendRequest('/returns', $params, self::METHOD_POST);
    }

    public function updateReturn($idreturn, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn, $params, self::METHOD_PUT);
    }

    public function getReturnLogAndComments($idreturn)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/logs');
    }

    public function addReturnLogOrChangeStatus($idreturn, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/logs', $params, self::METHOD_POST);

    }
    
    public function getReturnStatuses()
    {
        return $this->sendRequest('/return_statuses');
    }

    public function getAllReturnStatuses($filters = [])
    {
        return $this->getAllResults('returnStatuse');
    }

    public function getReturnReasons()
    {
        return $this->sendRequest('/return_reasons');
    }

    public function getAllReturnReasons($filters = [])
    {
        return $this->getAllResults('returnReason');
    }

    /*
     * Returned Products
     */

    public function getReturnedProducts($idreturn)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products');
    }

    public function addReturnedProducts($idreturn, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products', $params, self::METHOD_POST);
    }

    public function updateReturnedProduct($idreturn, $idreturn_product, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products/' . $idreturn_product, $params, self::METHOD_PUT);
    }

    public function removeReturnedProduct($idreturn, $idreturn_product)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products/' . $idreturn_product, null,self::METHOD_DELETE);
    }

    public function receiveReturnedProducts($idreturn, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/receive', $params, self::METHOD_POST);
    }

    /*
     * Replacement Products
     */

    public function getReplacementProducts($idreturn)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products');
    }

    public function addReplacementProducts($idreturn, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products', $params, self::METHOD_POST);
    }

    public function updateReplacementProduct($idreturn, $idreturn_product_replacement, $params)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products/' . $idreturn_product_replacement, $params, self::METHOD_PUT);
    }

    public function removeReplacementProduct($idreturn, $idreturn_product_replacement)
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products/' . $idreturn_product_replacement, null,self::METHOD_DELETE);
    }

    /*
     * Tags
     */
    public function getTags($filters = [])
    {
        return $this->sendRequest('/tags', [], self::METHOD_GET, $filters);
    }

    public function getAllTags($filters = [])
    {
        return $this->getAllResults('tag', $filters);
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
     * Hooks
     */
    public function getHooks()
    {
        return $this->sendRequest('/hooks');
    }

    public function getHook($id)
    {
        return $this->sendRequest('/hooks/' . $id);
    }

    public function addHook($params)
    {
        return $this->sendRequest('/hooks', $params, self::METHOD_POST);
    }

    public function deleteHook($id)
    {
        return $this->sendRequest('/hooks/' . $id, [], self::METHOD_DELETE);
    }

    /*
     * Backorders
     */
    public function getBackorders($filters = [])
    {
        return $this->sendRequest('/backorders', null, null, $filters);
    }

    public function getBackorder($idbackorder)
    {
        return $this->sendRequest('/backorders/' . $idbackorder);
    }

    public function processBackorders()
    {
        return $this->sendRequest('/backorders/process', null, self::METHOD_POST);
    }

    public function deleteBackorder($idbackorder)
    {
        return $this->sendRequest('/backorders/' . $idbackorder, null, self::METHOD_DELETE);
    }

    public function getBackordersForOrder($idorder)
    {
        return $this->sendRequest('/orders/' . $idorder . '/backorders');
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
    public function getShippingProviders()
    {
        return $this->sendRequest('/shippingproviders');
    }

    /*
     * Product fields
     */
    public function getProductFields()
    {
        return $this->sendRequest('/productfields');
    }

    public function getProductField($idproductfield)
    {
        return $this->sendRequest('/productfields/' . $idproductfield);
    }

    /*
     * Order fields
     */
    public function getOrderFields()
    {
        return $this->sendRequest('/orderfields');
    }

    public function getOrderField($idorderfield)
    {
        return $this->sendRequest('/orderfields/' . $idorderfield);
    }

    /*
     * Customer fields
     */
    public function getCustomerFields()
    {
        return $this->sendRequest('/customerfields');
    }

    public function getCustomerField($idcustomerfield)
    {
        return $this->sendRequest('/customerfields/' . $idcustomerfield);
    }

    /*
     * Users
     */
    public function getUsers()
    {
        return $this->sendRequest('/users');
    }

    public function getUser($iduser)
    {
        return $this->sendRequest('/users/' . $iduser);
    }

    public function getCurrentUser()
    {
        return $this->getUser('me');
    }

    /*
     * Templates
     */
    public function getTemplates()
    {
        return $this->sendRequest('/templates');
    }

    /*
     * Locations
     */
    public function getLocations($filters = [])
    {
        return $this->sendRequest('/locations', [], self::METHOD_GET, $filters);
    }

    public function getAllLocations($filters = array())
    {
        return $this->getAllResults('location', $filters);
    }

    public function getLocation($id)
    {
        return $this->sendRequest('/locations/' . $id);
    }

    public function addLocation($params, $autoLinkToParent = false)
    {
        return $this->sendRequest('/locations', $params, self::METHOD_POST, ['auto_link_to_parent' => $autoLinkToParent]);
    }

    public function updateLocation($id, $params)
    {
        return $this->sendRequest('/locations/' . $id, $params, self::METHOD_PUT);
    }

    public function deleteLocation($id)
    {
        return $this->sendRequest('/locations/' . $id, [], self::METHOD_DELETE);
    }

    public function getProductsOnLocation($id)
    {
        return $this->sendRequest('/locations/' . $id . '/products', [], self::METHOD_GET);
    }

    public function getAllProductsOnLocation($id)
    {
        return $this->getAllResults('/locations/' . $id . '/products');
    }

    /*
     * Stats
     */
    public function getStats()
    {
        return $this->sendRequest('/stats');
    }

    public function getStat($key)
    {
        return $this->sendRequest('/stats/' . $key);
    }

    /*
     * General
     */
    public function getAllResults(string $entity, array $filters = []): array
    {
        $gotAll = false;
        $collection = [];

        if (str_starts_with($entity, '/')) {
            // If '/products' is the entity, use that endpoint directly
            $mode = 'byEndpoint';
            $baseEndpoint = $entity;
        } else {
            // If 'product' is the entity, use the getProducts() method
            $mode = 'byMethod';
            $methodName = $this->getEntityGetterMethodName($entity);
        }

        $i = 0;
        while ($gotAll == false) {
            $filters['offset'] = ($i * 100);

            if ($mode === 'byEndpoint') {
                $result = $this->sendRequest($baseEndpoint, null, null, $filters);
            } else {
                $result = $this->$methodName($filters);
            }

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

        return ['success' => true, 'data' => $collection];
    }

    /**
     * Yield all results from the API
     * @throws Exception
     */
    public function getResultGenerator(string $entity, $filters = [])
    {
        $methodName = $this->getEntityGetterMethodName($entity);

        $i = 0;
        $gotAll = false;
        while ($gotAll === false) {
            $filters['offset'] = ($i * 100);

            $result = $this->$methodName($filters);
            if (! isset($result['success']) || ! isset($result['data']) || $result['success'] !== true) {
                throw new Exception("Invalid API response: " . json_encode($result));
            }

            if (count($result['data']) < 100) {
                $gotAll = true;
            }

            foreach ($result['data'] as $item) {
                yield $item;
            }

            $i++;
        }
    }

    /**
     * Creates a new company account for Picqer
     * @throws RateLimitException
     */
    public function addCompany($params)
    {
        return $this->sendRequest('/companies', $params, self::METHOD_POST);
    }

    /**
     * Enable debug mode gives verbose output on requests and responses
     */
    public function enableDebugmode(): void
    {
        $this->debug = true;
    }

    /**
     * Disable Curl's SSL verification for testing
     */
    public function disableSslVerification(): void
    {
        $this->skipSslVerification = true;
    }

    public function setApihost(string $apiHost): void
    {
        $this->apiHost = $apiHost;
    }

    /**
     * @param string $protocol http or https
     */
    public function setProtocol(string $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function setUseragent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Change the timeout for CURL requests
     */
    public function setTimeoutInSeconds(int $timeoutInSeconds): void
    {
        $this->timeoutInSeconds = $timeoutInSeconds;
    }

    public function enableRetryOnRateLimitHit(): void
    {
        $this->waitOnRateLimit = true;
    }

    /**
     * @throws RateLimitException
     */
    public function sendRequest($endpoint, $params = [], $method = self::METHOD_GET, $filters = [])
    {
        $endpoint = $this->getEndpoint($endpoint, $filters);

        $this->debug('URL: ' . $this->getUrl($endpoint));

        $curlSession = curl_init();

        curl_setopt($curlSession, CURLOPT_HEADER, false);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curlSession, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($curlSession, CURLOPT_TIMEOUT, $this->timeoutInSeconds);

        curl_setopt($curlSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curlSession, CURLOPT_USERPWD, $this->apiKey . ':' . $this->password);

        curl_setopt($curlSession, CURLOPT_USERAGENT, $this->userAgent . ' ' . $this->clientVersion);
        curl_setopt($curlSession, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($curlSession, CURLOPT_HEADERFUNCTION, function ($curl, $header) {
            $this->rawResponseHeaders[] = $header;
            return strlen($header);
        });

        $this->setPostData($curlSession, $method, $params);
        $this->setSslVerification($curlSession);

        $apiResult = curl_exec($curlSession);
        $headerInfo = curl_getinfo($curlSession);

        $this->debug('Raw result: ' . $apiResult);

        $apiResultJson = json_decode($apiResult, true);
        $apiResultHeaders = $this->parseRawHeaders();
        $this->resetRawHeaders();

        $result = [];
        $result['success'] = false;
        $result['rate-limit-remaining'] = $this->getRemainingRateLimit($apiResultHeaders);

        // CURL failed
        if ($apiResult === false) {
            $result['error'] = true;
            $result['errorcode'] = 0;
            $result['errormessage'] = curl_error($curlSession);
            curl_close($curlSession);
            return $result;
        }

        curl_close($curlSession);

        if ($headerInfo['http_code'] == '429') {
            return $this->handleRateLimitReached($endpoint, $params, $method, $filters);
        }

        // API returns error
        if (! in_array($headerInfo['http_code'], ['200', '201', '204'])) {
            $result['error'] = true;
            $result['errorcode'] = $headerInfo['http_code'];
            $result['errormessage'] = $apiResult;

            return $result;
        }

        // API returns success
        $result['success'] = true;
        $result['data'] = (($apiResultJson === null) ? $apiResult : $apiResultJson);

        return $result;
    }

    protected function getUrl(string $endpoint): string
    {
        return $this->protocol . '://' . $this->company . '.' . $this->apiHost . $this->apiLocation . '/' . $this->apiVersion . $endpoint;
    }

    protected function prepareData($params)
    {
        return json_encode($params);
    }

    protected function getEndpoint(string $endpoint, ?array $filters): string
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

    protected function debug($message): void
    {
        if ($this->debug) {
            echo 'Debug: ' . $message . PHP_EOL;
        }
    }

    protected function parseRawHeaders(): array
    {
        $parsedHeaders = [];

        foreach ($this->rawResponseHeaders as $header) {
            $headerPieces = explode(':', $header, 2);

            if (! isset($headerPieces[0]) || ! isset($headerPieces[1])) {
                continue;
            }

            $parsedHeaders[strtolower($headerPieces[0])] = trim($headerPieces[1]);
        }

        return $parsedHeaders;
    }

    protected function resetRawHeaders(): void
    {
        $this->rawResponseHeaders = [];
    }

    protected function getRemainingRateLimit(array $apiResultHeaders)
    {
        return (array_key_exists('x-ratelimit-remaining', $apiResultHeaders)) ? $apiResultHeaders['x-ratelimit-remaining'] : null;
    }

    protected function setPostData($curlSession, $method, $params): void
    {
        if (! in_array($method, [self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE])) {
            return;
        }

        $data = $this->prepareData($params);
        $this->debug('Data: ' . $data);

        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
    }

    protected function setSslVerification($curlSession): void
    {
        if ($this->skipSslVerification) {
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, false);
        }
    }

    /**
     * @throws RateLimitException
     */
    protected function handleRateLimitReached($endpoint, $params = [], $method = self::METHOD_GET, $filters = [])
    {
        if (! $this->waitOnRateLimit) {
            throw new RateLimitException('Rate limit exceeded. Try again later.');
        }

        $this->debug('Rate limit hit, sleeping and trying again');

        sleep($this->sleepTimeOnRateLimitHitInSeconds);

        return $this->sendRequest($endpoint, $params, $method, $filters);
    }

    private function getEntityGetterMethodName(string $entity): string
    {
        if (array_key_exists($entity, $this->entityBaseMethodNameMap)) {
            return $this->entityBaseMethodNameMap[$entity];
        }

        return 'get' . ucfirst($entity) . 's';
    }
}
