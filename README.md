# CustomMenu

Модуль для создания кастомных менюшек в EvolutionCMS (Modx), не привязанных к дереву ресурсов.

Обязательная зависимость — класс DLTemplate (устанавливается c DocLister)

Модуль не имеет инсталлятора.

Для установки нужно

* вручную скопировать файлы модуля.  
* создать в админке модуль CustomMenu и указать в его содержимом:
    ```php
    include_once MODX_BASE_PATH.'assets/modules/CustomMenu/index.php';
    ```
* создать в админке сниппет CM и указать в его содержимом
    ```php
    <?php
    return include MODX_BASE_PATH.'assets/modules/CustomMenu/CM.snippet.php';
    ```

Вызов сниппета (выбор меню можно указать по его идентификатору или имени, имя имеет больший приоритет, если указаны оба параметра):

```
[[CM?
&menuId=`8`
&menuName=`mainmenu`
&tplWrap=`@CODE: <ul>[+wrap+]</ul>`
&tplItem=`@CODE: <li><a href="[+url+]">[+title+]</a>[+wrap+]</li>`
]]
```
