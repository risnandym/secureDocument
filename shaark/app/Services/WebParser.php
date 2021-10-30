<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class WebParser
{
    /** @var string $url */
    public $url;
    /** @var Crawler $crawler */
    public $crawler;
    /** @var string $title */
    public $title = '';
    /** @var string $content */
    public $content = '';

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public static function parse(string $url): self
    {
        /** @todo It's not really... hmmmm */
        if (app()->environment('testing')) {
            $parser = new static($url);
            $parser->title = $url;
            $parser->content = 'Web parser content';
            return $parser;
        }

        return (new static($url))
            ->fetchContent()
            ->grepInfos();
    }

    public static function parseTesting(string $url): array
    {
        return [
            'title' => $url,
            'content' => 'Web parse content',
        ];
    }

    public function fetchContent(): self
    {
        try {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, trim($this->url));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Accept: text/*; application/*',
            ]);

            $content = curl_exec($curl);
            $this->crawler = new Crawler($content);

            curl_close($curl);
        } catch (\Exception $e) {
            unset($e);
        }

        return $this;
    }

    public function grepInfos(): self
    {
        try {
            if (! empty($this->crawler)
                && ! empty($this->crawler->html())) {
                $this->grepTitle();
                $this->grepContent();
            }
        } catch (\Exception $e) {
            unset($e);
        }

        return $this;
    }

    private function grepTitle(): ?string
    {
        try {
            $this->title = $this->crawler->filterXPath('//title')->text();
            return $this->title;
        } catch (\Exception $e) {
            unset($e);
        }

        try {
            $this->title = $this->crawler->filterXPath('//meta[@property="og:title"]')->attr('content');
            return $this->title;
        } catch (\Exception $e) {
            unset($e);
        }

        try {
            $this->title = $this->crawler->filterXPath('//meta[@name="twitter:title"]')->attr('content');
            return $this->title;
        } catch (\Exception $e) {
            unset($e);
        }

        return null;
    }

    private function grepContent(): ?string
    {
        try {
            $this->content = $this->crawler->filterXPath('//meta[@name="description"]')->attr('content');
            return $this->content;
        } catch (\Exception $e) {
            unset($e);
        }

        try {
            $this->content = $this->crawler->filterXPath('//meta[@property="og:description"]')->attr('content');
            return $this->content;
        } catch (\Exception $e) {
            unset($e);
        }

        try {
            $this->content = $this->crawler->filterXPath('//meta[@name="twitter:description"]')->attr('content');
            return $this->content;
        } catch (\Exception $e) {
            unset($e);
        }

        return null;
    }
}
