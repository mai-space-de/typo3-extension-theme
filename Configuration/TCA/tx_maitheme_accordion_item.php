<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\PassthroughConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_theme', 'Default/locallang_tca.xlf');

return (new Table($lang('item.accordion')))
    ->setLabel('question')
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
    ->setIconForType('default', 'content-accordion')
    ->addColumn('parent_uid', '', new PassthroughConfig())
    ->addColumn('sort', '', new PassthroughConfig())
    ->addColumn(
        'question',
        $lang('item.question'),
        (new InputConfig())->setSize(60)->setEval('trim')->setRequired()
    )
    ->addColumn(
        'answer',
        $lang('item.answer'),
        (new TextConfig())->setRows(10)->setCols(60)->enableRte()
    )
    ->addTypeShowItem(
        '0',
        'hidden, sys_language_uid, l10n_parent,' .
        '--div--;' . $lang('tab.content') . ',' .
        'question, answer'
    )
    ->getConfig();
