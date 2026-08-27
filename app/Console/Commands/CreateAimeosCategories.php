<?php

namespace App\Console\Commands;

use Aimeos\MShop;
use Illuminate\Console\Command;

class CreateAimeosCategories extends Command
{
	protected $signature = 'aimeos:create-categories
		{--root= : Code or ID of the parent node (defaults to the site root category)}
		{--dry-run : Preview categories without inserting them}';

	protected $description = 'Creates the Duains category tree (idempotent, skips existing codes)';

	/** Category tree: code => ['label' => ..., 'config' => [...], 'children' => [...]] */
	private array $categories = [
		'new-arrivals' => ['label' => 'New Arrivals'],
		'fragrances' => [
			'label' => 'Fragrances',
			'config' => ['css-class' => 'megamenu'],
			'children' => [
				'fragrances-edp' => ['label' => 'Eau de Parfum'],
				'fragrances-oud' => ['label' => 'Oud & Attar'],
				'fragrances-amber' => ['label' => 'Amber & Musk'],
			],
		],
		'gift-sets' => [
			'label' => 'Gift Sets',
			'children' => [
				'gift-sets-bridal' => ['label' => 'Bridal'],
				'gift-sets-discovery' => ['label' => 'Discovery'],
				'gift-sets-travel' => ['label' => 'Travel'],
			],
		],
	];

	public function handle(): int
	{
		$context = app('aimeos.context')->get();
		$manager = MShop::create($context, 'catalog');

		if( !( $parent = $this->resolveParent( $manager ) ) ) {
			return self::FAILURE;
		}

		$rows = [];
		$created = $skipped = 0;

		foreach( $this->categories as $code => $data ) {
			[$created, $skipped, $rows] = $this->createNode( $manager, $parent->getId(), $code, $data, 1, $created, $skipped, $rows );
		}

		$this->table( ['Code', 'Label', 'Level', 'Result'], $rows );
		$this->info( 'Created: ' . $created . ' | Skipped (already exists): ' . $skipped );

		if( $this->option( 'dry-run' ) ) {
			$this->warn( 'Dry-run: nothing was inserted.' );
		}

		return self::SUCCESS;
	}

	/**
	 * Resolves the parent node from the --root option (code or ID)
	 * or falls back to the site's root category.
	 */
	private function resolveParent( $manager ): ?\Aimeos\MShop\Catalog\Item\Iface
	{
		$root = $this->option( 'root' );

		if( $root )
		{
			try
			{
				if( is_numeric( $root ) ) {
					return $manager->getTree( $root );
				}

				$item = $manager->search( $manager->filter()->add( 'catalog.code', '==', $root ) )->first();

				if( $item ) {
					return $manager->getTree( $item->getId() );
				}
			}
			catch( \Throwable $e )
			{
				$this->error( 'Parent node not found: ' . $root . ' (' . $e->getMessage() . ')' );
				return null;
			}

			$this->error( 'Parent node not found: ' . $root );
			return null;
		}

		return $manager->getTree();
	}

	/**
	 * Recursively inserts a category and its children under the given parent.
	 */
	private function createNode( $manager, string $parentId, string $code, array $data, int $level,
		int $created, int $skipped, array $rows ): array
	{
		$label = $data['label'] ?? ucfirst( str_replace( '-', ' ', $code ) );
		$exists = !$manager->search( $manager->filter()->add( 'catalog.code', '==', $code ) )->isEmpty();

		if( !$exists && !$this->option( 'dry-run' ) )
		{
			$item = $manager->create()
				->setCode( $code )
				->setLabel( $label )
				->setStatus( 1 );

			foreach( $data['config'] ?? [] as $key => $value ) {
				$item->setConfigValue( $key, $value );
			}

			$manager->insert( $item, $parentId );
			$parentId = $item->getId();
			$created++;
			$rows[] = [$code, $label, $level, 'Created'];
		}
		else
		{
			$rows[] = [$code, $label, $level, $exists ? 'Skipped' : 'Dry-run'];

			if( $exists ) {
				$skipped++;
			}
		}

		foreach( $data['children'] ?? [] as $childCode => $childData ) {
			[$created, $skipped, $rows] = $this->createNode( $manager, $parentId, $childCode, $childData, $level + 1, $created, $skipped, $rows );
		}

		return [$created, $skipped, $rows];
	}
}