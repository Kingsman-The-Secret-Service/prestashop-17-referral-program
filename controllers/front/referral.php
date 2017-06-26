<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Kk_ReferralProgramReferralModuleFrontController extends ModuleFrontController
{

	public function initContent(){

    	$this->setTemplate('module:kk_referralprogram/views/templates/front/referral.tpl');

    	parent::initContent();
	}
	
	public function postProcess(){	

		// Configuration::updateValue("KK_REFERRAL_VALID_PERIOD", "+3 Months");
		// Configuration::updateValue("KK_REFERRAL_MIN_MONEY",200);
		// Configuration::updateValue("KK_REFERRAL_QUANTITY",99);
		// Configuration::updateValue("KK_REFERRAL_EACH_USER",1);
		// Configuration::updateValue("KK_REFERRAL_REDUCTION_PERCENTAGE",10.0);
		// Configuration::updateValue("KK_REFERRAL_FREE_SHIPPING",true);
		Configuration::updateValue("KK_REFERRAL_HIGHLIGHT",false);
		// Configuration::updateValue("KK_REFERRAL_REDUCTION_EXCLUDE_SPECIAL",true);
		// Configuration::updateValue("KK_REFERRAL_GROUP_RESTRICTION_IDS", implode(',',[1,2,3]));
		// Configuration::updateValue("KK_REFERRAL_CHANGE_CUSTOMER_GROUP", implode(',',[4]));

		$tempVars = [];

		
		$cartRuleId = CartRule::getIdByCode($this->generateCouponCode());
		$cartRule = new CartRule($cartRuleId);

    	if(!$cartRuleId || strtotime($cartRule->date_to) < time()){

    		$this->generateCartRule();
    	}

    	$this->debug($this->getOrdersList($cartRuleId));

		$tempVars['code'] = $this->generateCouponCode();

    	$this->context->smarty->assign($tempVars);
    }

    protected function generateCartRule(){

    	$customer = $this->context->customer;
    	$name = $customer->id.'_'.$customer->firstname.$customer->lastname."_referral_program";

    	$cartRuleId = CartRule::getIdByCode($this->generateCouponCode());

    	if(!$cartRuleId && !CartRule::cartRuleExists($name)){

	    	$coupon = new CartRule();

    		$coupon->date_from = date('Y-m-d', time("now"));
	    	$coupon->date_to = date('Y-m-d', strtotime(Configuration::get("KK_REFERRAL_VALID_PERIOD"), strtotime("today")));
	    	$coupon->name = [1=> $name];
	    	$coupon->description = (string) "This offer is due to ". $customer->firstname.' '.$customer->lastname;
	    	$coupon->quantity = (int) Configuration::get("KK_REFERRAL_QUANTITY");
	    	$coupon->quantity_per_user = (int) Configuration::get("KK_REFERRAL_EACH_USER");
	    	$coupon->priority = (int) 1;
	    	$coupon->partial_use = (bool) true;
	    	$coupon->code = (string) $this->generateCouponCode($customer);
	    	$coupon->minimum_amount = Configuration::get("KK_REFERRAL_MIN_MONEY");
	    	$coupon->free_shipping = (bool) Configuration::get("KK_REFERRAL_FREE_SHIPPING");
	    	$coupon->highlight = (bool) Configuration::get("KK_REFERRAL_HIGHLIGHT");
	    	$coupon->reduction_percent = (float) Configuration::get("KK_REFERRAL_REDUCTION_PERCENTAGE");
	    	$coupon->group_restriction = (bool) true;
	    	$coupon->reduction_exclude_special = (bool) Configuration::get("KK_REFERRAL_REDUCTION_EXCLUDE_SPECIAL");
	    	$coupon->active = (bool) true;
			$coupon->add();

			$cartRuleId = $coupon->id;
    	}
		else{

			$coupon = new CartRule($cartRuleId);
			$coupon->date_from = date('Y-m-d', time("now"));
	    	$coupon->date_to = date('Y-m-d', strtotime(Configuration::get("KK_REFERRAL_VALID_PERIOD"), strtotime("today")));
	    	$coupon->quantity = (int) Configuration::get("KK_REFERRAL_QUANTITY");
	    	$coupon->quantity_per_user = (int) Configuration::get("KK_REFERRAL_EACH_USER");
	    	$coupon->minimum_amount = Configuration::get("KK_REFERRAL_MIN_MONEY");
	    	$coupon->free_shipping = (bool) Configuration::get("KK_REFERRAL_FREE_SHIPPING");
	    	$coupon->highlight = (bool) Configuration::get("KK_REFERRAL_HIGHLIGHT");
	    	$coupon->reduction_percent = (float) Configuration::get("KK_REFERRAL_REDUCTION_PERCENTAGE");
	    	$coupon->reduction_exclude_special = (bool) Configuration::get("KK_REFERRAL_REDUCTION_EXCLUDE_SPECIAL");
	    	$coupon->active = (bool) true;
			$coupon->update();
		}

		$groupIds = explode(',',Configuration::get("KK_REFERRAL_GROUP_RESTRICTION_IDS"));

		foreach ($groupIds as $id){

			$values[] = '('.(int)$cartRuleId.','.(int)$id.')';
		}

		$type = "group";
		Db::getInstance()->execute('REPLACE INTO `'._DB_PREFIX_.'cart_rule_'.$type.'` (`id_cart_rule`, `id_'.$type.'`) VALUES '.implode(',', $values));
    }

    protected function generateCouponCode(){

    	$customer = $this->context->customer;

    	$name = str_replace(' ', '', $customer->firstname.$customer->lastname);
    	$trimName = mb_substr($name, 0, 5);
    	return  strtoupper($trimName).$customer->id;
    }

    protected function changeCustomerGroup(){

    	$customer = $this->context->customer;
    	$customer->cleanGroups();
    	$customer->addGroups(explode(',', Configuration::get("KK_REFERRAL_CHANGE_CUSTOMER_GROUP")));
    }

    protected function getOrdersList($cartRuleId){


    	$orders = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'order_cart_rule` ocr
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
			AND ocr.`id_cart_rule` = '.(int)$cartRuleId
			);

    	$this->debug('
			SELECT *
			FROM `'._DB_PREFIX_.'order_cart_rule` ocr
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
			AND ocr.`id_cart_rule` = '.(int)$cartRuleId);

    	return Db::getInstance()->executeS('
			SELECT DISTINCT id_cart_rule
			FROM `'._DB_PREFIX_.'order_cart_rule` ocr
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
			AND o.`id_customer` = '.(int)$id_customer);
    }

    public function debug($debug){

    	echo "<pre>";
		print_r($debug);
		die;
    }

}