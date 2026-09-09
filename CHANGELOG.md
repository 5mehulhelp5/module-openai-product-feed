# Changelog

All notable changes to this module are documented in this file.

## [2.1.0] - 2026-07-06

### Performance

Feed generation was profiled against a production benchmark (1,900
configurable products, 5 store views, ~10,300 rows per file, 50 minutes on
v2.0.0) and the per-product query hot spots were replaced with batch loading:

- **Stock data is batch-preloaded per collection page.** Availability and
  salable quantity are now read from a single SELECT against the MSI
  `inventory_stock_{id}` view per page (and per configurable parent for its
  children), replacing two or more queries per SKU. The resolver is
  fail-open: SKUs missing from the preload fall back to the per-product
  Magento APIs, so custom stock setups keep working.
- **Category paths are served from an in-memory map** loaded with one
  category collection query per store view, replacing repository lookups per
  ancestor per product.
- **The product collection loads an explicit attribute list** instead of
  `addAttributeToSelect('*')`, drops the unused media-gallery join, preloads
  URL rewrites (`addUrlRewrite`) and category IDs (`addCategoryIds`),
  removing two more per-product lookups.
- **Attribute option labels are memoized** when building `variant_dict`.

### Added

- `--store=<code>` option (repeatable) on `angeo:product-feed:generate` to
  generate selected store views only.
- Live per-page progress output in the CLI and total duration on completion.
- `attributes` di.xml argument on `ProductCollectionProvider` to extend the
  loaded attribute list for custom handlers.

### Fixed

- `inventory_quantity` stays empty for composite product types (configurable,
  bundle, grouped): the batch stock preload would otherwise export `0` on
  listing rows under the default stock, a semantic regression against v2.0.x.
- Configurable option labels used in `variant_dict` are resolved and cached
  per store view, so multi-store runs cannot leak labels between store views.

### Changed

- `variant_dict` keys are now sorted alphabetically (natural, case-
  insensitive), making feed output deterministic and diff-friendly across
  runs. Previously key order followed each parent's super-attribute position.

## [2.0.1] - 2026-07-06

### Changed

- Lowered the PHP requirement from 8.3 to **8.1**, extending compatibility to
  Magento 2.4.4+ environments. Typed class constants (a PHP 8.3-only syntax)
  were replaced with untyped constants; no behavior changes.

## [2.0.0] - 2026-07-04

### Breaking changes

- **Feed columns renamed to match the OpenAI Product Feed file-upload specification (Stable):**
  `id` → `item_id`, `enable_search` → `is_eligible_search`, `enable_checkout` → `is_eligible_checkout`,
  `image_link` → `image_url`, `return_window` → `return_deadline_in_days`.
  If you post-process the generated CSV, update your column mapping.
- **`ProductMapperInterface::map()` now returns a list of feed rows** (each row is an
  associative array keyed by feed column name) instead of a single positional row.
  Custom type mappers must be updated accordingly.
- The `weight` column now contains only the numeric value; the unit is exported in the
  new `item_weight_unit` column (`lb` / `kg`), as required by the specification.
- Boolean flags are exported as lower-case `true` / `false` strings, as required by
  the specification.
- The custom `GenerateOpenAiFeedForStoreException` class was removed. Feed generation
  no longer throws on per-product or per-store errors (see below).

### Added

- **Support for all Magento product types**: configurable, virtual, downloadable,
  bundle and grouped, in addition to simple products.
  - Configurable products are exported as a parent listing row plus one row per
    enabled child variant, with `group_id`, `listing_has_variations`,
    `item_group_title` and a JSON `variant_dict` built from the configurable
    attributes (spec "Variants" schema).
  - Virtual and downloadable products are exported with `is_digital=true` and no
    physical weight.
  - Grouped products reference their associated products through
    `related_product_id` / `relationship_type=part_of_set` (spec "Related Products"
    schema) and export their minimal price.
  - Bundle products export their minimal ("from") price for dynamic bundles and
    the fixed price for fixed bundles.
- New feed columns: `item_weight_unit`, `is_digital`, `group_id`,
  `listing_has_variations`, `variant_dict`, `item_group_title`,
  `related_product_id`, `relationship_type`.
- Dedicated log file `var/log/angeo_openai_feed.log` with a per-store summary
  (rows written / products skipped).

### Changed

- **Feed generation is now fault-tolerant.** Custom exceptions were replaced with
  logging: a product that fails to map is skipped and logged, a failing attribute
  handler produces an empty cell and a warning, and a store whose writer cannot be
  created is skipped — the run always continues.
- `description` is exported as plain text (HTML stripped, entities decoded,
  whitespace collapsed, truncated to 5,000 characters) per the specification.
- `product_category` now exports the full category path with the ` > ` separator
  (e.g. `Apparel & Accessories > Shoes`) and no longer crashes on uncategorized
  products.
- Prices are formatted as `<amount> <ISO 4217 code>` (e.g. `79.99 USD`) without
  locale symbols or thousands separators, and resolve through the price-info
  pipeline so composite product types export a correct minimal price.
- `sale_price` is only exported when an active discount exists (final price below
  regular price), honoring special-price date ranges and catalog price rules.
- `is_eligible_checkout` is forced to `false` whenever `is_eligible_search` is not
  `true`, per the specification dependency rule.
- CSV rows are normalized against the header definition, guaranteeing column order
  and empty cells for missing values.
- Attribute handlers are instantiated once per run instead of once per product row.

### Fixed

- Fatal error in the per-store service: the second `throw` referenced an unimported
  exception class, and the exception file name did not match its class name (PSR-4).
- Operator-precedence bug in `ProductWeightProvider` that dropped the weight unit
  for products with a weight and exported `0 <unit>` for products without one.
- `ProductCategoryProvider` crash on products without categories (`current([])`
  returns `false`, which the null-coalescing fallback never caught).
- Broken `ProductAttributeHandlerProvider` (class/file name mismatch and wrong
  import) rewritten and wired into the mapping pipeline.
- Inventory quantity lookups no longer fail rows for composite product types that
  have no salable quantity of their own.
- `bin/magento angeo:product-feed:generate` no longer fails when the area code is
  already set.

## [1.0.2]

- Initial public release line: simple product export, seller and return policy
  enrichment, CLI command and daily cron.
