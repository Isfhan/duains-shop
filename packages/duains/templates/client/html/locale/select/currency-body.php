<?php

/**
 * Duains storefront — locale currency selector override
 * -----------------------------------------------------
 * Renders only when the current language offers more than
 * one currency. Identical markup and class names as the
 * ai-client-html original otherwise.
 *
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Duains (duains.shop), 2026
 */

$enc = $this->encoder();

/** client/html/locale/select/currency/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.09
 */
$config = $this->config( 'client/html/locale/select/currency/url/config', [] );


?>
<?php if( count( $this->get( 'selectMap', map() )->get( $this->get( 'selectLanguageId', 'en' ), [] ) ) > 1 ) : ?>
	<div class="locale-select-currency">
		<h2 class="header"><?= $this->translate( 'client', 'Select currency' ) ?></h2>

		<ul class="select-menu">
			<li class="select-dropdown select-current"><a href="#"><?= $this->get( 'selectCurrencyId', 'EUR' ) ?></a>
				<ul class="select-dropdown">

					<?php foreach( $this->get( 'selectMap', map() )->get( $this->get( 'selectLanguageId', 'en' ), [] ) as $currency => $locParam ) : ?>
						<li class="select-item <?= ( $currency === $this->get( 'selectCurrencyId', 'EUR' ) ? 'active' : '' ) ?>">
							<a href="<?= $enc->attr( $this->url( $this->request()->getTarget(), $this->param( 'controller' ), $this->param( 'action' ), array_merge( $this->get( 'selectParams', [] ), $locParam ), [], $config ) ) ?>"
								title="<?= $enc->attr( $currency ) ?>">
								<?= $enc->html( $this->translate( 'currency', $currency ), $enc::TRUST ) ?>
							</a>
						</li>
					<?php endforeach ?>

				</ul>
			</li>
		</ul>

	</div>
<?php endif ?>