<?php
/**
 * Resolve the Aimeos site logo uploaded via JQAdm -> Settings -> Basic.
 * $aimeossite is composed into every view by AppServiceProvider.
 */
$siteLogoUrl = null;
try {
	$logoRel = '';
	if( isset( $aimeossite ) && $aimeossite ) {
		if( method_exists( $aimeossite, 'getLogo' ) ) {
			$logoRel = trim( (string) $aimeossite->getLogo() );
		}
		if( $logoRel === '' && method_exists( $aimeossite, 'getConfigValue' ) ) {
			$logoRel = trim( (string) $aimeossite->getConfigValue( 'logo', '' ) );
		}
	}
	if( $logoRel !== '' ) {
		$base   = rtrim( (string) config( 'shop.resource.fs-media.baseurl', '' ), '/' );
		$baseFs = rtrim( (string) config( 'shop.resource.fs-media.basedir', public_path( 'aimeos' ) ), '/' );
		$relPath = ltrim( $logoRel, '/' );
		if( $base !== '' && is_file( $baseFs . '/' . $relPath ) ) {
			$siteLogoUrl = $base . '/' . $relPath;
		}
	}
} catch( \Throwable $e ) {
	$siteLogoUrl = null;
}
?>
<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Duains</title>

		@if( config('app.debug') !== true )
			<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self' 'nonce-{{ app( 'aimeos.context' )->get()->nonce() }}'; {{ config( 'shop.csp.frontend', 'style-src \'unsafe-inline\' \'self\'; img-src \'self\' data: https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		@if( in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) )
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.rtl.css?v=' . config( 'shop.version', 1 ) ) }}">
		@else
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.css?v=' . config( 'shop.version', 1 ) ) }}">
		@endif
		<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/aimeos.css?v=' . config( 'shop.version', 1 ) ) }}">
		<link type="text/css" rel="stylesheet" href="{{ asset('css/duains-shop.css?v=' . config( 'shop.version', 1 ) ) }}">

		<link rel="icon" href="{{ asset('duains-favicon.svg') }}" type="image/svg+xml">

		@yield('aimeos_header')

		<style nonce="{{ app( 'aimeos.context' )->get()->nonce() }}">
			:root {
				@foreach( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getConfigValue( 'theme/default', [] ) as $key => $value )
					{{ $key }}: {{ $value }};
				@endforeach
			}

			/* Duains brand wordmark (renders regardless of APP_URL) */
			.duains-brand {
				display: inline-flex;
				align-items: center;
				font-weight: 700;
				font-size: 1.35rem;
				letter-spacing: 0.18em;
				text-transform: uppercase;
				color: #fff;
				text-decoration: none;
				line-height: 1;
			}
			.duains-brand span { color: #C9A96A; font-weight: 700; }
			.duains-brand.footer { color: #1c1c1e; }
			.duains-brand.footer span { color: #A8834B; }

			/* Uploaded site logo (replaces the wordmark when set in JQAdm) */
			.duains-brand-img {
				display: block;
				max-height: 100px;
				width: auto;
				height: auto;
			}
			.duains-brand.footer .duains-brand-img {
				max-height: 100px;
			}

			/* Basket offscreen safety net (in case Aimeos component CSS 404s) */
			.basket-mini .zeynep {
				position: fixed;
				top: 0;
				bottom: 0;
				right: -20rem;
				left: unset;
				width: 20rem;
				z-index: 1032;
				pointer-events: none;
				transition: transform .25s;
			}
			.basket-mini .zeynep.opened { transform: translateX(-20rem); pointer-events: auto; }
		</style>

		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/roboto-condensed-v19-latin-700.woff2') }}" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="{{ asset('vendor/shop/themes/default/assets/bootstrap-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
	</head>
	<body class="{{ $page ?? '' }}">
		<nav class="navbar navbar-expand-md navbar-top">
			<a class="navbar-brand duains-brand" href="/" title="Duains">
				@if( $siteLogoUrl )
					<img class="duains-brand-img" src="{{ $siteLogoUrl }}?v={{ config('shop.version', 1) }}" alt="Duains">
				@else
					DUAINS<span>.shop</span>
				@endif
			</a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-top" aria-controls="navbar-top" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbar-top">
				@yield('aimeos_head_nav')
			</div>

			@yield('aimeos_head_locale')
			@yield('aimeos_head_search')

			<ul class="navbar-nav">
				@if (Auth::guest() && config('app.shop_registration'))
					<li class="nav-item register"><a class="nav-link" href="{{ airoute( 'register' ) }}" title="{{ __( 'Register' ) }}"><span class="name">{{ __('Register') }}</span></a></li>
				@endif
				@if (Auth::guest())
					<li class="nav-item login"><a class="nav-link" href="{{ airoute( 'login' ) }}" title="{{ __( 'Login' ) }}"><span class="name">{{ __( 'Login' ) }}</span></a></li>
				@else
					<li class="nav-item login profile dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false" title="{{ __( 'Account' ) }}"><span class="name">{{ __( 'Account' ) }}</span> <span class="caret"></span></a>
						<ul class="dropdown-menu dropdown-menu-end" role="menu">
							<li class="dropdown-item"><a class="nav-link" href="{{ airoute( 'aimeos_shop_account' ) }}"><span class="name">{{ __( 'Profile' ) }}</span></a></li>
							<li class="dropdown-item"><form id="logout" action="{{ airoute( 'logout' ) }}" method="POST">{{ csrf_field() }}<button class="nav-link"><span class="name">{{ __( 'Logout' ) }}</span></button></form></li>
						</ul>
					</li>
				@endif
			</ul>

			@yield('aimeos_head_basket')
		</nav>

		<div class="content">
			@yield('aimeos_stage')
			<main>
				@yield('aimeos_body')
				@yield('content')
			</main>
		</div>


		<footer>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-8">
						<div class="row">
							<div class="col-sm-6 footer-left">
								<div class="footer-block">
									<h2 class="pb-3" aria-label="{{ __('Legal information') }}">{{ __( 'LEGAL' ) }}</h2>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'terms']) }}">{{ __( 'Terms & Conditions' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'privacy']) }}">{{ __( 'Privacy Notice' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'cancel']) }}">{{ __( 'Cancellation' ) }}</a></p>
								</div>
							</div>
							<div class="col-sm-6 footer-center">
								<div class="footer-block">
									<h2 class="pb-3" aria-label="{{ __('About the company') }}">{{ __( 'ABOUT US' ) }}</h2>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'contact']) }}">{{ __( 'Contact us' ) }}</a></p>
									<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'about']) }}">{{ __( 'Company' ) }}</a></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 footer-right">
						<div class="footer-block">
							<a class="duains-brand footer" href="/" title="Duains">
								@if( $siteLogoUrl )
									<img class="duains-brand-img" src="{{ $siteLogoUrl }}?v={{ config('shop.version', 1) }}" alt="Duains">
								@else
									DUAINS<span>.shop</span>
								@endif
							</a>
							<div class="social" aria-label="{{ __('Social media links') }}">
								<p><a href="#" class="sm facebook" title="Facebook" rel="noopener">Facebook</a></p>
								<p><a href="#" class="sm twitter" title="Twitter" rel="noopener">Twitter</a></p>
								<p><a href="#" class="sm instagram" title="Instagram" rel="noopener">Instagram</a></p>
								<p><a href="#" class="sm youtube" title="Youtube" rel="noopener">Youtube</a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>



		<a id="toTop" class="back-to-top" href="#" title="{{ __( 'Back to top' ) }}">
			<div class="top-icon"></div>
		</a>

		<!-- Scripts -->
		<script src="{{ asset('vendor/shop/themes/default/app.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		<script src="{{ asset('vendor/shop/themes/default/aimeos.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		@yield('aimeos_scripts')
	</body>
</html>
