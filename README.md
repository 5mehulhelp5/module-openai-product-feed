# OpenAI Product Feed Generator for Magento 2

[![Latest Version](https://img.shields.io/packagist/v/angeo/module-openai-product-feed)](https://packagist.org/packages/angeo/module-openai-product-feed)
[![Total Downloads](https://img.shields.io/packagist/dt/angeo/module-openai-product-feed)](https://packagist.org/packages/angeo/module-openai-product-feed)
[![License](https://img.shields.io/packagist/l/angeo/module-openai-product-feed)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/dependency-v/angeo/module-openai-product-feed/php)](composer.json)
![Magento](https://img.shields.io/badge/Magento-2.4.x-orange)
![OpenAI Feed Spec](https://img.shields.io/badge/OpenAI%20Feed%20Spec-Stable-blue)

Generates an [OpenAI (ChatGPT) product feed](https://developers.openai.com/commerce/specs/file-upload/products) per store view so your Magento catalog can be discovered — and purchased — inside ChatGPT.

The exported CSV is compliant with the **OpenAI Product Feed file-upload specification (Stable)** and covers **all Magento product types**: simple, virtual, downloadable, configurable, bundle and grouped.

## Features

- [x] Feed columns aligned with the OpenAI Product Feed file-upload specification (Stable): `item_id`, `is_eligible_search`, `is_eligible_checkout`, `image_url`, `item_weight_unit`, `return_deadline_in_days`, and more
- [x] All Magento product types: simple, virtual, downloadable, configurable, bundle, grouped
- [x] Configurable products exported as parent listing + child variant rows (`group_id`, `variant_dict`, `item_group_title`)
- [x] Grouped products linked via `related_product_id` / `relationship_type`
- [x] Digital goods flagged with `is_digital`
- [x] Fault-tolerant generation: failing products are logged and skipped, never abort the run
- [x] Dedicated log file: `var/log/angeo_openai_feed.log`
- [ ] Optional attributes (media gallery, reviews, Q&A)
- [ ] Test coverage

## Requirements

- PHP >= 8.1
- Magento 2.4.x (Open Source or Adobe Commerce)

## Installation

You can install this module as a Composer package.

1. `composer require angeo/module-openai-product-feed`
2. Run `bin/magento setup:upgrade`

### As a module

1. Download latest release files and extract them under `app/code/Angeo/OpenAiProductFeed`
2. Run `bin/magento setup:upgrade`

## Upgrading from 1.x

Version 2.0.0 is a breaking release. Feed columns were renamed to match the OpenAI
specification (`item_id`, `is_eligible_search`, `is_eligible_checkout`, `image_url`,
`item_weight_unit`, `return_deadline_in_days`, …) and `ProductMapperInterface::map()`
now returns a list of rows. See [CHANGELOG.md](CHANGELOG.md) for the full list.

No data migration is needed: the `enable_search` / `enable_checkout` product
attributes are unchanged — only the exported column names differ.

## Configuration

You can find the module's configuration under `Stores -> Settings -> Configuration -> Angeo`:
seller identity (name, URL, privacy policy, terms of service) and return policy
(URL, return window in days). Per the OpenAI specification, `seller_privacy_policy`
and `seller_tos` are required when checkout is enabled for a product.

Two product attributes control per-product eligibility (found in the
"OpenAI Configurations" attribute group):

- **Enable Search** → exported as `is_eligible_search`
- **Enable Checkout** → exported as `is_eligible_checkout` (automatically forced to
  `false` in the feed when search is not enabled, per the specification)

## Product Feed

To generate the product feeds manually, use the `angeo:product-feed:generate` Magento command:

```
  bin/magento angeo:product-feed:generate
```

A cron job (`angeo_generate_openai_feed`) also runs daily at 02:00.

Output file path, relative to the `var` directory: `var/angeo/openai_feed/<store_code>.csv`

Skipped products and per-store summaries are written to `var/log/angeo_openai_feed.log`.

## Product type handling

| Type | Behavior |
| --- | --- |
| Simple | One row |
| Virtual / Downloadable | One row, `is_digital=true`, no weight |
| Configurable | Parent row (`listing_has_variations=true`) + one row per enabled child with `group_id`, `item_group_title` and JSON `variant_dict` |
| Grouped | One row, minimal price, children listed in `related_product_id` with `relationship_type=part_of_set` |
| Bundle | One row, minimal ("from") price for dynamic bundles, fixed price otherwise |

## Contributing

Found a bug, have a feature suggestion or just want to help in general? Contributions are very welcome! Check out the list of active issues or submit one yourself.

---

**Need help with agentic commerce for Magento?** Professional support, AEO audits and implementation at [angeo.dev](https://angeo.dev/). Check how your store looks to AI agents with the free scanner at [api.angeo.dev](https://api.angeo.dev/).

*Questions? Contact me at info@angeo.dev*
