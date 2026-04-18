<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\PassthroughConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_theme', 'Default/locallang_tca.xlf');

return (new Table($lang('item.tab')))
    ->setLabel('tab_title')
    ->setSortingField('sort')
    ->setCreationDateField()
    ->setModifiedDateField()
    ->setDeleteField()
    ->setDisabledField('hidden')
    ->setLanguageField()
    ->setTranslationOriginField('l10n_parent')
    ->setTranslationOriginDiffSourceField()
    ->hideTableInLists(true)
    ->enableVersioning()
    ->setIconForType('default', 'content-tab')
    ->addColumn('parent_uid', '', new PassthroughConfig())
    ->addColumn('sort', '', new PassthroughConfig())
    ->addColumn(
        'tab_title',
        $lang('item.tab_title'),
        (new InputConfig())->setSize(60)->setEval('trim')->setRequired()
    )
    ->addColumn(
        'content',
        $lang('item.content'),
        (new TextConfig())->setRows(10)->setCols(60)->enableRte()
    )
    ->addTypeShowItem(
        '0',
        'hidden, sys_language_uid, l10n_parent,' .
        '--div--;' . $lang('tab.content') . ',' .
        'tab_title, content'
    )
    ->getConfig();
