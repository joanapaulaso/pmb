<?php

// app/Services/LinkPreviewService.php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RetryMiddleware;

class LinkPreviewService
{
    public function getPreview(string $url): array
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(Middleware::retry(
            function ($retries, RequestInterface $request, ResponseInterface $response = null, RequestException $exception = null) {
                // Retry up to 3 times
                if ($retries >= 3) {
                    return false;
                }
                // Retry on connection exceptions or 5xx responses
                if ($exception instanceof RequestException || ($response && $response->getStatusCode() >= 500)) {
                    return true;
                }
                return false;
            },
            function ($retries) {
                return 1000 * $retries; // Retry delay (milliseconds)
            }
        ));

        $client = new Client(['handler' => $handlerStack]);

        try {
            $response = $client->request('GET', $url);
            $html = (string) $response->getBody();

            $crawler = new Crawler($html);

            // Check if the title exists
            $titleNode = $crawler->filter('title')->first();
            $title = $titleNode->count() ? $titleNode->text() : '';

            // Check if the meta description exists
            $descriptionNode = $crawler->filter('meta[name="description"]');
            $description = $descriptionNode->count() ? $descriptionNode->attr('content') : '';

            // Check if the og:image meta tag exists
            $imageNode = $crawler->filter('meta[property="og:image"]');
            $image = $imageNode->count() ? $imageNode->attr('content') : '';

            // Logging for debugging
            if (!$title && !$description && !$image) {
                Log::error("No metadata found for URL: $url");
            } else {
                Log::info("Metadata found for URL: $url", [
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                ]);
            }

            return [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'url' => $url,
            ];
        } catch (RequestException $e) {
            Log::error("RequestException for URL: $url", ['message' => $e->getMessage()]);
            return [
                'title' => '',
                'description' => '',
                'image' => '',
                'url' => $url,
            ];
        } catch (\Exception $e) {
            Log::error("General exception for URL: $url", ['message' => $e->getMessage()]);
            return [
                'title' => '',
                'description' => '',
                'image' => '',
                'url' => $url,
            ];
        }
    }
}
