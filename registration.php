<?php
/**
 * Script to register M2-module
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

use Flancer32\DemoImport\Config as Cfg;
use Magento\Framework\Component\ComponentRegistrar as Registrar;

Registrar::register(
    Registrar::MODULE,
    Cfg::MODULE,
    __DIR__
);