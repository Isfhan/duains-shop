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
			<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self' 'nonce-{{ app( 'aimeos.context' )->get()->nonce() }}'; {{ config( 'shop.csp.frontend', 'style-src \'unsafe-inline\' \'self\' https://fonts.googleapis.com; font-src \'self\' data: https://fonts.gstatic.com; img-src \'self\' data: https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		@if( in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) )
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.rtl.css?v=' . config( 'shop.version', 1 ) ) }}">
		@else
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.css?v=' . config( 'shop.version', 1 ) ) }}">
		@endif
		<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/aimeos.css?v=' . config( 'shop.version', 1 ) ) }}">
		<link type="text/css" rel="stylesheet" href="{{ asset('css/duains-shop.css?v=' . config( 'shop.version', 1 ) ) }}">

		<!-- Duains luxury header overrides (fonts + override stylesheet) -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

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

		<!-- Duains luxury header overrides — LAST in head so they win the cascade
		     over the Aimeos theme CSS AND the component CSS injected via @yield('aimeos_header') -->
		<link type="text/css" rel="stylesheet" href="{{ asset('mytheme/aimeos.css?v=' . config( 'shop.version', 1 ) ) }}">
	</head>
	<body class="{{ $page ?? '' }}">
		<div class="du-announce" role="note">
			<span>Free Delivery on all orders above Rs. 2500!</span>
		</div>
		<nav class="navbar navbar-expand-md navbar-top">
			<a class="navbar-brand duains-brand" href="/" title="Duains">
				@if( $siteLogoUrl )
					<img class="duains-brand-img" src="{{ $siteLogoUrl }}?v={{ config('shop.version', 1) }}" alt="Duains">
				@else
					DUAINS<span>.shop</span>
				@endif
			</a>

			<button class="navbar-toggler" type="button" aria-label="{{ __( 'Menu' ) }}">
				<span class="navbar-toggler-icon"></span>
			</button>

			@yield('aimeos_head_nav')
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
			<div class="container-xxl">
				<div class="row du-footer-main">
					<div class="col-lg-4 col-md-6 col-12 footer-col du-footer-brand">
						<div class="footer-block">
							<a class="duains-brand footer" href="/" title="Duains">
								@if( $siteLogoUrl )
									<img class="duains-brand-img" src="{{ $siteLogoUrl }}?v={{ config('shop.version', 1) }}" alt="Duains">
								@else
									DUAINS<span>.shop</span>
								@endif
							</a>
							<p class="du-footer-tagline">ELEGANCE IN EVERY SCENT</p>

							<ul class="du-footer-contact" aria-label="{{ __('Contact details') }}">
								<li class="du-footer-contact-row">
									<span class="du-footer-contact-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-7-7.2-7-12a7 7 0 1 1 14 0c0 4.8-7 12-7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
									</span>
									<span>14 Avenue de la Paix, Paris</span>
								</li>
								<li class="du-footer-contact-row">
									<span class="du-footer-contact-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
									</span>
									<a href="tel:+33144786000">+33 1 44 78 60 00</a>
								</li>
								<li class="du-footer-contact-row">
									<span class="du-footer-contact-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
									</span>
									<a href="mailto:care@duains.shop">care@duains.shop</a>
								</li>
							</ul>
						</div>
					</div>

					<div class="col-lg-4 col-md-6 col-12 footer-col du-footer-links">
						<div class="footer-block">
							<h2 class="pb-3" aria-label="{{ __('Customer service') }}">{{ __( 'CUSTOMER SERVICE' ) }}</h2>
							<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'faq']) }}">{{ __( 'FAQs' ) }}</a></p>
							<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'contact']) }}">{{ __( 'Contact Us' ) }}</a></p>
							<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'refund']) }}">{{ __( 'Refund Policy' ) }}</a></p>
							<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'terms']) }}">{{ __( 'Terms of Service' ) }}</a></p>
							<p><a href="{{ airoute(config('shop.client.html.cms.page.url.target', 'aimeos_page'), ['path' => 'privacy']) }}">{{ __( 'Privacy Policy' ) }}</a></p>
						</div>
					</div>

					<div class="col-lg-4 col-md-12 col-12 footer-col du-footer-featured">
						<div class="footer-block">
							<h2 class="pb-3" aria-label="{{ __('Featured categories') }}">{{ __( 'FEATURED' ) }}</h2>
							<p><a href="#">{{ __( 'Eau de Parfum' ) }}</a></p>
							<p><a href="#">{{ __( 'Body & Hair Mist' ) }}</a></p>
							<p><a href="#">{{ __( 'Discovery Sets' ) }}</a></p>
						</div>
					</div>
				</div>

				<div class="du-footer-social" aria-label="{{ __('Social media links') }}">
					<a href="#" class="sm instagram" title="Instagram" rel="noopener" aria-label="Instagram">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
					</a>
					<a href="#" class="sm facebook" title="Facebook" rel="noopener" aria-label="Facebook">
						<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.6l.4-3h-3V8.7c0-.9.3-1.5 1.6-1.5h1.6V4.5c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.2v2.4H7.5v3h2.8V21h3.2z"/></svg>
					</a>
					<a href="#" class="sm tiktok" title="TikTok" rel="noopener" aria-label="TikTok">
						<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.5 3v2.3a5.7 5.7 0 0 0 4 1.6v2.5a8 8 0 0 1-4-1.1V14a5.5 5.5 0 1 1-5.5-5.5c.3 0 .6 0 .9.1v2.6a3 3 0 1 0 2.1 2.8V3h2.5z"/></svg>
					</a>
				</div>

				<div class="du-footer-bottom">
					<span class="du-footer-copy">&copy; {{ date('Y') }} {{ __( 'Duains' ) }}. {{ __( 'All Rights Reserved.' ) }}</span>
				</div>
			</div>
		</footer>



		<a id="toTop" class="back-to-top" href="#" title="{{ __( 'Back to top' ) }}">
			<div class="top-icon"></div>
		</a>

		<!-- Scripts -->
		<script src="{{ asset('vendor/shop/themes/default/app.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		<script src="{{ asset('vendor/shop/themes/default/aimeos.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		<!-- Duains luxury header overrides (must load AFTER the Aimeos theme JS) -->
		<script src="{{ asset('mytheme/aimeos.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		@yield('aimeos_scripts')
	</body>
</html>
