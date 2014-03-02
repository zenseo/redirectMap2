RedirectMap2
============
Вторая версия известного плагина [RedirectMap под MODX Evolution](http://community.modx-cms.ru/blog/addons/1130.html)
<a href="http://take.ms/LQgjX"><img src="https://monosnap.com/image/34bHO3aPnVEgWslmqshhwhUGoh3QD6.png"></a>
* Быстрое отключение правил перенаправления
* Выборка правил перенаправления по целевой странице и активности
* Сортировка по любому полю и выборка правл с использованием Ajax
* InLine редактирование данных прямо в таблице
* Полное соответствие дизайну MODX Evolution
* Проверка на доступность добавляемого URI (*Допустим в системе уже имеется страница доступная по адресу указанному в поле uri. Модуль автоматически определит эту страницу и подставит и не даст создать не рабочее правило*)
* Возможность загрузки правил из CSV файлов
* Осуществелине перенаправление с сохранением/сбросом GET параметров
* Поиск правил для перенаправления с учетом/без учета GET параметров

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
* Проверяется, чтобы значение параметра requestName из строки конфигурации совпадала в модуле, плагине и сниппете

Вывод правил на страницу редактирования документа
============
При желании можно вывести список правил на страницу редактирования документа. Для этого необходимо 
создать ТВ параметр типа **Custom Input** с возможными значениями:
```php
@INCLUDE: assets/modules/RedirectMap/tv.RedirectMap.php
```
После чего во время редактирования документа будет выведен список правил и кнопка быстрого перехода в модуль для управления правилами редиректов этой страницы:
<img src="https://monosnap.com/image/3tSiIW50ZHsj5BPZPbExotWKMkpEwJ.png">
<img src="https://monosnap.com/image/vtpDbsyfICjHfFxU7hdMEEC6iXQ25b.png">
<img src="https://monosnap.com/image/0OQ2d6XUHeypSZBMSF5737FUGGtEP8.png">

Если же запуск модуля не доступен для менеджера, то кнопка отображаться не будет (*хотя список правил будет по прежнему выведен*):
<img src="https://monosnap.com/image/F015Vp2Zds8gSzDN0Kahx2QdHOl9qG.png">