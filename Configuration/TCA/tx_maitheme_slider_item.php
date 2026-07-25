<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\FileConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\LinkConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\PassthroughConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_theme', 'Default/locallang_tca.xlf');

return (new Table($lang('item.slide')))
    ->setLabel('headline')
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
    ->setIconForType('default', 'content-carousel')
    ->addColumn('parent_uid', '', new PassthroughConfig())
    ->addColumn('sort', '', new PassthroughConfig())
    ->addColumn(
        'image',
        $lang('item.image'),
        (new FileConfig())
            ->setAllowed('common-image-types')
            ->setMaxItems(1)
            ->setAppearance(['createNewRelationLinkTitle' => 'Add slide image'])
    )
    ->addColumn(
        'headline',
        $lang('item.headline'),
        (new InputConfig())->setSize(60)->setEval('trim')
    )
    ->addColumn(
        'bodytext',
        $lang('item.bodytext'),
        (new TextConfig())->setRows(4)->setCols(60)->enableRte()
    )
    ->addColumn(
        'link',
        $lang('item.link'),
        new LinkConfig()
    )
    ->addTypeShowItem(
        '0',
        'hidden, sys_language_uid, l10n_parent,' .
        '--div--;' . $lang('tab.content') . ',' .
        'image, headline, bodytext, link'
    )
    ->getConfig();
