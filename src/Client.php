<?php

namespace Picqer\Api;

use CurlHandle;

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

    const string METHOD_GET = 'GET';
    const string METHOD_POST = 'POST';
    const string METHOD_PUT = 'PUT';
    const string METHOD_DELETE = 'DELETE';

    protected array $rawResponseHeaders = [];

    public function __construct(string $company, string $apikey = '', string $password = 'X')
    {
        $this->company = $company;
        $this->apiKey = $apikey;
        $this->password = $password; // Only needed with legacy integrations
    }

    /*
     * Movements
     */
    public function getMovements(array $filters = []): array
    {
        return $this->sendRequest('/movements', [], self::METHOD_GET, $filters);
    }

    public function getMovement(int $idmovement): array
    {
        return $this->sendRequest('/movements/' . $idmovement);
    }

    /*
     * Customers
     */
    public function getCustomers(array $filters = []): array
    {
        return $this->sendRequest('/customers', [], self::METHOD_GET, $filters);
    }

    public function getAllCustomers(array $filters = []): array
    {
        return $this->getAllResults('customer', $filters);
    }

    public function getCustomer(int $idcustomer): array
    {
        return $this->sendRequest('/customers/' . $idcustomer);
    }

    public function getCustomerByCustomerid(int $customerid): array
    {
        $result = $this->sendRequest('/customers?customerid=' . urlencode($customerid));
        if (is_array($result['data']) && count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        } else {
            $result['data'] = null;
        }
        return $result;
    }

    public function addCustomer(array $params): array
    {
        return $this->sendRequest('/customers', $params, self::METHOD_POST);
    }

    /** @deprecated Use the `update`, stays here for backwards compatibility */
    public function editCustomer(int $idcustomer, array $params): void
    {
        $this->updateCustomer($idcustomer, $params);
    }

    public function updateCustomer(int $idcustomer, array $params): array
    {
        return $this->sendRequest('/customers/' . $idcustomer, $params, self::METHOD_PUT);
    }

    public function getCustomerAddress(int $idcustomer): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses');
    }

    public function addCustomerAddress(int $idcustomer, array $params): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses', $params, self::METHOD_POST);
    }

    public function updateCustomerAddress(int $idcustomer, int $idaddress, array $params): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, $params, self::METHOD_PUT);
    }

    public function deleteCustomerAddress(int $idcustomer, int $idaddress): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/addresses/' . $idaddress, [], self::METHOD_DELETE);
    }

    public function getCustomerTags(int $idcustomer): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags');
    }

    public function addCustomerTag(int $idcustomer, int $idtag): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags', ['idtag' => $idtag], self::METHOD_POST);
    }

    public function deleteCustomerTag(int $idcustomer, int $idtag): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    public function addCustomerComment(int $idcustomer, array $params): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/comments', $params, self::METHOD_POST);
    }

    public function getCustomerComments(int $idcustomer): array
    {
        return $this->sendRequest('/customers/' . $idcustomer . '/comments');
    }

    /**
     * Fulfilment customers
     */
    public function getFulfilmentCustomers(array $filters = []): array
    {
        return $this->sendRequest('/fulfilment/customers', [], self::METHOD_GET, $filters);
    }

    public function getAllFulfilmentCustomers(array $filters = []): array
    {
        return $this->getAllResults('fulfilmentCustomer', $filters);
    }

    public function getFulfilmentCustomer(int $idfulfilmentcustomer): array
    {
        return $this->sendRequest('/fulfilment/customers/' . $idfulfilmentcustomer);
    }

    public function addFulfilmentCustomer(array $params): array
    {
        return $this->sendRequest('/fulfilment/customers', $params, self::METHOD_POST);
    }

    public function updateFulfilmentCustomer(int $idfulfilmentcustomer, array $params): array
    {
        return $this->sendRequest('/fulfilment/customers/' . $idfulfilmentcustomer, $params, self::METHOD_PUT);
    }

    /*
     * Products
     */
    public function getProducts(array $filters = []): array
    {
        return $this->sendRequest('/products', [], self::METHOD_GET, $filters);
    }

    public function getAllProducts(array $filters = []): array
    {
        return $this->getAllResults('product', $filters);
    }

    public function getProduct(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct);
    }

    public function getProductByProductcode(string $productcode): array
    {
        $result = $this->sendRequest('/products?productcode=' . urlencode($productcode));

        if (is_array($result['data']) && count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        } else {
            $result['data'] = null;
        }

        return $result;
    }

    public function addProduct(array $params): array
    {
        return $this->sendRequest('/products', $params, self::METHOD_POST);
    }

    public function inactivateProduct(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/inactivate', [], self::METHOD_POST);
    }

    public function activateProduct(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/activate', [], self::METHOD_POST);
    }

    public function getProductStock(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock');
    }

    public function getProductStockForWarehouse(int $idproduct, int $idwarehouse): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse);
    }

    public function updateProductStockForWarehouse(int $idproduct, int $idwarehouse, array $params): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/stock/' . $idwarehouse, $params, self::METHOD_POST);
    }

    public function getProductWarehouseSettings(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses');
    }

    public function updateProductWarehouseSetting(int $idproduct, int $idwarehouse, array $params): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/warehouses/' . $idwarehouse, $params, self::METHOD_PUT);
    }

    public function getProductImages(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/images');
    }

    public function addImageToProduct(int $idproduct, string $base64Image): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/images', ['image' => $base64Image], self::METHOD_POST);
    }

    public function deleteImageFromProduct(int $idproduct, int $idproduct_image): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/images/' . $idproduct_image, [], self::METHOD_DELETE);
    }

    public function updateProduct(int $idproduct, array $params): array
    {
        return $this->sendRequest('/products/' . $idproduct, $params, self::METHOD_PUT);
    }

    public function getProductTags(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags');
    }

    public function addProductTag(int $idproduct, int $idtag): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags', ['idtag' => $idtag], self::METHOD_POST);
    }

    public function deleteProductTag(int $idproduct, int $idtag): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    public function getProductParts(int $idproduct): array
    {
        return $this->sendRequest('/products/'.$idproduct.'/parts');
    }

    public function getProductPart(int $idproduct, int $idproduct_part): array
    {
        return $this->sendRequest('/products/'.$idproduct.'/parts/'.$idproduct_part);
    }

    public function addProductPart(int $idproduct, array $params): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/parts', $params, self::METHOD_POST);
    }

    public function updateProductPartAmount(int $idproduct, int $idproductpart, int $amount): array
    {
        $params = ['amount' => $amount];

        return $this->sendRequest('/products/' . $idproduct . '/parts/'.$idproductpart, $params, self::METHOD_PUT);
    }

    public function deleteProductPart(int $idproduct, int $idproductpart): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/parts/' . $idproductpart, [], self::METHOD_DELETE);
    }

    public function getProductLocations(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/locations');
    }

    public function linkProductToLocation(int $idproduct, int $idlocation): array
    {
        $params = array('idlocation' => $idlocation);

        return $this->sendRequest('/products/' . $idproduct . '/locations', $params, self::METHOD_POST);
    }

    public function unlinkProductFromLocation(int $idproduct, int $idlocation): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/locations/' . $idlocation, array(), self::METHOD_DELETE);
    }

    public function addProductComment(int $idproduct, array $params): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/comments', $params, self::METHOD_POST);
    }

    public function getProductComments(int $idproduct): array
    {
        return $this->sendRequest('/products/' . $idproduct . '/comments');
    }

    /*
     * Stock history
     */
    public function getStockHistories(array $filters = []): array
    {
        return $this->sendRequest('/stockhistory', [], self::METHOD_GET, $filters);
    }

    public function getStockHistory(int $idproduct_stock_history): array
    {
        return $this->sendRequest('/stockhistory/' . $idproduct_stock_history);
    }

    public function getStockHistoryForProduct(int $idproduct, int $offset = 0): array
    {
        return $this->getStockHistories(['idproduct' => $idproduct, 'offset' => $offset]);
    }

    public function getStockHistoryForWarehouse(int $idwarehouse, int $offset = 0): array
    {
        return $this->getStockHistories(['idwarehouse' => $idwarehouse, 'offset' => $offset]);
    }

    /*
     * Orders
     */
    public function getOrders(array $filters = []): array
    {
        return $this->sendRequest('/orders', [], self::METHOD_GET, $filters);
    }

    public function getAllOrders(array $filters = []): array
    {
        return $this->getAllResults('order', $filters);
    }

    public function getOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder);
    }

    public function getOrderByOrderid(int $orderid): array
    {
        $result = $this->sendRequest('/orders?orderid=' . urlencode($orderid));

        if (count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        }

        return $result;
    }

    public function addOrder(array $params): array
    {
        return $this->sendRequest('/orders', $params, self::METHOD_POST);
    }

    public function cancelOrder(int $idorder, array $params = []): array
    {
        return $this->sendRequest('/orders/' . $idorder, $params, self::METHOD_DELETE);
    }

    public function getOrderProductStatus(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/productstatus');
    }

    public function getOrderBackorders(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/backorders');
    }

    /** @deprecated Use `processOrder`, still here for backwards compatibility */
    public function closeOrder(int $idorder): array
    {
        return $this->processOrder($idorder);
    }

    public function processOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/process', [], self::METHOD_POST);
    }

    public function processOrderBackorders(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/process-backorders', [], self::METHOD_POST);
    }

    public function getOrderNotes(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/notes');
    }

    public function addOrderNote(int $idorder, string $note): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/notes', ['note' => $note], self::METHOD_POST);
    }

    public function addOrderComment(int $idorder, array $params): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/comments', $params, self::METHOD_POST);
    }

    public function getOrderComments(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/comments');
    }

    public function getOrderTags(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags');
    }

    public function createTag(string $title, string $color = '#823882', bool $inherit = false): array
    {
        $params = [
            'title' => $title,
            'color' => $color,
            'inherit' => $inherit
        ];

        return $this->sendRequest('/tags/', $params, self::METHOD_POST);
    }

    public function deleteTag(string $idtag): array
    {
        return $this->sendRequest('/tags/'.$idtag, [], self::METHOD_DELETE);
    }

    public function addOrderTag(int $idorder, int $idtag): array
    {
        $params = ['idtag' => $idtag];

        return $this->sendRequest('/orders/' . $idorder . '/tags', $params, self::METHOD_POST);
    }

    /** @deprecated Use the `delete`, stays here for backwards compatibility */
    public function removeOrderTag(int $idorder, int $idtag): void
    {
        $this->deleteOrderTag($idorder, $idtag);
    }

    public function deleteOrderTag(int $idorder, int $idtag): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/tags/' . $idtag, [], self::METHOD_DELETE);
    }

    public function updateOrder(int $idorder, array $params): array
    {
        return $this->sendRequest('/orders/' . $idorder, $params, self::METHOD_PUT);
    }

    public function getOrderFieldsOnOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields');
    }

    public function updateOrderFieldOnOrder(int $idorder, int $idorderfield, mixed $value): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields/' . $idorderfield, ['value' => $value], self::METHOD_PUT);
    }

    public function deleteOrderFieldFromOrder(int $idorder, int $idorderfield): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/orderfields/' . $idorderfield, [], self::METHOD_DELETE);
    }

    public function allocateStockForOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/allocate', [], self::METHOD_POST);
    }

    public function deallocateStockForOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/deallocate', [], self::METHOD_POST);
    }

    public function prioritiseOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/prioritise', [], self::METHOD_POST);
    }

    /*
     * Picklists
     */
    public function getPicklists(array $filters = []): array
    {
        return $this->sendRequest('/picklists', [], self::METHOD_GET, $filters);
    }

    public function getAllPicklists(array $filters = []): array
    {
        return $this->getAllResults('picklist', $filters);
    }

    public function getPicklist(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist);
    }

    public function getPicklistByPicklistid(int $picklistid): array
    {
        $result = $this->sendRequest('/picklists?picklistid=' . urlencode($picklistid));

        if (count($result['data']) == 1) {
            $result['data'] = $result['data'][0];
        }

        return $result;
    }

    public function updatePicklist(int $idpicklist, array $params): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist, $params, self::METHOD_PUT);
    }

    public function closePicklist(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/close', [], self::METHOD_POST);
    }

    public function cancelPicklist(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/cancel', [], self::METHOD_POST);
    }

    public function pickProductOnPicklist(int $idpicklist, string $product, int $amount): array
    {
        $params = [
            'product' => $product,
            'amount' => $amount
        ];

        return $this->sendRequest('/picklists/' . $idpicklist . '/pick', $params, self::METHOD_POST);
    }

    public function pickallPicklist(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/pickall', [], self::METHOD_POST);
    }

    public function assignPicklistToUser(int $idpicklist, int $iduser): array
    {
        $params = ['iduser' => $iduser];
        return $this->sendRequest('/picklists/' . $idpicklist . '/assign', $params, self::METHOD_POST);
    }

    public function unassignPicklist(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/unassign', [], self::METHOD_POST);
    }

    public function snoozePicklist(int $idpicklist, ?string $snooze_until = null): array
    {
        $params = ['snooze_until' => $snooze_until];
        return $this->sendRequest('/picklists/' . $idpicklist . '/snooze', $params, self::METHOD_POST);
    }

    public function createShipment(int $idpicklist, array $params): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments', $params, self::METHOD_POST);
    }

    public function getShipments(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/shipments');
    }

    public function getPicklistPdf(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/picklistpdf');
    }

    public function getPackinglistPdf(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/packinglistpdf');
    }

    public function addPicklistComment(int $idpicklist, array $params): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/comments', $params, self::METHOD_POST);
    }

    public function getPicklistComments(int $idpicklist): array
    {
        return $this->sendRequest('/picklists/' . $idpicklist . '/comments');
    }

    /*
     * Picklist batches
     */
    public function getPicklistBatches(array $filters = []): array
    {
        return $this->sendRequest('/picklists/batches', [], self::METHOD_GET, $filters);
    }

    public function getAllPicklistBatches(array $filters = []): array
    {
        return $this->getAllResults('picklistBatch', $filters);
    }

    public function getPicklistBatch(int $idpicklist_batch): array
    {
        return $this->sendRequest('/picklists/batches/' . $idpicklist_batch);
    }

    /*
     * Suppliers
     */
    public function getSuppliers(array $filters = []): array
    {
        return $this->sendRequest('/suppliers', [], self::METHOD_GET, $filters);
    }

    public function getAllSuppliers(array $filters = []): array
    {
        return $this->getAllResults('supplier', $filters);
    }

    public function getSupplier(int $idsupplier): array
    {
        return $this->sendRequest('/suppliers/' . $idsupplier);
    }

    public function addSupplier(array $params): array
    {
        return $this->sendRequest('/suppliers', $params, self::METHOD_POST);
    }

    public function updateSupplier(int $idsupplier, array $params): array
    {
        return $this->sendRequest('/suppliers/' . $idsupplier, $params, self::METHOD_PUT);
    }

    public function addSupplierComment(int $idsupplier, array $params): array
    {
        return $this->sendRequest('/suppliers/' . $idsupplier . '/comments', $params, self::METHOD_POST);
    }

    public function getSupplierComments(int $idsupplier): array
    {
        return $this->sendRequest('/suppliers/' . $idsupplier . '/comments');
    }

    /*
     * Purchase orders
     */
    public function addPurchaseorder(array $params): array
    {
        return $this->sendRequest('/purchaseorders', $params, self::METHOD_POST);
    }

    public function getPurchaseorders(array $filters = []): array
    {
        return $this->sendRequest('/purchaseorders', [], self::METHOD_GET, $filters);
    }

    public function getPurchaseorder(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder);
    }

    public function markPurchaseorderAsPurchased(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/mark-as-purchased', [], self::METHOD_POST);
    }

    public function cancelPurchaseorder(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/cancel', [], self::METHOD_POST);
    }

    public function getPurchaseorderProducts(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products');
    }

    public function addProductToPurchaseorder(int $idpurchaseorder, int $idproduct, int $amount, ?float $price = null, ?string $deliverydate = null): array
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

    public function updatePurchaseorderProduct(int $idpurchaseorder, int $idpurchaseorder_product, array $params): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products/' . $idpurchaseorder_product, $params, self::METHOD_PUT);
    }

    public function removePurchaseorderProduct(int $idpurchaseorder, int $idpurchaseorder_product): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/products/' . $idpurchaseorder_product, [], self::METHOD_DELETE);
    }

    public function getReceiptsFromPurchaseorder(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts');
    }

    public function getReceiptFromPurchaseorder(int $idpurchaseorder, int $idreceipt): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts/' . $idreceipt);
    }

    public function addReceiptToPurchaseorder(int $idpurchaseorder, array $params): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/receipts', $params, self::METHOD_POST);
    }

    public function updatePurchaseorder(int $idpurchaseorder, array $params): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder, $params, self::METHOD_PUT);
    }

    public function addPurchaseorderComment(int $idpurchaseorder, array $params): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/comments', $params, self::METHOD_POST);
    }

    public function getPurchaseorderComments(int $idpurchaseorder): array
    {
        return $this->sendRequest('/purchaseorders/' . $idpurchaseorder . '/comments');
    }

    /**
     * New receipt endpoints have not added yet, so added it below purchase orders for now
     */

    public function addReceiptComment(int $idreceipt, array $params): array
    {
        return $this->sendRequest('/receipts/' . $idreceipt . '/comments', $params, self::METHOD_POST);
    }

    public function getReceiptComments(int $idreceipt): array
    {
        return $this->sendRequest('/receipts/' . $idreceipt . '/comments');
    }

    /*
     * Returns
     */

    public function getReturns(array $filters = []): array
    {
        return $this->sendRequest('/returns', [], self::METHOD_GET, $filters);
    }

    public function getAllReturns(array $filters = []): array
    {
        return $this->getAllResults('return', $filters);
    }

    public function getReturn(int $idreturn): array
    {
        return $this->sendRequest('/returns/' . $idreturn);
    }

    public function addReturn(array $params): array
    {
        return $this->sendRequest('/returns', $params, self::METHOD_POST);
    }

    public function updateReturn(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn, $params, self::METHOD_PUT);
    }

    public function getReturnLogAndComments(int $idreturn): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/logs');
    }

    public function addReturnLogOrChangeStatus(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/logs', $params, self::METHOD_POST);

    }

    public function getReturnStatuses(): array
    {
        return $this->sendRequest('/return_statuses');
    }

    public function getAllReturnStatuses(array $filters = []): array
    {
        return $this->getAllResults('returnStatuse');
    }

    public function getReturnReasons(): array
    {
        return $this->sendRequest('/return_reasons');
    }

    public function getAllReturnReasons(array $filters = []): array
    {
        return $this->getAllResults('returnReason');
    }

    public function addReturnComment(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/comments', $params, self::METHOD_POST);
    }

    public function getReturnComments(int $idreturn): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/comments');
    }

    /*
     * Returned Products
     */

    public function getReturnedProducts(int $idreturn): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products');
    }

    public function addReturnedProducts(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products', $params, self::METHOD_POST);
    }

    public function updateReturnedProduct(int $idreturn, int $idreturn_product, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products/' . $idreturn_product, $params, self::METHOD_PUT);
    }

    public function removeReturnedProduct(int $idreturn, int $idreturn_product): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/returned_products/' . $idreturn_product, [], self::METHOD_DELETE);
    }

    public function receiveReturnedProducts(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/receive', $params, self::METHOD_POST);
    }

    /*
     * Replacement Products
     */

    public function getReplacementProducts(int $idreturn): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products');
    }

    public function addReplacementProducts(int $idreturn, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products', $params, self::METHOD_POST);
    }

    public function updateReplacementProduct(int $idreturn, int $idreturn_product_replacement, array $params): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products/' . $idreturn_product_replacement, $params, self::METHOD_PUT);
    }

    public function removeReplacementProduct(int $idreturn, int $idreturn_product_replacement): array
    {
        return $this->sendRequest('/returns/' . $idreturn . '/replacement_products/' . $idreturn_product_replacement, [], self::METHOD_DELETE);
    }

    /*
     * Tags
     */
    public function getTags(array $filters = []): array
    {
        return $this->sendRequest('/tags', [], self::METHOD_GET, $filters);
    }

    public function getAllTags(array $filters = []): array
    {
        return $this->getAllResults('tag', $filters);
    }

    public function getTag(int $idtag): array
    {
        return $this->sendRequest('/tags/' . $idtag);
    }

    /*
     * VAT Groups
     */
    public function getVatgroups(): array
    {
        return $this->sendRequest('/vatgroups');
    }

    public function getVatgroup(int $idvatgroup): array
    {
        return $this->sendRequest('/vatgroups/' . $idvatgroup);
    }

    /*
     * Hooks
     */
    public function getHooks(): array
    {
        return $this->sendRequest('/hooks');
    }

    public function getHook(int $id): array
    {
        return $this->sendRequest('/hooks/' . $id);
    }

    public function addHook(array $params): array
    {
        return $this->sendRequest('/hooks', $params, self::METHOD_POST);
    }

    public function deleteHook(int $id): array
    {
        return $this->sendRequest('/hooks/' . $id, [], self::METHOD_DELETE);
    }

    /*
     * Backorders
     */
    public function getBackorders(array $filters = []): array
    {
        return $this->sendRequest('/backorders', [], self::METHOD_GET, $filters);
    }

    public function getBackorder(int $idbackorder): array
    {
        return $this->sendRequest('/backorders/' . $idbackorder);
    }

    public function processBackorders(): array
    {
        return $this->sendRequest('/backorders/process', [], self::METHOD_POST);
    }

    public function deleteBackorder(int $idbackorder): array
    {
        return $this->sendRequest('/backorders/' . $idbackorder, [], self::METHOD_DELETE);
    }

    public function getBackordersForOrder(int $idorder): array
    {
        return $this->sendRequest('/orders/' . $idorder . '/backorders');
    }

    /*
     * Warehouses
     */
    public function getWarehouses(): array
    {
        return $this->sendRequest('/warehouses');
    }

    public function getWarehouse(int $idwarehouse): array
    {
        return $this->sendRequest('/warehouses/' . $idwarehouse);
    }

    /*
     * Pricelists
     */
    public function getPricelists(): array
    {
        return $this->sendRequest('/pricelists');
    }

    public function getPricelist(int $idpricelist): array
    {
        return $this->sendRequest('/pricelists/' . $idpricelist);
    }

    /*
     * Shipping providers
     */
    public function getShippingProviders(): array
    {
        return $this->sendRequest('/shippingproviders');
    }

    /*
     * Product fields
     */
    public function getProductFields(): array
    {
        return $this->sendRequest('/productfields');
    }

    public function getProductField(int $idproductfield): array
    {
        return $this->sendRequest('/productfields/' . $idproductfield);
    }

    /*
     * Order fields
     */
    public function getOrderFields(): array
    {
        return $this->sendRequest('/orderfields');
    }

    public function getOrderField(int $idorderfield): array
    {
        return $this->sendRequest('/orderfields/' . $idorderfield);
    }

    /*
     * Customer fields
     */
    public function getCustomerFields(): array
    {
        return $this->sendRequest('/customerfields');
    }

    public function getCustomerField(int $idcustomerfield): array
    {
        return $this->sendRequest('/customerfields/' . $idcustomerfield);
    }

    /*
     * Users
     */
    public function getUsers(array $filters = []): array
    {
        return $this->sendRequest('/users', [], self::METHOD_GET, $filters);
    }

    public function getAllUsers(array $filters = []): array
    {
        return $this->getAllResults('user', $filters);
    }

    public function getUser(int $iduser): array
    {
        return $this->sendRequest('/users/' . $iduser);
    }

    public function getCurrentUser(): array
    {
        return $this->getUser('me');
    }

    /*
     * Templates
     */
    public function getTemplates(): array
    {
        return $this->sendRequest('/templates');
    }

    /*
     * Locations
     */
    public function getLocations(array $filters = []): array
    {
        return $this->sendRequest('/locations', [], self::METHOD_GET, $filters);
    }

    public function getAllLocations(array $filters = []): array
    {
        return $this->getAllResults('location', $filters);
    }

    public function getLocation(int $id): array
    {
        return $this->sendRequest('/locations/' . $id);
    }

    public function addLocation(array $params, bool $autoLinkToParent = false): array
    {
        return $this->sendRequest('/locations', $params, self::METHOD_POST, ['auto_link_to_parent' => $autoLinkToParent]);
    }

    public function updateLocation(int $id, array $params): array
    {
        return $this->sendRequest('/locations/' . $id, $params, self::METHOD_PUT);
    }

    public function deleteLocation(int $id): array
    {
        return $this->sendRequest('/locations/' . $id, [], self::METHOD_DELETE);
    }

    public function getProductsOnLocation(int $id): array
    {
        return $this->sendRequest('/locations/' . $id . '/products', [], self::METHOD_GET);
    }

    public function getAllProductsOnLocation(int $id): array
    {
        return $this->getAllResults('/locations/' . $id . '/products');
    }

    /*
     * Stats
     */
    public function getStats(): array
    {
        return $this->sendRequest('/stats');
    }

    public function getStat(int $key): array
    {
        return $this->sendRequest('/stats/' . $key);
    }

    /*
    * Comments
    */

    public function getComments(array $filters = []): array
    {
        return $this->sendRequest('/comments', [], self::METHOD_GET, $filters);
    }

    public function getComment(int $idcomment): array
    {
        return $this->sendRequest('/comments/' . $idcomment, [], self::METHOD_GET);
    }

    public function deleteComment(int $idcomment): array
    {
        return $this->sendRequest('/comments/' . $idcomment, [], self::METHOD_DELETE);
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
                $result = $this->sendRequest($baseEndpoint, [], self::METHOD_GET, $filters);
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
    public function getResultGenerator(string $entity, array $filters = [])
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
    public function addCompany(array $params): array
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
    public function sendRequest(string $endpoint, array $params = [], string $method = self::METHOD_GET, array $filters = []): array
    {
        $url = $this->getUrl($endpoint, $filters);

        $this->debug('URL: ' . $url);

        $curlSession = curl_init();

        curl_setopt($curlSession, CURLOPT_HEADER, false);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curlSession, CURLOPT_URL, $url);
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

    protected function getUrl(string $endpoint, ?array $filters): string
    {
        $result = $this->protocol . '://' . $this->company . '.' . $this->apiHost . $this->apiLocation . '/' . $this->apiVersion . $endpoint;

        if (! empty($filters)) {
            $i = 0;
            foreach ($filters as $key => $value) {
                if ($i == 0) {
                    $result .= '?';
                } else {
                    $result .= '&';
                }
                $result .= $key . '=' . urlencode($value);
                $i++;
            }
        }

        return $result;
    }

    protected function prepareData(array $params): bool|string
    {
        return json_encode($params);
    }

    protected function debug(string $message): void
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

    protected function setPostData(CurlHandle $curlSession, string $method, array $params = []): void
    {
        if (! in_array($method, [self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE])) {
            return;
        }

        $data = $this->prepareData($params);
        $this->debug('Data: ' . $data);

        curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $data);
    }

    protected function setSslVerification(CurlHandle $curlSession): void
    {
        if ($this->skipSslVerification) {
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, false);
        }
    }

    /**
     * @throws RateLimitException
     */
    protected function handleRateLimitReached(string $endpoint, array $params = [], string $method = self::METHOD_GET, array $filters = [])
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
