<?php

/**
 * @license MIT, https://opensource.org/licenses/MIT
 * @copyright Duains (duains.shop), 2026
 */

return [
	'name' => 'duains',
	'depends' => [
		'ai-client-html',
		'ai-cms-grapesjs',
	],
	'template' => [
		'client/html/templates' => [
			'templates/client/html',
		],
	],
	'custom' => [
		'admin/jqadm' => [
			'manifest.jsb2',
		],
	],
];