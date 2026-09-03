<?php

namespace Aimeos\Client\Html\Cms\Page;


/**
 * ai-cms-grapesjs's Standard::data() decodes the stored {html, css} JSON
 * but only ever uses "html" - "css" is silently discarded, so a CMS page's
 * saved styling never renders on the storefront. This re-derives
 * pageContent from the same already-loaded page item, prepending each
 * entry's css as an inline <style> block (body.php outputs pageContent
 * unescaped, so this requires no template changes).
 */
class CssFix extends Standard
{
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$view = parent::data( $view, $tags, $expire );

		if( $page = $view->get( 'pageCmsItem' ) )
		{
			$view->pageContent = $page->getRefItems( 'text', 'content' )->map( function( $item ) {
				$json = json_decode( $item->getContent(), true );
				$html = is_array( $json ) ? ( $json['html'] ?? '' ) : $item->getContent();
				$css = is_array( $json ) && !empty( $json['css'] ) ? '<style>' . $json['css'] . '</style>' : '';

				return $css . '<div class="cms-content">' . $html . '</div>';
			} )->all();
		}

		return $view;
	}
}
