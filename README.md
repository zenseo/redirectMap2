RedirectMap2
============
Вторая версия известного плагина [RedirectMap под MODX Evolution](http://community.modx-cms.ru/blog/addons/1130.html)

Зависимости
============
* [Библиотека MODxAPI](https://github.com/AgelxNash/resourse)
* [Сниппет DocLister](https://github.com/AgelxNash/DocLister)
* [Плагин getPageID](https://gist.github.com/AgelxNash/9268660)
* [jQuery виджет jeditable](https://github.com/tuupola/jquery_jeditable)
* [jQuery виджет FileAPI](https://github.com/RubaXa/jquery.fileapi)

Скачивание проекта
============
* **Вариант 1** с обновлением сниппета DocLister, плагина getPageID и библиотеки MODxAPI до последней актуальной версии:
```
git clone https://github.com/AgelxNash/redirectMap2.git
cd redirectMap2
git submodule update --init --recursive
```
После чего содержимое папки redirectMap2 заливается в корень сайта с перезаписью всех существующих файлов
* **Вариант 2** скачивание модуля архивом из раздела релизов со всеми зависимостями (*на сайте могут быть установлены более актуальные версии сниппета DocLister, плагина getPageID и библиотеки MODxAPI*).

Установка
============
* В базе данных создается таблица redirect_map. SQL запросы для создания таблицы находятся в файле db.sql
* Создается плагин getPageID на событиях OnPageNotFound и OnWebPageInit с кодом
```php
include MODX_BASE_PATH."assets/plugins/getPageID/getPageID.plugin.php";
```
* К плагину getPageID добавляется строка конфигурации 
```
&requestName=Имя GET переменной;input;getPageId
```
* Создается сниппет getPageID с кодом 
```php
return require MODX_BASE_PATH.'assets/plugins/getPageID/getPageID.snippet.php';
```
* К сниппету getPageID добавляется стркоа конфигурации
```
&requestName=Имя GET переменной;input;getPageId
```
* Создается сниппет DocLister с кодом
```php
return require MODX_BASE_PATH.'assets/snippets/DocLister/snippet.DocLister.php';
```
* Создается плагин RedirectMap на событии OnPageNotFound с кодом
```php
include MODX_BASE_PATH."assets/modules/RedirectMap/plugin.RedirectMap.php";
```
* К плагину RedirectMap добавляется строка конфигурации
```
&saveParams=Сохранять GET параметры при редиректе;list;true,false;true &findWithParams=Искать правила с учетом GET параметров;list;true,false;false
```
* Создается модуль RedirectMap с кодом
```php
include MODX_BASE_PATH."assets/modules/RedirectMap/init.php";
```
* К модулю RedirectMap добавляется строка конфигурации:
```
&display=Правил на странице;input;20 &requestName=Имя GET переменной;input;getPageId
```