# mage2_ext_demo_import

Demo module for [Habr](https://habr.com/ru/post/436020/).



## Installation

```bash
$ cd ${MAGE_ROOT}
$ composer require flancer32/mage2_ext_demo_import
$ ./bin/magento module:enable Flancer32_DemoImport
$ ./bin/magento setup:upgrade
$ ./bin/magento setup:di:compile
```


## Usage

Place importing data into `./etc/data/products.json` (image paths are related to `./etc/data/img/`):
```json
[
  {
    "sku": "...",
    "name": "...",
    "desc": "...",
    "desc_short": "...",
    "price": ...,
    "qty": ...,
    "categories": ["..."],
    "image_path": "..."
  }
]
```

... then run command:
```bash
$ ./bin/magento fl32:import:prod -t regular
```