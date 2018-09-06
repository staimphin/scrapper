Scrapper

Author: Gregory Staimphin
Mail: gregory.staimphin@gmail.com
言語:PHP

実装内容の説明
ホームページ から記事一覧の json を作成しています。

1) HTML タグ (div, ul, section ...) と　ID ( ID 又は class 名 )からHTML解析をしています。
2) 'target_wrapper settings'のタグで囲んでいる記事一覧のテキストを分析しています。記事リストを作ります。
3)  'search'のパラメータを記事リストに検索しています。
合ってる場合は SEARCH_KEY => MATCHING_CONTENT　でデータを保存します、
4) 結果 JSON になります。 
[
{ SEARCH_KEY1: MATCHING_CONTENT, SEARCH_KEY2: MATCHING_CONTENT,..,  SEARCH_KEYn => MATCHING_CONTENT},
{ SEARCH_KEY1: MATCHING_CONTENT, SEARCH_KEY2: MATCHING_CONTENT,..,  SEARCH_KEYn => MATCHING_CONTENT}
]


実行方法

プロジェクト設定：
とりあえず「class_scrapper」をインクルードしてください。

設定のフォーマットは：
$args = array(
 'URL' => '',// ソース URL
 'wrapper' => 
	array(
		'tag' => '',//記事一覧を囲んでいるHTMLタグ: 例 section / div / ul
		'identifier' => '' //  ID 又は class 名 
	),
 'target_wrapper' => '',//記事囲んでいるHTMLタグ: 例 dl / li 
 'search' => //アレ:　検索しているキーは JSONの結果になります。キーを追加出来ます。
	array(
		'date' => '', // 日付パターン　YYYY.?MM.?DD を　検索します。
		'label' => '',/ラベルパターン: 「"」で囲んでいるデータ　を　検索します。
		'url' => '',// URL　パターン を　検索します。
		'description' => '',// デフォルトパターン: タグで囲んでいるデータ　を　検索します。
		//  追加されたキーの検索パターンはデフォルトパターンになります。
	),
);


新しい「scrapper」を立ち上げ：

new scrapper($args);

又は 
$scrapper = new scrapper();
$scrapper->setUp($arg);


Results:

the Json data are retrieved by using

$scrapper->retrieveData();
in order to display the data you should echo the result.

