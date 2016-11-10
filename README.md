# open-graph
주어진 사이트의 open graph를 가져옵니다.

## 설치
composer require visualplus/open-graph

## 사용

```
$url = 'http://example.com/foo/bar';
$openGraphParser = new \Visualplus\OpenGraph\OpenGraphParser();

$openGraphTags = $openGraphParser->parse($url);
```