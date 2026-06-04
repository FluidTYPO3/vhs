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
  'version' => '7.2.1',
  'constraints' => 
  array (
    'depends' => 
    array (
      'php' => '8.2.0-8.4.99',
      'typo3' => '13.4.0-14.3.99',
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
