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

$string['studentdisclosuredefault']  ='所有上传的文件都会被提交到抄袭检测系统';
// strings used for help information
$string['studentdisclosure'] = '显示给学生的声明';
$string['studentdisclosure_help'] = '这些文字会在文件上传页面显示给所有学生。';
$string['grammarsize'] = '文法长度';
$string['grammarsize_help'] = '文法长度是用来计算文档指纹中一个哈希的文本长度。建议值是30。';
$string['windowsize'] = '窗口大小';
$string['windowsize_help'] = '窗口大小表示如何划分搜索技术的粒度。建议值是60。';
$string['colour'] = '颜色';
$string['colour_help'] = '用这些颜色在一对一的文档对比中突出显示相似的文本片段。不过，目前只支持一种颜色（#FF0000）。';
$string['clusterdistance'] = '文本簇哈希之间的最大距离';
$string['clusterdistance_help'] = '此参数用在给相似文本片段着色时，如何把哈希分布为文本簇。建议值是100。';
$string['clustersize'] = '最小的簇大小';
$string['clustersize_help'] = '着色相似文本片段时，一个文本簇至少要包含几个哈希。建议值是2.';
$string['defaultthreshold'] = '缺省阈值';
$string['defaultthreshold_help'] = '相似度小于阈值的作业不会显示在“反抄袭-作业”页面。';
$string['globalsearchthreshold'] = '全局搜索阈值';
$string['globalsearchthreshold_help'] = '为将来的开发预留。建议值是90。';
$string['MSlivekey'] = 'MS Application ID key';
$string['MSlivekey_help'] = '您必须从微软获得一个MS Application ID key才能使用全局搜索功能。';
$string['globalsearchquerysize'] = '全局搜索查询长度';
$string['globalsearchquerysize_help'] = '这是全局搜索查询时使用的关键词数。建议值是7。';
$string['percentageofsearchqueries'] = 'Web搜索时查询百分比';
$string['percentageofsearchqueries_help'] = '这是从所有Web搜索查询中随机选择查询的百分比。建议值是40。';
$string['numberofwebdocuments'] = '下载web文档个数';
$string['numberofwebdocuments_help'] = '在可能的web抄袭源列表中下载多少个web文档。建议值是10。';
$string['cultureinfo'] = '全局搜索的文化信息';
$string['cultureinfo_help'] = 'Bing搜索引擎的查询中使用的文化信息。';
$string['maxfilesize'] = '文件最大尺寸';
$string['maxfilesize_help'] = '大小超过此值的文件不会从internet下载。建议值是1000000。';
$string['cleantables'] = '清理表';
$string['cleantables_help'] = '删除作业集以外的所有Crot数据，为后面的检查做准备！重新计算指纹可能会对服务器带来高负荷。';

$string['newexplain'] = '关于此插件的更多信息请看：';
$string['crot'] = 'Crot反抄袭';
$string['crot_help'] = 'Crot是一个支持doc、docx、pdf、odt、rtf、txt、cpp和java文件的反抄袭工具。';
$string['crotexplain'] = 'Crot是一个反抄袭工具：它能比较同一机构的学生之间的作业拷贝，也能比较来自Internet的拷贝。它使用文档指纹技术处理内部文档比较，使用MSN Live搜索引擎处理全局搜索。<br /><br />更多信息在<a href="http://www.crotsoftware.com">www.crotsoftware.com</a>';

$string['usecrot'] ='启用Crot';
$string['savedconfigsuccess'] = '抄袭设置已保存';
$string['compareinternet'] = '将提交的文件在Internet中比较';
$string['comparestudents'] = '将提交的文件在学生间比较';

//strings copied from block - probably need reworking for new plugin
$string['block_name'] = '反抄袭';
$string['course_summary'] = '课程简介';
$string['Topics'] = '主题';
$string['report'] = '报告';
//$string['settings'] = '设置';
$string['have_to_be_a_teacher'] = '<c>此版块正在建设中。<br>只有教师才能看到内容';
$string['assignments']='作业';
$string['local']='本地';
$string['global']='全局';
$string['settings']='设置';
$string['settings_cancelled']='反抄袭设置已取消';
$string['settings_saved']='反抄袭设置已成功保存';
$string['save']='保存';
$string['select_assignment']='选择此作业';
$string['student_name']='学生姓名';
$string['similar']='相似作业';
//$string['default_threshold']='缺省阈值';
$string['grammar_size']='文法长度';
$string['colours']='颜色';
$string['window_size']='窗口大小';
$string['cluster_distance'] = '文本簇哈希之间的最大距离';
$string['cluster_size'] = '最小的簇大小';
$string['global_search_threshold']='全局搜索阈值';
$string['default_threshold']='缺省阈值';
$string['global_search_settings']='<b>全局搜索设置</b>';
//$string['global_search_threshold']='全局搜索阈值';
$string['MS_live_key']='MS Application ID key';
$string['global_search_query_size']='全局搜索查询长度';
$string['percentage_of_search_queries']='Web搜索时查询百分比 (1-100)';
$string['number_of_web_documents']='下载Web文档个数';
$string['clean_tables']='清理表 (<b>警告！请阅读帮助！</b>)';
$string['culture_info']='全局搜索的文化信息';
$string['antiword_path']='antiword的路径<br/>如果您不想使用antiword处理MS-Word文件，就留空';
$string['tools']='<b>工具</b>';
$string['test_global_serach']='勾选以快速测试全局搜索';
$string['registration'] = '<b>注册</b>';
$string['max_file_size'] = '文件最大尺寸';

$string['no_similarities'] = '没有相似的';
$string['incorrect_courseid'] = '课程ID不正确';
$string['incorrect_courseAid'] = '课程A的ID不正确';
$string['incorrect_courseBid'] = '课程B的ID不正确';
$string['incorrect_docAid'] = '文档A的ID不正确';
$string['incorrect_docBid'] = '文档B的ID不正确';
$string['incorrect_fileAid'] = '文件A的ID不正确';
$string['incorrect_fileBid'] = '文件B的ID不正确';
$string['incorrect_submAid'] = '提交A的ID不正确';
$string['incorrect_submBid'] = '提交B的ID不正确';
$string['incorrect_assignmentAid'] = '作业A的ID不正确';
$string['incorrect_assignmentBid'] = '作业B的ID不正确';
$string['tables_cleaned_up'] = 'Crot表清理结束！';

$string['col_name'] = '姓名';
$string['col_course'] = '课程';
$string['col_similarity_score'] = '相似度';
$string['file_was_not_found'] = '找不到本地文件。它好像已经从系统删除';
$string['course_not_applicable'] = '不可使用';
$string['no_plagiarism'] = '没有发现抄袭，或者还没有检查';
$string['name_unknown'] = '未知姓名';
$string['webdoc'] = 'Web文档：<br> 源头：';
$string['webdocument'] = 'Web文档';
$string['bing_search'] = '<br>全局抄袭检测由Bing搜索引擎支持';
$string['assignments_not_displayed'] = '相似度小于{$a}%的作业不会显示';

$string['download_inwicast_message'] = '<p>INWICAST Publisher是一个非常易用、强大的工具。它可以用来面向web和移动设备，录制和发布音频、视频、屏幕和幻灯片。在INWICAST Publisher中您可以：</p><p>

    * 录制音频、视频、屏幕和Powerpoint幻灯片<br/>
    * 将录制的媒体转为各种格式：flv、wmv、mp4、mp3等等<br/>
    * 为iPod或Zune等移动播放器创建多媒体内容<br/>
    * 创建和管理多媒体播放列表<br/>
    * 方便地将您的博客发布到您的Moodle平台<br/>
</p>';

$string['FILE_FORMAT_NOT_ALLOWED'] = "很抱歉，不允许上传这种文件格式";

