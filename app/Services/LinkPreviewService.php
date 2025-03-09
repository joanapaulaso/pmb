<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class LinkPreviewService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            'http_errors' => false,
            'verify' => false, // Desativa verificação SSL para desenvolvimento
        ]);
    }

    /**
     * Obter preview de um link
     *
     * @param string $url
     * @return array
     */
    public function getPreview($url)
    {
        try {
            Log::info('Tentando obter preview para URL: ' . $url);

            // Verifica se a URL parece válida
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::warning("URL inválida: $url");
                return $this->getDefaultPreview($url);
            }

            // Tentar fazer a requisição
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                ],
            ]);

            // Se a resposta não for 200, retornar preview padrão
            if ($response->getStatusCode() !== 200) {
                Log::warning("Status não 200 para URL: $url, status: " . $response->getStatusCode());
                return $this->getDefaultPreview($url);
            }

            // Obter o conteúdo HTML
            $html = (string) $response->getBody();

            // Se o HTML estiver vazio, retornar preview padrão
            if (empty($html)) {
                Log::warning("HTML vazio para URL: $url");
                return $this->getDefaultPreview($url);
            }

            // Extrair informações do HTML
            return $this->extractInfo($html, $url);
        } catch (ConnectException $e) {
            // Tratar erros de conexão
            Log::warning("Erro de conexão para URL: $url - " . $e->getMessage());
            return $this->getDefaultPreview($url);
        } catch (RequestException $e) {
            // Tratar erros de requisição
            Log::warning("Erro de requisição para URL: $url - " . $e->getMessage());
            return $this->getDefaultPreview($url);
        } catch (GuzzleException $e) {
            // Tratar outros erros do Guzzle
            Log::warning("Erro Guzzle para URL: $url - " . $e->getMessage());
            return $this->getDefaultPreview($url);
        } catch (\Exception $e) {
            // Tratar qualquer outro erro
            Log::error("Erro ao obter preview para URL: $url - " . $e->getMessage());
            return $this->getDefaultPreview($url);
        }
    }

    /**
     * Extrair informações do HTML
     *
     * @param string $html
     * @param string $url
     * @return array
     */
    protected function extractInfo($html, $url)
    {
        // Criar um DOM a partir do HTML
        $doc = new \DOMDocument();

        // Suprimir erros de parsing HTML malformado
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        // Extrair título
        $title = $this->extractTitle($doc) ?: parse_url($url, PHP_URL_HOST);

        // Extrair descrição
        $description = $this->extractDescription($doc) ?: 'Sem descrição disponível';

        // Extrair imagem
        $image = $this->extractImage($doc, $url);

        return [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'image' => $image,
        ];
    }

    /**
     * Extrair o título da página
     *
     * @param \DOMDocument $doc
     * @return string|null
     */
    protected function extractTitle($doc)
    {
        // Tentar extrair de Open Graph
        $ogTitle = $doc->getElementsByTagName('meta');
        foreach ($ogTitle as $tag) {
            if ($tag->getAttribute('property') == 'og:title') {
                return $tag->getAttribute('content');
            }
        }

        // Tentar extrair do título HTML
        $titleTags = $doc->getElementsByTagName('title');
        if ($titleTags->length > 0) {
            return $titleTags->item(0)->nodeValue;
        }

        // Tentar extrair de H1
        $h1Tags = $doc->getElementsByTagName('h1');
        if ($h1Tags->length > 0) {
            return $h1Tags->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * Extrair a descrição da página
     *
     * @param \DOMDocument $doc
     * @return string|null
     */
    protected function extractDescription($doc)
    {
        // Tentar extrair de Open Graph
        $ogDescription = $doc->getElementsByTagName('meta');
        foreach ($ogDescription as $tag) {
            if ($tag->getAttribute('property') == 'og:description') {
                return $tag->getAttribute('content');
            }
            if ($tag->getAttribute('name') == 'description') {
                return $tag->getAttribute('content');
            }
        }

        return null;
    }

    /**
     * Extrair a imagem da página
     *
     * @param \DOMDocument $doc
     * @param string $url
     * @return string|null
     */
    protected function extractImage($doc, $url)
    {
        // Tentar extrair de Open Graph
        $ogImage = $doc->getElementsByTagName('meta');
        foreach ($ogImage as $tag) {
            if ($tag->getAttribute('property') == 'og:image') {
                $image = $tag->getAttribute('content');
                return $this->resolveUrl($image, $url);
            }
        }

        // Tentar extrair da primeira imagem grande
        $images = $doc->getElementsByTagName('img');
        if ($images->length > 0) {
            foreach ($images as $img) {
                $src = $img->getAttribute('src');
                if ($src) {
                    // Checar se a imagem é suficientemente grande pela presença de atributos width/height
                    $width = $img->getAttribute('width');
                    $height = $img->getAttribute('height');

                    if (($width && $height) && (intval($width) > 100 && intval($height) > 100)) {
                        return $this->resolveUrl($src, $url);
                    }

                    // Se não encontrou atributos, pegar a primeira imagem
                    if ($images->length > 0) {
                        return $this->resolveUrl($images->item(0)->getAttribute('src'), $url);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Resolver URL relativa para absoluta
     *
     * @param string $url
     * @param string $base
     * @return string
     */
    protected function resolveUrl($url, $base)
    {
        if (empty($url)) {
            return null;
        }

        // Verificar se já é uma URL absoluta
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        // Resolver URL relativa
        if (strpos($url, '//') === 0) {
            // URL sem protocolo
            $baseScheme = parse_url($base, PHP_URL_SCHEME);
            return $baseScheme . ':' . $url;
        } elseif (strpos($url, '/') === 0) {
            // URL relativa à raiz
            $parsedBase = parse_url($base);
            $scheme = isset($parsedBase['scheme']) ? $parsedBase['scheme'] : 'http';
            $host = isset($parsedBase['host']) ? $parsedBase['host'] : '';
            return "$scheme://$host$url";
        } else {
            // URL relativa ao caminho atual
            $baseDir = dirname($base);
            return $baseDir . '/' . $url;
        }
    }

    /**
     * Obter preview padrão para uma URL
     *
     * @param string $url
     * @return array
     */
    protected function getDefaultPreview($url)
    {
        $domain = parse_url($url, PHP_URL_HOST);

        return [
            'url' => $url,
            'title' => $domain ?: 'Link',
            'description' => 'Clique para visitar este link',
            'image' => null
        ];
    }
}
