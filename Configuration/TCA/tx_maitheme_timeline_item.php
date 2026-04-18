<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\FileConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\PassthroughConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_theme', 'Default/locallang_tca.xlf');

return (new Table($lang('item.timeline')))
    ->setLabel('title')
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
    ->setIconForType('default', 'content-list-bullet')
    ->addColumn('parent_uid', '', new PassthroughConfig())
    ->addColumn('sort', '', new PassthroughConfig())
    ->addColumn(
        'event_date',
        $lang('item.event_date'),
        (new InputConfig())->setSize(20)->setEval('trim')
    )
    ->addColumn(
        'title',
        $lang('item.title'),
        (new InputConfig())->setSize(60)->setEval('trim')->setRequired()
    )
    ->addColumn(
        'description',
        $lang('item.description'),
        (new TextConfig())->setRows(6)->setCols(60)->enableRte()
    )
    ->addColumn(
        'image',
        $lang('item.image'),
        (new FileConfig())
            ->setAllowed('common-image-types')
            ->setMaxItems(1)
            ->setAppearance(['createNewRelationLinkTitle' => 'Add image'])
    )
    ->addTypeShowItem(
        '0',
        'hidden, sys_language_uid, l10n_parent,' .
        '--div--;' . $lang('tab.content') . ',' .
        'event_date, title, description, image'
    )
    ->getConfig();
