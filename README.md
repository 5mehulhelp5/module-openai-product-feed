# OpenAi Feed Genarator for Magento 2

The OpenAi Product Feed Generator for Magento 2 automatically generates a product feed based on store, powering the chatbot with inventory, and detailed product information.

**This module is currently actively under development by Ievgenii Gryshkun and is open to public contributions.**
[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-☕-yellow.svg)](https://buymeacoffee.com/angeo)

## Features

- [x] ChatGPT Compatible Product Feed Export
- [ ] Add mappers for all product types
- [ ] Add Optional attributes
- [ ] Covers functionality with tests

## Requirements

- PHP >= 8.3

## Installation

You can install this module as a Composer package.

1. `composer require angeo/module-openai-product-feed`
2. Run `bin/magento setup:upgrade`

### As a module

1. Download latest release files and extract them under `app/code/Angeo/OpenAiProductFeed`
2. Run `bin/magento setup:upgrade`

## Configuration

You can find the Module's configuration under `Stores -> Settings -> Configuration -> Angeo`:

## Product Feed

To generate the product feeds manually, use the `angeo:product-feed:generate` Magento command:

```
  bin/magento angeo:product-feed:generate
```
Output file path. Relative to var directory is ["var/angeo/openai_feed/store_code.csv"]

## Contributing

Found a bug, have a feature suggestion or just want to help in general? Contributions are very welcome! Check out the list of active issues or submit one yourself.


*Have questions or need help? Contact me at i.gryshkun@gmail.com*

☕ Support the Project

If this module helps you save time or improve your Magento store, consider supporting development by buying me a coffee:

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-☕-yellow.svg)](https://buymeacoffee.com/angeo)

Your support helps me continue maintaining and improving open-source Magento tools. Thank you! 🙏

