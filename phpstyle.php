<?php

// Specify the paths in this variable ---
$paths = array(__DIR__ . '/src');

$paths[] = __DIR__ . '/tests';
// --------------------------------------

// Specify the rules for code formatting ---------
$rules = array('@PSR12' => true);

$cscp = 'control_structure_continuation_position';
$rules[$cscp] = ['position' => 'next_line'];

$braces = array();
$braces['control_structures_opening_brace'] = 'next_line_unless_newline_at_signature_end';
$braces['functions_opening_brace'] = 'next_line_unless_newline_at_signature_end';
$braces['anonymous_functions_opening_brace'] = 'next_line_unless_newline_at_signature_end';
$braces['anonymous_classes_opening_brace'] = 'next_line_unless_newline_at_signature_end';
$braces['allow_single_line_empty_anonymous_classes'] = false;
$braces['allow_single_line_anonymous_functions'] = false;
$rules['braces_position'] = $braces;

$visibility = array('elements' => array());
$visibility['elements'] = array('method', 'property');
$rules['visibility_required'] = $visibility;

$rules['phpdoc_var_annotation_correct_order'] = true;

$rules['single_quote'] = ['strings_containing_single_quote_chars' => true];

$rules['no_unused_imports'] = true;

$rules['align_multiline_comment'] = true;

$rules['trim_array_spaces'] = true;

$order = ['case_sensitive' => true];
$order['null_adjustment'] = 'always_last';
$rules['phpdoc_types_order'] = $order;

$rules['new_with_parentheses'] = ['named_class' => false];

$rules['concat_space'] = ['spacing' => 'one'];

$rules['no_empty_phpdoc'] = true;

$groups = [];
$groups[] = ['template', 'extends'];
$groups[] = ['deprecated', 'link', 'see', 'since', 'codeCoverageIgnore'];
$groups[] = ['property', 'property-read', 'property-write'];
$groups[] = ['method'];
$groups[] = ['author', 'copyright', 'license'];
$groups[] = ['category', 'package', 'subpackage'];
$groups[] = ['param'];
$groups[] = ['return', 'throws'];
$rules['phpdoc_separation'] = ['groups' => $groups];

$align = ['align' => 'vertical'];
$align['tags'] = ['method', 'param', 'property', 'throws', 'type', 'var'];
$rules['phpdoc_align'] = $align;

$rules['statement_indentation'] = false;

$rules['align_multiline_comment'] = true;
// -----------------------------------------------

$finder = new \PhpCsFixer\Finder;

$finder->in((array) $paths);

$config = new \PhpCsFixer\Config;

$config->setRules($rules);

return $config->setFinder($finder);
