<?php

namespace App\Service;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractResourceCollector {
    protected Filesystem $filesystem;
    protected Package $assets;
    public function __construct(
        protected string $fileExtension,
        protected string $resourceName,
        protected HttpClientInterface $httpClient
    ) {
        $this->filesystem = new Filesystem();
        $this->assets = new Package(new EmptyVersionStrategy());
    }

    /**
     * Checks if a resource with given id exists. If so, returns its path, otherwise calls collect()
     *
     * @param string $id id of the resource
     * @return string path to the resource
     *
     * @see NamecardCollector::collect()
     */
    public function get(string $id): string {
        $collectible = $this->getCollectible($id, '/');
        if ($this->filesystem->exists($collectible)) {
            return $collectible;
        }
        return $this->collect($id);
    }

    /**
     * Reads .json file for a resource with given id, and fetches it from external API
     *
     * @param string $id id of the resource to fetch
     * @return string relative path to the imported resource
     */
    public function collect(string $id): string|null {
        $resource = $this->assets->getUrl("res/$this->resourceName.json");
        if (!$this->filesystem->exists($resource)) {
            return null;
        }

        $file = $this->filesystem->readFile($resource);
        $json = json_decode($file, true);
        $collectible = $this->getCollectible($id);
        if (!$this->fetch(reset($json[$id]) . $this->fileExtension, $collectible)) {
            return null;
        }
        return $collectible;
    }

    private function fetch(string $collectible, string $target): bool {
        try {
            $response = $this->httpClient->request('GET', $_ENV['ENKANETWORK_API'] . '/' . $collectible);
            $statusCode = $response->getStatusCode();
            $this->filesystem->dumpFile($this->assets->getUrl($target), $response->getContent());
        } catch (ClientExceptionInterface
        |TransportExceptionInterface
        |ServerExceptionInterface
        |RedirectionExceptionInterface $e) {
            return false;
        }
        return $statusCode < 400;
    }
    
    private function getCollectible(string $id, ?string $prefix = ''): string {
        return $this->assets->getUrl("{$prefix}images/{$this->resourceName}/{$id}{$this->fileExtension}");
    }
}