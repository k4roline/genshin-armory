<?php

namespace App\Service;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractResourceCollector {
    protected Filesystem $filesystem;
    protected Package $assets;
    public function __construct(
        protected string $fileExtension,
        protected string $resourceName,
        protected readonly HttpClientInterface $httpClient,
        protected readonly ResourceDownloader $resourceDownloader
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
        $collectible = $this->getCollectible($id);
        if ($this->filesystem->exists($collectible)) {
            return "/$collectible";
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
        $resource = $_ENV['ENKANETWORK_API'] . '/' . reset($json[$id]) . $this->fileExtension;
        if (!$this->resourceDownloader->download($resource, $collectible)) {
            return null;
        }
        return "/$collectible";
    }
    
    private function getCollectible(string $id): string {
        return $this->assets->getUrl("images/{$this->resourceName}/{$id}{$this->fileExtension}");
    }
}
