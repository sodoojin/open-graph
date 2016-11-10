<?php

namespace Visualplus\OpenGraph;


use DOMDocument;
use DOMElement;
use DOMXPath;

class OpenGraphParser
{
    /**
     * @var int
     */
    private $recursiveCallMaximumCount = 10;

    /**
     * @param $url
     * @return array
     */
    public function parse($url)
    {
        return $this->getOpenGraphTags($url);
    }

    /**
     * @param $url
     * @param int $callCounter
     * @return array
     */
    private function getOpenGraphTags($url, $callCounter = 0)
    {
        if ($callCounter > $this->recursiveCallMaximumCount) return [];

        $html = file_get_contents($url);
        $html = $this->convertHtmlToUtf8($html);

        $domDocument = new DOMDocument();
        @$domDocument->loadHTML($html);

        $xPath = new DOMXPath($domDocument);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $metaList = $xPath->query($query);

        $openGraphTags = [];

        if ($metaList->length === 0) {
            $urlList = $this->extractUrlListFromDocument($domDocument);
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

            foreach ($urlList as $extractedUrl) {
                if (strpos($extractedUrl, 'http') !== 0) {
                    $extractedUrl = $domain . $extractedUrl;
                }

                $openGraphTags = $this->getOpenGraphTags($extractedUrl, ++ $callCounter);

                if (count($openGraphTags) > 0) {
                    break;
                }
            }
        } else {
            foreach ($metaList as $meta) {
                /** @var DOMElement $meta */
                $property = $meta->getAttribute('property');
                $content = $meta->getAttribute('content');

                $openGraphTags[$property] = $content;
            }
        }

        return $openGraphTags;
    }

    /**
     * @param $html
     * @return string
     */
    private function convertHtmlToUtf8($html)
    {
        $encoding = mb_detect_encoding($html, 'euc-kr, ms949, cp949, utf-8');

        if ($encoding != '' && strtolower($encoding) != 'utf-8') {
            if (strtolower($encoding) !== 'utf-8') {
                $html = iconv($encoding, 'utf-8', $html);
            }
        }

        $html = preg_replace('/(<meta.*charset=)(utf-8|ms949)/i', '${1}utf-8', $html);

        return $html;
    }

    /**
     * @param DOMDocument $domDocument
     * @return array
     */
    private function extractUrlListFromDocument($domDocument)
    {
        $domDocument->getElementsByTagName('html');
        $urlList = [];
        $frames = $domDocument->getElementsByTagName('frame');
        $iframes = $domDocument->getElementsByTagName('iframe');

        foreach ($frames as $frame) {
            /** @var DOMElement $frame */
            $src = $frame->getAttribute('src');

            if ($src) {
                $urlList[] = $src;
            }
        }

        foreach ($iframes as $iframe) {
            /** @var DOMElement $iframe */
            $src = $iframe->getAttribute('src');

            if ($src) {
                $urlList[] = $src;
            }
        }

        return $urlList;
    }
}