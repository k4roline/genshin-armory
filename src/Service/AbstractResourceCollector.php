<?php

namespace App\Service;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Downloads collectibles from
 */
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
     * Checks if a collectible with given id exists. If so, returns its path, otherwise calls collect().
     *
     * @param string $id id of the collectible.
     * @return string|null path to the collectible or null if collectible with given id could not be found.
     *
     * @see NamecardCollector::collect()
     */
    public function get(string $id): ?string {
        $collectible = $this->getCollectible($id, false);
        if ($this->filesystem->exists($collectible)) {
            return $this->getCollectible($id);
        }
        return $this->collect($id);
    }

    /**
     * Reads .json file for a collectible with given id, and fetches it from external API.
     *
     * @param string $id id of the collectible to fetch.
     * @return string|null path to the collectible or null if collectible with given id could not be found.
     */
    public function collect(string $id): ?string {
        $resource = $this->assets->getUrl("res/$this->resourceName.json");
        if (!$this->filesystem->exists($resource)) {
            return null;
        }

        $file = $this->filesystem->readFile($resource);
        $json = json_decode($file, true);
        $collectible = $this->getCollectible($id, false);
        $resource = $_ENV['ENKANETWORK_API'] . '/' . reset($json[$id]) . $this->fileExtension;
        if (!$this->resourceDownloader->download($resource, $collectible)) {
            return null;
        }
        return $this->getCollectible($id);
    }

    /**
     * Returns url of the given collectible. If $url is set to false, a string with no leading "/" is returned,
     * making the path usable by filesystem.
     *
     * @param string $id id of the collectible.
     * @param bool $url indicates whether returned string should be a path or an url.
     * @return string url to the collectible.
     */
    private function getCollectible(string $id, bool $url = true): string {
        $prefix = $url ? '/' : '';
        return $this->assets->getUrl("{$prefix}images/{$this->resourceName}/{$id}{$this->fileExtension}");
    }
}
