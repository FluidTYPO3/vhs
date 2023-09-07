<?php
$EM_CONF['vhs'] = array (
  'title' => 'VHS: Fluid ViewHelpers',
  'description' => 'A collection of ViewHelpers for performing rendering tasks that are not natively provided by the Fluid templating engine.',
  'category' => 'misc',
  'author' => 'FluidTYPO3 Team',
  'author_email' => 'claus@namelesscoder.net',
  'author_company' => '',
  'shy' => '',
  'dependencies' => '',
  'conflicts' => '',
  'priority' => '',
  'module' => '',
  'state' => 'beta',
  'internal' => '',
  'uploadfolder' => 0,
  'createDirs' => '',
  'modify_tables' => '',
  'clearCacheOnLoad' => 0,
  'lockType' => '',
  'version' => '6.1.3',
  'constraints' =>
  array (
    'depends' =>
    array (
      'php' => '7.4.0-8.2.99',
      'typo3' => '10.4.0-11.5.99',
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
