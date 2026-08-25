<?php

namespace Aimeos\Admin\JQAdm\Product\Stock;

use Aimeos\MShop\Product\Item\Iface as ProductItemIface;


/**
 * Null-safe override of the product stock JQAdm client.
 *
 * Fixes a PHP 8.2 TypeError in the parent fromArray() where a missing
 * "stock.id" (new stock rows) caused Map::pull(null) to be called.
 */
class StockFix extends \Aimeos\Admin\JQAdm\Product\Stock\Standard
{
	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item object without referenced domain items
	 * @param array $data Data array
	 * @return \Aimeos\MShop\Product\Item\Iface Modified product item
	 */
	protected function fromArray( \Aimeos\MShop\Product\Item\Iface $item, array $data ) : \Aimeos\MShop\Product\Item\Iface
	{
		$stockItems = [];
		$ids = map( $data )->col( 'stock.id' )->filter();

		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );
		$filter = $manager->filter()->add( 'stock.productid', '==', $item->getId() );
		$stocks = $manager->search( $filter );

		foreach( $data as $entry )
		{
			$id = (string) $this->val( $entry, 'stock.id' );
			$stockItem = $stocks->pull( $id ) ?: $manager->create();
			$stockItem->fromArray( $entry )->setProductId( $item->getId() );

			if( ( $entry['stock.stockflag'] ?? false ) ) {
				$stockItem->setStockLevel( ( $stockItem->getStockLevel() ?? 0 ) + (int) ( $entry['stock.stockdiff'] ?? 0 ) );
			}

			$item->setInStock( (int) $stockItem->getStockLevel() > 0 || $stockItem->getStockLevel() === null );

			$stockItems[] = $stockItem;
		}

		$manager->delete( $stocks );
		$manager->save( $stockItems, false );

		return $item;
	}
}
