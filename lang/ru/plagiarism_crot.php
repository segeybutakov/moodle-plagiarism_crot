<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   plagiarism_crot
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['studentdisclosuredefault']  = 'Все загруженные файлы будут отправлены в модуль проверки на плагиат';
// strings used for help information
$string['studentdisclosure'] = 'Сообщение студентам';
$string['studentdisclosure_help'] = 'Этот текст будет отображаться у всех студентов на странице загрузки файла';
$string['grammarsize'] = 'Размер грамматики';
$string['grammarsize_help'] = 'Размер грамматики - это размер текста, используемый для расчета одного хэша при получении "отпечатка" документа. Рекомендуемое значение - 30.';
$string['windowsize'] = 'Размер окна';
$string['windowsize_help'] = 'Рекомендуемое значение - 60.';
$string['colour'] = 'Цвета';
$string['colour_help'] = 'Эти цвета используются для выделения одинаковых фрагментов текста при сравнении документов. Но на данный момент функция покраски работает только с одним цветом (#FF0000).';
$string['clusterdistance'] = 'Максимальное расстояние между хэшами в текстовом кластере';
$string['clusterdistance_help'] = 'Этот параметр используется для распределения хэшей по текстовым кластерам при покраске одинаковых текстовых фрагментов. Рекомендуемое значение - 100.';
$string['clustersize'] = 'Минимальный размер кластера';
$string['clustersize_help'] = 'Это минимальное количество хэшей в текстовом кластере, используемое для покраски одинаковых текстовых фрагментов. Рекомендуемое значение - 2.';
$string['defaultthreshold'] = 'Порог по умолчанию';
$string['defaultthreshold_help'] = 'Задания с процентом совпадения меньше чем пороговое значение не отображаются на странице «Антиплагиат – Задания»';
$string['globalsearchthreshold'] = 'Порог для глобального поиска';
$string['globalsearchthreshold_help'] = 'Зарезервировано для будущего развития. Рекомендуемое значение - 90.';
$string['MSlivekey'] = 'MS Application ID key';
$string['MSlivekey_help'] = 'MS Application ID key необходимо получить на сайте Майкрософт, чтобы использовать функции поиска в интернете.';
$string['globalsearchquerysize'] = 'Размер запроса для глобального поиска';
$string['globalsearchquerysize_help'] = 'Это количество слов в запросе для поиска в интернете. Рекомендуемое значение - 7.';
$string['percentageofsearchqueries'] = 'Процент запросов для поиска в интернете';
$string['percentageofsearchqueries_help'] = 'Это процент запросов, выбранных случайным образом из всех поисковых запросов для поиска в интернете. Рекомендуемое значение - 40.';
$string['numberofwebdocuments'] = 'Количество скачиваемых веб-документов';
$string['numberofwebdocuments_help'] = 'Сколько веб-документов будет скачано на Ваш сервер из списка возможных источников в интернете. Рекомендуемое значение - 10.';
$string['cultureinfo'] = 'Языковой стандарт для глобального поиска';
$string['cultureinfo_help'] = 'Языковой стандарт используется в запросах к поисковой системе Bing.';
$string['maxfilesize'] = 'Максимальный размер файла';
$string['maxfilesize_help'] = 'Файлы с размером больше этого значения не скачиваются из интернета. Рекомендуемое значение - 1000000.';
$string['cleantables'] = 'Очистить таблицы';
$string['cleantables_help'] = 'Будут удалены все таблицы Крота за исключением настроек Крота для заданий! Перерасчет "отпечатков" документов может привести к большой нагрузке на сервер.';

$string['newexplain'] = 'For more information on this plugin see: ';
$string['crot'] = 'Крот';
$string['crotexplain'] = 'Крот - модуль проверки на плагиат: он проводит сравнение как файлов, скопированных у студентов этого же учебного заведения, так и файлов, скопированных из интернета. Он использует технологию "отпечатка" документа для сравнения документов и поисковую систему MSN Live для осуществления поиска в интернете.<br/><br/> Более подробную информацию смотрите на <a href="http://www.crotsoftware.com">www.crotsoftware.com</a>';

$string['usecrot'] ='Использовать Крот';
$string['savedconfigsuccess'] = 'Настройки антиплагиата сохранены';
$string['compareinternet'] = 'Сравнивать представленные файлы с файлами из интернета';
$string['comparestudents'] = 'Сравнивать представленные файлы с файлами других студентов';

//strings copied from block - probably need reworking for new plugin
$string['block_name'] = 'Антиплагиат';
$string['course_summary'] = 'Course Summary';
$string['Topics'] = 'Topics';
$string['report'] = 'Report';
//$string['settings'] = 'Settings';
$string['have_to_be_a_teacher'] = '<c>Модуль находится в стадии разработки. <br> Вы должны быть преподавателем, чтобы увидеть его содержание.';
$string['assignments']='Задания';
$string['local']='Local';
$string['global']='Global';
$string['settings']='Настройки';
$string['settings_cancelled']='Настройки антиплагиата были отменены';
$string['settings_saved']='Настройки антиплагиата были успешно сохранены';
$string['save']='Сохранить';
$string['select_assignment']='Select the assignment';
$string['student_name']='ФИО студента';
$string['similar']='Похожие задания';
//$string['default_threshold']='Порог по умолчанию';
$string['grammar_size']='Размер грамматики';
$string['colours']='Цвета';
$string['window_size']='Размер окна';
$string['cluster_distance']='Максимальное расстояние между хэшами в текстовом кластере';
$string['cluster_size']='Минимальный размер кластера';
$string['global_search_threshold']='Порог для глобального поиска';
$string['default_threshold']='Порог по умолчанию';
$string['global_search_settings']='<b>Настройки глобального поиска</b>';
//$string['global_search_threshold']='Global Search Threshold';
$string['MS_live_key']='MS Application ID key';
$string['global_search_query_size']='Размер запроса для глобального поиска';
$string['percentage_of_search_queries']='Процент запросов для поиска в интернете (1-100)';
$string['number_of_web_documents']='Количество скачиваемых веб-документов';
$string['clean_tables']='Очистить таблицы (<b>ПРЕДУПРЕЖДЕНИЕ! Пожалуйста, прочитайте помощь!</b>)';
$string['culture_info']='Языковой стандарт для глобального поиска';
$string['antiword_path']='Path to antiword<br/>Keep it empty if you do not want to use antiword <br/>to process MS-Word submissions';
$string['tools']='<b>Инструменты</b>';
$string['test_global_serach']='Отметьте для выполнения быстрого теста глобального поиска';
$string['registration'] = '<b>Регистрация</b>';
$string['max_file_size'] = 'Максимальный размер файла';

$string['no_similarities'] = 'нет похожих документов';
$string['incorrect_courseid'] = 'Неверный ID курса';
$string['incorrect_courseAid'] = 'Неверный ID курса А';
$string['incorrect_courseBid'] = 'Неверный ID курса B';
$string['incorrect_docAid'] = 'Неверный ID документа А';
$string['incorrect_docBid'] = 'Неверный ID документа B';
$string['incorrect_fileAid'] = 'Неверный ID файла A';
$string['incorrect_fileBid'] = 'Неверный ID файла B';
$string['incorrect_submAid'] = 'Неверный ID отправки документа A';
$string['incorrect_submBid'] = 'Неверный ID отправки документа B';
$string['incorrect_assignmentAid'] = 'Неверный ID задания А';
$string['incorrect_assignmentBid'] = 'Неверный ID задания B';
$string['tables_cleaned_up'] = 'Таблицы Крота были очищены!';

$string['col_name'] = 'Название';
$string['col_course'] = 'Курс';
$string['col_similarity_score'] = 'Процент совпадения';
$string['file_was_not_found'] = 'Невозможно найти локальный файл. Скорее всего он был удален из системы';
$string['course_not_applicable'] = 'не применяется';
$string['no_plagiarism'] = 'плагиат не обнаружен или проверка еще не выполнена';
$string['name_unknown'] = 'имя неизвестно';
$string['webdoc'] = 'Веб-документ: <br> Источник:';
$string['webdocument'] = 'Веб-документ';
$string['bing_search'] = '<br>Глобальный поиск плагиата осуществляется с помощью поисковой системы Bing';
$string['assignments_not_displayed'] = 'Задания с процентом совпадения меньше {$a}% не отображаются';

$string['download_inwicast_message'] = '<p>INWICAST Publisher is a very simple and powerful tool designed to record and publish podcasts, videocasts, screencasts and slidecasts for the web and for mobile devices. INWICAST Publisher allows you to:</p><p>

    * record podcasts, videocasts, screencasts and Powerpoint slidescasts<br/>
    * convert recorded medias to various formats: flv, wmv, mp4, mp3, etc.<br/>
    * create multimedia content for mobile players like iPod or Zune<br/>
    * create and manage multimedia playlists<br/>
    * easily publish your podcasts on your Moodle platform<br/>
</p>';

$string['FILE_FORMAT_NOT_ALLOWED'] = "Sorry but this file format is not allowed for upload";

