<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Okalm\Merchandizing\Controller\Adminhtml\Config;

/**
 * Description of Launch
 *
 * @author michiz
 */
class Launch extends \Magento\Backend\App\Action{
    
    public function execute() {
        
        $q = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $query = 'INSERT INTO okalm_product_promotion VALUES ';
        
        /**
         * Load all rules that doesn't use coupons
         */
        $rules = $this->_objectManager
                ->create('Magento\SalesRule\Model\ResourceModel\Rule\Collection')
                ->addFieldToFilter('coupon_type', 1);
        
        /**
         * Load all products
         */
        $productCollection = $this->_objectManager
                ->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
        
        $collection = $productCollection->create()
                    ->addAttributeToSelect('*')
                    ->load();
        
        /**
         * Need to clear table before insert new data
         */
        $q->getConnection('core_write')->query("TRUNCATE TABLE okalm_product_promotion");
       
        foreach ($collection as $product) {
            foreach($rules as $rule){
                $item = $this->_objectManager->create('Magento\Catalog\Model\Product');
                $item->setProduct($product);
                
                $validate = $rule->getActions()->validate($item);

                if($validate){
                    $product_id = $product->getId();
                    $rule_id    = $rule->getId();
                    $values[] = "($product_id, $rule_id)";
                    
                    break;
                }
            }
        }
        
        $query .= implode(',', $values);
        
        $q->getConnection('core_write')->query($query);
        
    }
}
