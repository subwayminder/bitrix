<?
$MESS['WDU_GENERAL'] = 'Общие настройки';
	$MESS['WDU_GLOBAL_MAIN_FUNCTIONS'] = 'Простые функции отладки <code>P()</code> и <code>L()</code> для <code>PHP</code>';
		$MESS['WDU_GLOBAL_MAIN_FUNCTIONS_HINT'] = 'Опция активирует дополнительные специальные функции PHP: P (отладочный вывод), L (логирование).<br/><br/>
		<b>Отладочный вывод</b><br/>
		<code>P($myVar);</code><br/><br/>
		<b>Логирование</b><br/>
		<code>L($myVar, $_SERVER[\'DOCUMENT_ROOT\'].\'/log.txt\');</code><br/>
		Второй аргумент - имя файла (абсолютное). Если имя файла не задано, то имя файла берется из константы LOG_FILENAME. Если же эта константа не задана, то логирование будет в файл «!log_*.txt» в корне сайта.';
	$MESS['WDU_JS_DEBUG_FUNCTIONS'] = 'Отладочные функции JS (<code>var_dump()</code>, <code>var_dump()</code>, и <code>log()</code>)';
		$MESS['WDU_JS_DEBUG_FUNCTIONS_HINT'] = 'Опция активирует дополнительную специальную функции для JavaScript, которые помогают анализировать содержимое объектов и простых переменных.<br/><br/>
		<code>
		&lt;script&gt;<br/>
		log(var_dump_ex(location));<br/>
		&lt;/script&gt;
		</code><br/><br/>
		Данный функционал был реализовано достаточно давно, и с учетом современных возможностей отладки в браузерах, может быть полезным только в редких случаях.';
	$MESS['WDU_SET_ADMIN_FAVICON'] = 'Устанавливать в админке собственную favicon';
		$MESS['WDU_SET_ADMIN_FAVICON_HINT'] = 'Опция позволяет установить для админинстративного раздела собственную иконку сайта. Может быть полезно когда на сайте favicon.ico находится не в корне сайта. Или для более удобного ориентирования в большом количестве вкладок браузера.';
	$MESS['WDU_ADMIN_FAVICON'] = 'Административная favicon';
		$MESS['WDU_ADMIN_FAVICON_HINT'] = 'Здесь необходимо указать путь к файлу favicon.ico относительно корня сайта.';
	$MESS['WDU_PAGEPROPS_ENABLED'] = 'Включить удобные свойства страниц/разделов';
		$MESS['WDU_PAGEPROPS_ENABLED_HINT'] = 'Опция включает функционал удобных свойств.<br/><br/>Удобные свойства настраиваются в настройках модуля «Управления структурой» (в поле «Типы свойств» создается колонка с кнопкой, где для каждого свойства можно определить параметры), и действует при настройке этих свойств для страниц и разделов - например, если на любой странице сайта нажать «Изменить страницу» (подменю) - «Заголовок и свойства страницы».';
	$MESS['WDU_PREVENT_LOGOUT'] = 'Подтверждение выхода (кнопка «Выйти» на панели)';
		$MESS['WDU_PREVENT_LOGOUT_HINT'] = 'Данная опция может быть весьма полезной для тех, кто случайно нажимает «Выйти» на административной панели.<br/><br/><b>Внимание!</b> Опция работает отдельно для каждого администратора.';

$MESS['WDU_IBLOCK'] = 'Полезности для инфоблоков';
	$MESS['WDU_IBLOCK_ADD_DETAIL_LINK'] = 'Добавить кнопку просмотра товара со страницы редактирования';
		$MESS['WDU_IBLOCK_ADD_DETAIL_LINK_HINT'] = 'Опция добавляет на страницу редактирования товара кнопку «Просмотр на сайте» (как подменю кнопки «Действия»), которая ведет на страницу сайта с карточкой товара.';
	$MESS['WDU_IBLOCK_SHOW_ELEMENT_ID'] = 'Показывать ID элемента в форме редактирования';
		$MESS['WDU_IBLOCK_SHOW_ELEMENT_ID_HINT'] = 'Опция позволяет вывести ID товара в форме редактирования (на панель с кнопками «Сохранить», «Применить», «Отменить»).<br/><br/>
		Работает в т.ч. в popup-окне редактирования товара.';
	$MESS['WDU_IBLOCK_RENAME_COLUMNS'] = 'Включить возможность переименовывания столбцов в списках';
		$MESS['WDU_IBLOCK_RENAME_COLUMNS_HINT'] = 'Опция позволяет переименовывать колонки в стандартных списках административного раздела Битрикс.<br/><br/>
		В связи с переходом Битрикс на новый тип списков (UI), данная опция становится менее актуальной, т.к. на новом типе это не работает (но там есть штатная система переименования столбцов).';

$MESS['WDU_FASTSQL'] = 'Быстрые SQL-запросы';
	$MESS['WDU_FASTSQL_ENABLED'] = 'Включить быстрые запросы';
		$MESS['WDU_FASTSQL_ENABLED_HINT'] = 'Данная опция позволяет для <a href="/bitrix/admin/sql.php?lang='.LANGUAGE_ID.'" target="_blank">страницы выполнения SQL-запросов</a> создавать быстрые запросы, выполняемые одним кликом.<br/><br/>Управление быстрыми запросами (добавление, редактирование, удаление) производится <a href="/bitrix/admin/wdu_fastsql_list.php?lang='.LANGUAGE_ID.'" target="_blank">здесь</a>.';
	$MESS['WDU_FASTSQL_AUTO_EXEC'] = 'Авто-выполнение запроса';
		$MESS['WDU_FASTSQL_AUTO_EXEC_HINT'] = 'Данная опция позволяет указать, как будет выполняться SQL-запрос при клике по нему.';
		$MESS['WDU_FASTSQL_AUTO_EXEC_N'] = 'нет';
		$MESS['WDU_FASTSQL_AUTO_EXEC_Y'] = 'да, с подтверждением';
		$MESS['WDU_FASTSQL_AUTO_EXEC_X'] = 'да, без подтверждения';

$MESS['WDU_HEADERS'] = 'Управление заголовками ответа сервера';
	$MESS['WDU_HEADERS_ADD'] = 'Добавить заголовки';
		$MESS['WDU_HEADERS_ADD_HINT'] = 'Данная опция позволяет добавить произвольные заголовки ответа страницы.<br/><br/>Используется стандартная PHP-функция header().<br/><br/>Имейте ввиду, что стандартные заголовки (напр., Server, Date, Connection и др.) не могут быть переопределены.';
		$MESS['WDU_HEADERS_ADD_PLACEHOLDER'] = 'Например, X-Powered-By: PHP/8.0.0';
	$MESS['WDU_HEADERS_REMOVE'] = 'Удалить заголовки';
		$MESS['WDU_HEADERS_REMOVE_HINT'] = 'Данная опция позволяет удалить некоторые заголовки из ответа страницы.<br/><br/>Имейте ввиду, что могут быть удалены только те заголовки, которые были выставлены PHP и (или) Битриксом. Заголовки, устанавливаемые веб-сервером, или nginx, удалены не могут быть, например: Server, Date, Connection и др.<br/><br/>Однако некоторые из них могут быть переопределены: Content-Type, Accept-Language, Accept-Encoding, Cache-Control и др.';
		$MESS['WDU_HEADERS_REMOVE_PLACEHOLDER'] = 'Например, Expires';
	$MESS['WDU_HEADERS_ITEM_ADD'] = 'Добавить';
	$MESS['WDU_HEADERS_ITEM_DELETE'] = 'Удалить';

$MESS['WDU_MISC'] = 'Другие настройки';

$MESS['WDU_TAB_GENERAL_NAME'] = 'Общие настройки';
$MESS['WDU_TAB_GENERAL_DESC'] = 'Редактирование общих настроек модуля';
$MESS['WDU_TAB_RIGHTS_NAME'] = 'Доступ';
$MESS['WDU_TAB_RIGHTS_DESC'] = 'Уровень доступа к модулю';
?>