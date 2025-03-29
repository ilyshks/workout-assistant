<?php

// Файл конфигурации для PHP CS Fixer.
// Документация: https://cs.symfony.com/doc/config.html

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app') // Укажите папки для сканирования
    ->in(__DIR__ . '/resources')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/tests')
    ->exclude('vendor') // Исключаем папку vendor
    ->exclude('storage') // Пример исключения папки storage (Laravel)
    ->exclude('bootstrap/cache') // Пример (Laravel)
    // ->in(__DIR__ . '/src') // Или укажите конкретные папки: app, tests, routes и т.д.
    // ->in(__DIR__ . '/tests')
    ->name('*.php') // Искать только PHP файлы
    ->notName('*.blade.php') // Не включать Blade шаблоны
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true, // Используем стандарт PSR-12
    'strict_param' => true, // Дополнительное правило: проверять типы в phpdoc для @param
    'array_syntax' => ['syntax' => 'short'], // Использовать короткий синтаксис массивов []
    'binary_operator_spaces' => [ // Пробелы вокруг бинарных операторов
        'default' => 'single_space',
        'operators' => ['=>' => null] // Кроме =>
    ],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => [
        'statements' => ['return', 'throw', 'try', 'while', 'do', 'if', 'foreach', 'switch', 'case', 'default']
    ],
    'braces' => true,
    'cast_spaces' => true,
    'class_attributes_separation' => [
        'elements' => ['method' => 'one', 'property' => 'one', /* 'const' => 'one', 'trait_import' => 'none' */]
    ],
    'class_definition' => true,
    'concat_space' => ['spacing' => 'one'], // Пробел при конкатенации
    'declare_equal_normalize' => true,
    'elseif' => true,
    'encoding' => true,
    'full_opening_tag' => true,
    'function_declaration' => true,
    'function_typehint_space' => true,
    'single_line_comment_style' => ['comment_types' => ['hash']], // // вместо #
    'include' => true,
    'indentation_type' => true, // Отступы пробелами
    'linebreak_after_opening_tag' => true,
    'lowercase_cast' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_constant_casing' => true,
    'magic_method_casing' => true,
    'method_argument_space' => true,
    'native_function_casing' => true,
    'no_alias_functions' => true,
    'no_extra_blank_lines' => [
        'tokens' => ['extra', 'throw', 'use', 'use_trait']
    ],
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_closing_tag' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => ['use' => 'echo'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => true,
    'no_spaces_inside_parenthesis' => true,
    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_trailing_whitespace' => true, // Удалять пробелы в конце строк
    'no_trailing_whitespace_in_comment' => true,
    'no_unneeded_control_parentheses' => true,
    'no_unreachable_default_argument_value' => true,
    'no_useless_return' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'normalize_index_brace' => true,
    'not_operator_with_successor_space' => true, // Пробел после !
    'object_operator_without_whitespace' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha'], // Сортировка use по алфавиту
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_tag_type' => true,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_var_without_name' => true,
    'psr_autoloading' => true, // PSR-4 autoloading standard
    'self_accessor' => true,
    'short_scalar_cast' => true,
    'simplified_null_return' => false, // Requires PHP 7.1+
    'single_blank_line_at_eof' => true,
    'single_blank_line_before_namespace' => true,
    'single_class_element_per_statement' => true,
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    'single_quote' => true, // Использовать одинарные кавычки для строк
    'space_after_semicolon' => true,
    'standardize_not_equals' => true,
    'switch_case_semicolon_to_colon' => true,
    'switch_case_space' => true,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays']], // Запятая в конце многострочных массивов
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'visibility_required' => ['elements' => ['method', 'property']], // Указывать visibility (public, private, protected)
    'whitespace_after_comma_in_array' => true,
    // Добавьте или измените правила по своему усмотрению
    // Список всех правил: https://cs.symfony.com/doc/rules/index.html
])
->setFinder($finder);
