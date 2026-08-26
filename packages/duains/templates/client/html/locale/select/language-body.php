<?php

/**
 * Duains storefront — locale language selector override
 * -----------------------------------------------------
 * Renders only when more than one language is available
 * (selectMap holds one entry per language). Identical markup
 * and class names as the ai-client-html original otherwise.
 *
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Duains (duains.shop), 2026
 */

$enc = $this->encoder();

/** client/html/locale/select/language/url/config
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
$config = $this->config( 'client/html/locale/select/language/url/config', [] );


?>
<?php if( count( $this->get( 'selectMap', [] ) ) > 1 ) : ?>
	<div class="locale-select-language">
		<h2 class="header"><?= $this->translate( 'client', 'Select language' ) ?></h2>

		<ul class="select-menu">
			<li class="select-dropdown select-current"><a href="#"><?= $enc->attr( $this->get( 'selectLanguageId', 'en' ) ) ?></a>
				<ul class="select-dropdown">

					<?php foreach( $this->get( 'selectMap', [] ) as $lang => $list ) : ?>
						<li class="select-item <?= ( $lang === $this->get( 'selectLanguageId', 'en' ) ? 'active' : '' ) ?>">
							<a href="<?= $enc->attr( $this->url( $this->request()->getTarget(), $this->param( 'controller' ), $this->param( 'action' ), array_merge( $this->get( 'selectParams', [] ), $list[$this->get( 'selectCurrencyId', 'EUR' )] ?? current( $list ) ), [], $config ) ) ?>"
								title="<?= $enc->attr( $lang ) ?>">
								<?= $enc->html( $this->translate( 'language', $lang ), $enc::TRUST ) ?>
							</a>
						</li>
					<?php endforeach ?>

				</ul>
			</li>
		</ul>

	</div>
<?php endif ?>