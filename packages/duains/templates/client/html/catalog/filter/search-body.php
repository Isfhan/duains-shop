<?php

/**
 * Duains storefront — catalog search override
 * -------------------------------------------
 * Same class names as the ai-client-html original (catalog-filter-search,
 * input-group, value, reset, btn-search, search-lists) so the core
 * catalog-filter.js autocompleter and reset logic keeps working.
 * The .header-name slide-toggle block is dropped: the navbar search is
 * an always-visible frosted pill styled in public/mytheme/aimeos.css.
 *
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Duains (duains.shop), 2026
 */

$enc = $this->encoder();

/** client/html/catalog/filter/search/force-search
 * Always reuse the current input for full text searches
 *
 * Normally, the full text search string is added to the input field after each
 * search. This is also the standard behavior of other shops.
 *
 * If it's desired, setting this configuration option to "0" will drop the full
 * text search input so it's not used if the user selects a category or attribute
 * filter.
 *
 * @param boolean True to reuse the search string, false to clear after each search
 * @since 2020.04
 */
$enforce = $this->config( 'client/html/catalog/filter/search/force-search', true );

?>
<?php $this->block()->start( 'catalog/filter/search' ) ?>
<div class="section catalog-filter-search" aria-label="<?= $enc->attr( $this->translate( 'client', 'Product search' ) ) ?>">

	<div class="search-lists">
		<div class="input-group">
			<input class="form-control value" autocomplete="off"
				name="<?= $enc->attr( $this->formparam( 'f_search' ) ) ?>"
				title="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"
				placeholder="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"
				value="<?= $enc->attr( $enforce ? $this->param( 'f_search' ) : '' ) ?>"
				data-url="<?= $enc->attr( $this->link( 'client/html/catalog/suggest/url', ['f_search' => '_term_'] ) ) ?>"
				data-hint="<?= $enc->attr( $this->translate( 'client', 'Please enter at least three characters' ) ) ?>"
			><!--
			--><button class="btn reset" type="reset" title="<?= $enc->attr( $this->translate( 'client', 'Reset' ) ) ?>"><span class="symbol"></span></button><!--
			--><button class="btn btn-search" type="submit" title="<?= $enc->attr( $this->translate( 'client', 'Search' ) ) ?>"></button>
		</div>
	</div>
</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'catalog/filter/search' ) ?>