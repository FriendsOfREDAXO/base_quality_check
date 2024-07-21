<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckGroup;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckSubGroup;

/** @var rex_addon $this */
$addon = rex_addon::get('base_quality_check');

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass(
        'rex_base_quality_check',
        BaseQualityCheck::class,
    );
    rex_yform_manager_dataset::setModelClass(
        'rex_base_quality_check_group',
        BaseQualityCheckGroup::class,
    );
    rex_yform_manager_dataset::setModelClass(
        'rex_base_quality_check_sub_group',
        BaseQualityCheckSubGroup::class,
    );
}

rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex::getUser() && true === $this->getProperty('compile')) {
        $compiler = new rex_scss_compiler();
        $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('scss/bqc.scss')]));
        $compiler->setScssFile($scss_files);
        $compiler->setCssFile($this->getPath('assets/bqc.css'));
        $compiler->compile();
        rex_file::copy($this->getPath('assets/bqc.css'), $this->getAssetsPath('bqc.css'));
    }
});

rex_view::addCssFile($this->getAssetsUrl('bqc.css'));

rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
    $suchmuster = '<h4 class="rex-nav-main-title">[translate:navigation_base_addon]</h4>';
    $ersetzen = '<div class="rex-nav-main-title" style="padding:0px; margin-bottom: 14px;"></div>';
    $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
});

if (isset($_GET['page']) && is_string($_GET['page']) && preg_match('/base_quality_check/', $_GET['page'])) {
    rex_view::addJsFile($this->getAssetsUrl('bqc.js'));

    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        $ep->setSubject(str_replace('class="rex-page-main', 'class="bqc-addon rex-page-main', $ep->getSubject()));
    });

    $replacements = [
        '<title>Backend &lt;div id=&quot;backendcount&quot;&gt;&lt;/div&gt;' => '<title>Backend ',
        '<title>Frontend &lt;div id=&quot;frontendcount&quot;&gt;&lt;/div&gt;' => '<title>Frontend ',
        '<title>Live &lt;div id=&quot;livecount&quot;&gt;&lt;/div&gt;' => '<title>Live ',
    ];

    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) use ($replacements) {
        $ep->setSubject(strtr($ep->getSubject(), $replacements));
    });
}

$func = rex_request('func', 'string');
$id = rex_request('id', 'int');

if ('' !== $func && '' !== $id) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_base_quality_check');
    $sql->setWhere('id = :id', ['id' => $id]);
    $sql->setValue('check', 'checktask' == $func ? 1 : 0);
    $sql->update();
}

function updateTaskscounter()
{
    $all_tasks = BaseQualityCheck::query()->where('status', 1)->count();
    $all_tasks_checked = BaseQualityCheck::query()->where('check', 1)->where('status', 1)->count();
    $all_tasks_checked_percentage = (int) round(($all_tasks_checked / $all_tasks) * 100, 0);

    $all_frontend_tasks = BaseQualityCheck::query()->where('group', 1)->where('status', 1)->count();
    $all_frontend_tasks_checked = BaseQualityCheck::query()->where('group', 1)->where('check', 1)->where('status', 1)->count();
    $frontend_tasks_checked_percentage = (int) round(($all_frontend_tasks_checked / $all_frontend_tasks) * 100, 0);

    $all_backend_tasks = BaseQualityCheck::query()->where('group', 2)->where('status', 1)->count();
    $all_backend_tasks_checked = BaseQualityCheck::query()->where('group', 2)->where('check', 1)->where('status', 1)->count();
    $backend_tasks_checked_percentage = (int) round(($all_backend_tasks_checked / $all_backend_tasks) * 100, 0);

    $all_live_tasks = BaseQualityCheck::query()->where('group', 3)->where('status', 1)->count();
    $all_live_tasks_checked = BaseQualityCheck::query()->where('group', 3)->where('check', 1)->where('status', 1)->count();
    $live_tasks_checked_percentage = (int) round(($all_live_tasks_checked / $all_live_tasks) * 100, 0);

    $color = getColorByPercentage($all_tasks_checked_percentage);
    $color_frontend = getColorByPercentage($frontend_tasks_checked_percentage);
    $color_backend = getColorByPercentage($backend_tasks_checked_percentage);
    $color_live = getColorByPercentage($live_tasks_checked_percentage);

    $script = "<script>
		document.addEventListener('DOMContentLoaded', function() {
			var element = document.querySelector('#rex-navi-page-base-quality-check a');
			var newText = document.createElement('span');
			newText.style.backgroundColor = '{$color}';
			newText.style.color = '#000';
			newText.innerHTML = ' {$all_tasks_checked_percentage}%';
			element.appendChild(newText);
		});
	";

    if (isset($_GET['page']) && is_string($_GET['page']) && preg_match('/base_quality_check/', $_GET['page'])) {
        $script .= "
		document.addEventListener('DOMContentLoaded', function() {
			var element = document.querySelector('#frontendcount');
			var newText = document.createElement('span');
			newText.style.backgroundColor = '{$color_frontend}';
			newText.style.color = '#000';
			newText.innerHTML = '{$all_frontend_tasks_checked} | {$all_frontend_tasks}';
			element.appendChild(newText);

			var element = document.querySelector('#backendcount');
			var newText = document.createElement('span');
			newText.style.backgroundColor = '{$color_backend}';
			newText.style.color = '#000';
			newText.innerHTML = '{$all_backend_tasks_checked} | {$all_backend_tasks}';
			element.appendChild(newText);

			var element = document.querySelector('#livecount');
			var newText = document.createElement('span');
			newText.style.backgroundColor = '{$color_live}';
			newText.style.color = '#000';
			newText.innerHTML = '{$all_live_tasks_checked} | {$all_live_tasks}';
			element.appendChild(newText);
		});
		";
    }

    $script .= '
	</script>';

    return $script;
}

function getColorByPercentage($percentage)
{
    $color = '#FF0000';

    if ($percentage >= 0 && $percentage < 25) {
        $color = '#FF0000';
    } elseif ($percentage < 50) {
        $color = '#EE4420';
    } elseif ($percentage < 75) {
        $color = '#DD8850';
    } elseif ($percentage < 99) {
        $color = '#ACCE40';
    } elseif (100 == $percentage) {
        $color = '#49AD50';
    }

    return $color;
}

rex_extension::register('PACKAGES_INCLUDED', static function () {
    if (rex_addon::get('base_quality_check')->isInstalled()) {
        echo updateTaskscounter();
    }
});
