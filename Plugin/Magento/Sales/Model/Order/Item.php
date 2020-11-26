<?php
namespace Coyote\Core\Plugin\Magento\Sales\Model\Order;
use Magento\Catalog\Model\Product as P;
use Magento\Sales\Model\Order\Item as Sb;
# 2020-11-26
# 1) «Argument 1 passed to Magento\InventorySourceDeductionApi\Model\IsItemCouldBeDeductedByTypes::execute()
# must be of the type string, null given,
# called in vendor/magento/module-inventory-shipping/Model/GetItemsToDeductFromShipment.php on line 210»:
# https://github.com/coyoteaccessories/site/issues/6
# 2) I have noticed that the `product_type` field of the `sales_order_item` table is NULL for some rows
# created since 2020-09-03:
# 	SELECT * FROM sales_order_item WHERE product_type IS NULL ORDER BY `created_at`;
# It is wrong. So I correct it with a plugin.
final class Item {
	/**
	 * 2020-11-26
	 * @param Sb $sb
	 * @param string|null $r
	 * @return string
	 * @see \Magento\Sales\Model\Order\Item::getProductType()
	 */
	function afterGetProductType(Sb $sb, $r) {
		if (!$r && ($p = $sb->getProduct())) { /** @var P $p */
			$sb->setProductType($r = $p->getTypeId())->save();
		}
		return $r;
	}
}