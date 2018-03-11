<?php
$EM_CONF[$_EXTKEY] = array (
  'title' => 'VHS: Fluid ViewHelpers',
  'description' => 'A collection of ViewHelpers to perform rendering tasks which are not natively supported by Fluid - for example: advanced formatters, math calculators, specialized conditions and Iterator/Array calculators and processors',
  'category' => 'misc',
  'author' => 'FluidTYPO3 Team',
  'author_email' => 'claus@namelesscoder.net',
  'author_company' => '',
  'shy' => '',
  'dependencies' => '',
  'conflicts' => '',
  'priority' => '',
  'module' => '',
  'state' => 'stable',
  'internal' => '',
  'uploadfolder' => 0,
  'createDirs' => '',
  'modify_tables' => '',
  'clearCacheOnLoad' => 0,
  'lockType' => '',
  'version' => '4.4.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'php' => '7.0.0-7.1.99',
      'typo3' => '7.6.13-8.7.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'suggests' => 
  array (
  ),
  '_md5_values_when_last_written' => '',
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'FluidTYPO3\\Vhs\\' => 'Classes/',
    ),
  ),
  'autoload-dev' => 
  array (
    'psr-4' => 
    array (
      'FluidTYPO3\\Vhs\\Tests\\' => 'Tests/',
    ),
  ),
);
