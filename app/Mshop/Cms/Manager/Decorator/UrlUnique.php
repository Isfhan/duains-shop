<?php

namespace Aimeos\MShop\Cms\Manager\Decorator;


/**
 * Turns a duplicate CMS page URL database error into a clear, translatable
 * \Aimeos\MShop\Exception instead of the raw \Aimeos\Base\DB\Exception, which
 * \Aimeos\Admin\JQAdm\Base::report() would otherwise show as the generic
 * "Error saving data" message.
 */
class UrlUnique extends \Aimeos\MShop\Common\Manager\Decorator\Base implements \Aimeos\MShop\Common\Manager\Decorator\Iface
{
	public function save( $items, bool $fetch = true )
	{
		try {
			return $this->getManager()->save( $items, $fetch );
		} catch( \Aimeos\Base\DB\Exception $e ) {
			if( str_contains( $e->getMessage(), 'unq_mscms_url_sid' ) ) {
				throw new \Aimeos\MShop\Exception(
					'This URL is already in use by another page. Please choose a different URL.', 409, $e
				);
			}

			throw $e;
		}
	}
}
